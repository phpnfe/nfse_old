<?php namespace PhpNFe\NFSe\Prefeituras;

use PhpNFe\Tools\XML;
use Illuminate\Filesystem\Filesystem;
use PhpNFe\Tools\Certificado\Certificado;
use PhpNFe\NFSe\Prefeituras\Blumenau\Sign;
use PhpNFe\NFSe\Prefeituras\Itajai\ItajaiRetorno;
use PhpNFe\NFSe\Prefeituras\Blumenau\BlumenauRetorno;
use PhpNFe\NFSe\Prefeituras\BalnearioCamboriu\BalnearioCamboriuRetorno;

/**
 * Classe que direciona as requisicoes para os provedores certos e realiza as mesmas.
 * Class Roteador.
 */
class Roteador
{
    const Blumenau          = 'blumenau';
    const Itajai            = 'itajai';
    const BalnearioCamboriu = 'balneariocamboriu';

    /**
     * Codigo do municipio.
     * @var
     */
    protected $codMun;

    /**
     * Certificado.
     * @var Certificado
     */
    protected $cert;

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * Caminho do diretorio criado no temp para armazenamento de dados do certificado.
     * @var
     */
    protected $certDir;

    /**
     * Roteador constructor.
     * @param $codMun
     * @param $cert
     */
    public function __construct($codMun, $cert)
    {
        $this->codMun = $codMun;
        $this->cert = $cert;
        $this->file = new Filesystem();
    }

    /**
     * @param $codMun
     * @param $data
     * @param MethodConfig $config
     * @return \Exception|BlumenauRetorno|ItajaiRetorno
     * @throws \Exception
     */
    public function retorno($codMun, $data, MethodConfig $config)
    {
        // Criar diretorio temporario.
        $this->certDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $this->file->makeDirectory($this->certDir);

        // Salvar certificados na pasta temp criada.
        $this->cert->salvaChave($this->certDir);

        $metodo = static::distribuir($codMun);

        // Chama o metodo correspondente a cidade da inscricao municipal passada por parametro
        return $this->$metodo($data, $config);
        //return call_user_func_array($this->distribuir($codMun), [$data, $config]);
    }

    /**
     * Montar xml do cancela.
     * @param $numNFse
     * @param $cnpj
     * @param $inscMun
     * @param $codMun
     * @param $motivo
     * @return mixed
     */
    public function montarCancela($numNFse, $cnpj, $inscMun, $codMun, $motivo)
    {
        $metodo = static::distribuir($codMun) . 'Cancela';

        return $this->$metodo($numNFse, $cnpj, $inscMun, $codMun, $motivo);
    }

    /**
     * Descobrir município pelo código municipal.
     * @param $codMun
     * @return \Exception|string
     */
    public static function distribuir($codMun)
    {
        //Distribuir os parametros para a devida cidade
        switch ($codMun) {
            case '4202404':
                return self::Blumenau;
            case '4208203':
                return self::Itajai;
            case '4202008':
                return self::BalnearioCamboriu;
        }

        return new \Exception('Código municipal incorreto!');
    }

    /**
     * Usa o template para gerar o xml de Balneario Camboriu.
     * @param $numNfse
     * @param $cnpj
     * @param $inscMun
     * @param $codMun
     * @param $motivo
     * @return mixed|string
     */
    protected function balneariocamboriuCancela($numNfse, $cnpj, $inscMun, $codMun, $motivo)
    {
        $xml = file_get_contents(__DIR__ . '/BalnearioCamboriu/templateCancela.xml');
        $xml = str_replace('{{numNfse}}', $numNfse, $xml);
        $xml = str_replace('{{cnpj}}', $cnpj, $xml);
        $xml = str_replace('{{inscMun}}', $inscMun, $xml);
        $xml = str_replace('{{codMun}}', $codMun, $xml);
        $xml = str_replace('{{motivo}}', $motivo, $xml);

        $xml = $this->cert->assinarXML($xml, 'InfPedidoCancelamento');

        return $xml;
    }

    /**
     * Usa o template para gerar o xml de Itajai.
     * @param $numNfse
     * @param $cnpj
     * @param $inscMun
     * @param $codMun
     * @param $motivo
     * @return mixed|string
     */
    protected function itajaiCancela($numNfse, $cnpj, $inscMun, $codMun, $motivo)
    {
        $xml = file_get_contents(__DIR__ . '/Itajai/templateCancela.xml');
        $xml = str_replace('{{numNfse}}', $numNfse, $xml);
        $xml = str_replace('{{cnpj}}', $cnpj, $xml);
        $xml = str_replace('{{inscMun}}', $inscMun, $xml);
        $xml = str_replace('{{codMun}}', $codMun, $xml);
        $xml = str_replace('{{motivo}}', $motivo, $xml);

        $xml = $this->cert->assinarXML($xml, 'InfPedidoCancelamento');

        return $xml;
    }

