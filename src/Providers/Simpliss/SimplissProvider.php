<?php namespace PhpNFe\NFSe\Providers\Simpliss;

use Carbon\Carbon;
use PhpNFe\NFSe\NFSe;
use Illuminate\Support\Arr;
use PhpNFe\NFSe\Builder\Rps;
use PhpNFe\NFSe\Builder\RCancela;
use PhpNFe\NFSe\Providers\Provider;

class SimplissProvider extends Provider
{
    /**
     * User para acessar o portal (CNPJ do prestador).
     *
     * @var string
     */
    protected $portalUser;

    /**
     * Senha para acessar o portal.
     *
     * @var string
     */
    protected $portalPass;

    /**
     * @param NFSe $nfse
     */
    public function __construct(NFSe $nfse)
    {
        parent::__construct($nfse);

        $this->portalUser = $nfse->config('portal.user', '');
        $this->portalPass = $nfse->config('portal.pass', '');
    }

    /**
     * Autorizar um RPS em NFSe.
     *
     * @param Rps $rps
     * @param $amb
     * @return RetornoAutoriza
     */
    public function autorizar(Rps $rps, $amb)
    {
        return $this->callWithCertPath(function($certPath) use ($rps, $amb) {
            $params = [
                'local_cert' => $certPath . '/cert.key',
                'trace' => true,
            ];

            $method = 'GerarNfse';
            $soap = new \SoapClient($this->getWsdlPath($rps->codMun, $amb), $params);

            // Converter RPS em object.
            $data = $this->makeRpsToRequest($rps);

            return new RetornoAutoriza($soap->$method($data));
        });
    }

    /**
     * Cancelar uma NFSe emitida.
     *
     * @param RCancela $can
     * @param $amb
     * @return RetornoCancela
     */
    public function cancela(RCancela $can, $amb)
    {
        return $this->callWithCertPath(function($certPath) use ($can, $amb) {
            $params = [
                'local_cert' => $certPath . '/cert.key',
                'trace' => true,
            ];

            $method = 'CancelarNfse';
            $soap = new \SoapClient($this->getWsdlPath($can->codMun, $amb), $params);

            // Converter RPS em object.
            $data = $this->makeRCanToRequest($can);

            return new RetornoCancela($soap->$method($data));
        });
    }

    /**
     * @param Rps $rps
     * @return mixed
     */
    protected function makeRpsToRequest(Rps $rps)
    {
        $nats = [
            '100' => '1', // Tributado no Municipio
            '200' => '2', // Tributado fora do Municipio
            '300' => '3', // Isento
            '400' => '4', // Imune
            '900' => '5', // Exigibilidade suspensa por decisão judicial
            '901' => '6', // Exigibilidade suspensa por procedimento administrativo
        ];

        $tomadorId = is_null($rps->tomador->identificacao->cnpj) ? 'Cpf' : 'Cnpj';
        $tomadorDoc = is_null($rps->tomador->identificacao->cnpj) ? $rps->tomador->identificacao->cpf : $rps->tomador->identificacao->cnpj;

        $data = [
            'GerarNovaNfseEnvio' => [
                'Prestador' => [
                    'Cnpj'               => $rps->prestador->identificacao->cnpj,
                    'InscricaoMunicipal' => $rps->prestador->identificacao->inscricaoMun,
                ],

                'InformacaoNfse' => [
                    'NaturezaOperacao'       => Arr::get($nats, $rps->ideRPS->naturezaOperacao, '1'),
                    'OptanteSimplesNacional' => $rps->prestador->simplesNacional,
                    'IncentivadorCultural'   => $rps->prestador->incentivadorCultural,
                    'Status'                 => '1', // Normal
                    'Competencia'            => Carbon::createFromFormat(Carbon::ATOM, $rps->ideRPS->dataEmissao)->format('Y-m-d'),

                    'Servico' => [
                        'Valores' => [
                            'ValorServicos'    => $rps->servico->valores->valorServicos,
                            'ValorPis'         => $rps->servico->valores->valorPIS,
                            'ValorCofins'      => $rps->servico->valores->valorCOFINS,
                            'ValorInss'        => $rps->servico->valores->valorINSS,
                            'ValorIr'          => $rps->servico->valores->valorIR,
                            'ValorCsll'        => $rps->servico->valores->valorCSLL,
                            'IssRetido'        => $rps->servico->valores->ISSRetido,
                            'ValorIss'         => $rps->servico->valores->valorISS,
                            'ValorIssRetido'   => $rps->servico->valores->valorISSRetido,
                            'BaseCalculo'      => $rps->servico->valores->baseCalculo,
                            'Aliquota'         => $rps->servico->valores->aliquota,
                            'ValorLiquidoNfse' => $rps->servico->valores->valorLiquidoNfse,
                        ],

                        'CodigoMunicipio'           => $rps->servico->codMun,
                        'ItemListaServico'          => $rps->servico->codigoServico,
                        'CodigoTributacaoMunicipio' => $rps->servico->codigoServico,
                        'Discriminacao'             => $rps->servico->discriminacao,
                        'ItensServico' => [
                            'Descricao'     => $rps->servico->discriminacao,
                            'Quantidade'    => 1,
                            'ValorUnitario' => $rps->servico->valores->valorServicos,
                            'IssTributavel' => '1',
                        ],
                    ],
                    'Tomador' => [
                        'IdentificacaoTomador' => [
                            'CpfCnpj' => [
                                $tomadorId => $tomadorDoc,
                            ],
                        ],
                        'RazaoSocial' => $rps->tomador->identificacao->razaoSocial,
                        'Endereco' => [
                            'Endereco'        => $rps->tomador->identificacao->endereco->endereco,
                            'Numero'          => $rps->tomador->identificacao->endereco->numero,
                            'Complemento'     => $rps->tomador->identificacao->endereco->complemento,
                            'Bairro'          => $rps->tomador->identificacao->endereco->bairro,
                            'CodigoMunicipio' => $rps->tomador->identificacao->endereco->codMun,
                            'Uf'              => $rps->tomador->identificacao->endereco->uf,
                            'Cep'             => $rps->tomador->identificacao->endereco->cep,
                        ],
                    ],
                ],
            ],

            'pParam' => [
                'P1' => $this->portalUser,
                'P2' => $this->portalPass,
            ],
        ];

        return $data;
    }

    /**
     * @param RCancela $can
     * @return mixed
     */
    protected function makeRCanToRequest(RCancela $can)
    {
        $data = [
            'CancelarNfseEnvio' => [
                'Pedido' => [
                    'InfPedidoCancelamento' => [
                        'CodigoCancelamento' => '2',
                        'IdentificacaoNfse' => [
                            'Numero'             => $can->numNfse,
                            'Cnpj'               => $can->cnpj,
                            'InscricaoMunicipal' => $can->inscrMun,
                            'CodigoMunicipio'    => $can->codMun,
                        ],
                    ],
                ],
            ],

            'pParam' => [
                'P1' => $this->portalUser,
                'P2' => $this->portalPass,
            ],
        ];

        return $data;
    }
}