    /**
     * Usa o template para gerar o xml de Blumenau.
     * @param $numNfse
     * @param $cnpj
     * @param $inscMun
     * @return mixed|string
     */
    protected function blumenauCancela($numNfse, $cnpj, $inscMun)
    {
        $xml = file_get_contents(__DIR__ . '/Blumenau/templateCancela.xml');
        $xml = str_replace('{{cnpj}}', $cnpj, $xml);
        $xml = str_replace('{{inscMun}}', $inscMun, $xml);
        $xml = str_replace('{{numNfse}}', $numNfse, $xml);

        $dom = XML::createByXml($xml);

        Sign::signCanc($dom, $this->cert);

        Sign::sign($dom, $this->cert);

        $xml = $dom->saveXML();

        return $xml;
    }

    /**
     * Processar Blumenau.
     * @param $data
     * @param MethodConfig $config
     * @return BlumenauRetorno
     */
    protected function blumenau($data, MethodConfig $config)
    {
        $params = [
            'local_cert' => $this->certDir . '/cert.key',
            'soap_version' => SOAP_1_1,
            //'ssl_method' => SOAP_SSL_METHOD_SSLv3,
            'ssl_method' => SOAP_SSL_METHOD_TLS, // Trocado apartir de 27/07/2017
            'trace' => true,
            'stream_context' => stream_context_create(
                ['ssl' => [
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT, // Trocado apartir de 27/07/2017
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
                ]
            ),
        ];

        $soap = new \SoapClient(__DIR__ . '/Blumenau/bnuwsdl.xml', $params);

        $method = $config->method;

        $params = [
            'VersaoSchema' => 1,
            'MensagemXML' => $data,
        ];

        return new BlumenauRetorno($soap->$method($params)->RetornoXML);
    }

    /**
     * Processar Itajai.
     * @param $data
     * @param MethodConfig $config
     * @return ItajaiRetorno
     */
    protected function itajai($data, MethodConfig $config)
    {
        $params = [
            'local_cert' => $this->certDir . '/cert.key',
            'trace' => true,
        ];

        $wsdl = sprintf('wsdl-%s.xml', $config->amb);

        $soap = new \SoapClient(__DIR__ . '/Itajai/' . $wsdl, $params);

        $method = $config->method;

        return new ItajaiRetorno($soap->$method($data));
    }

    /**
     * Processar Balneario Camboriu.
     *
     * @param $data
     * @param MethodConfig $config
     * @return ItajaiRetorno
     */
    protected function balneariocamboriu($data, MethodConfig $config)
    {
        $params = [
            'local_cert' => $this->certDir . '/cert.key',
            'trace' => true,
        ];

        $wsdl = sprintf('wsdl-%s.xml', $config->amb);

        $soap = new \SoapClient(__DIR__ . '/BalnearioCamboriu/' . $wsdl, $params);

        $method = $config->method;

        $params = [
            'GerarNovaNfseEnvio' => [
                'Prestador' => [
                    'Cnpj' => '01799858000120',
                    'InscricaoMunicipal' => '926',
                ],

                'InformacaoNfse' => [
                    'NaturezaOperacao' => '1',
                    'OptanteSimplesNacional' => '2',
                    'IncentivadorCultural' => '2',
                    'Status' => '1',
                    'Competencia' => '2018-09-26',

                    'Servico' => [
                        'Valores' => [
                            'ValorServicos' => 10.20,
                            'ValorPis' => 0,
                            'ValorCofins' => 0,
                            'ValorInss' => 0,
                            'ValorIr' => 0,
                            'ValorCsll' => 0,
                            'IssRetido' => 2,
                            'ValorIss' => 0.31,
                            'ValorIssRetido' => 0,
                            'BaseCalculo' => 10.20,
                            'Aliquota' => 3,
                            'ValorLiquidoNfse' => 10.20,
                        ],
                        'CodigoMunicipio' => '4202008',
                        'ItemListaServico' => '12.17',
                        'CodigoTributacaoMunicipio' => '12.17',
                        'Discriminacao' => 'INGRESSOS',
                        'ItensServico' => [
                            'Descricao' => 'INGRESSOS',
                            'Quantidade' => 1,
                            'ValorUnitario' => 10.20,
                            'IssTributavel' => 1,
                        ],
                    ],
                    'Tomador' => [
                        'IdentificacaoTomador' => [
                            'CpfCnpj' => [
                                'Cpf' => '00852036965',
                            ],
                        ],
                        'RazaoSocial' => 'Bruno',
                        'Endereco' => [
                            'Endereco' => 'Rua',
                            'Numero' => '123',
                            'Complemento' => 'Casa 1',
                            'Bairro' => 'BAIRRO',
                            'CodigoMunicipio' => '4202404',
                            'Uf' => 'SC',
                            'Cep' => '89032300',
                        ],
                    ],
                ],
            ],
            'pParam' => ['P1' => '01799858000120', 'P2' => 'CRISTO@2018'],
        ];

        $method = 'GerarNfse';
        $ret = $soap->$method($params);

        return new BalnearioCamboriuRetorno($soap->$method($params));
    }

    public function __destruct()
    {
        $this->file->deleteDirectory($this->certDir);
    }
}
