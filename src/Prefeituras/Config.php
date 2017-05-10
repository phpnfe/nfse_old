<?php namespace PhpNFe\NFSe\Prefeituras;

class Config
{
    /**
     * Ambientes.
     */
    const ambHomologacao = 'homologacao';
    const ambProducao = 'producao';

    public static $ambientes = [
        self::ambProducao => '1',
        self::ambHomologacao => '2',
    ];

    /**
     * Funções.
     */
    const mtAutoriza = 'autoriza';
    const mtCancela = 'cancela';
    const mtCartaCorrecao = 'cartacorrecao';
    const mtInutilizacao = 'inutilizacao';

    /**
     * Cidades.
     * @var array
     */
    protected static $servers = [
        // Blumenau
        '4202404' => [
            self::ambProducao => [
                self::mtAutoriza => [
                    'method' => 'EnvioRPS',
                    'op' => 'PedidoEnvioRPS',
                    'versao' => '1',
                    'url' => 'https://nfse.blumenau.sc.gov.br/ws/lotenfe.asmx?wsdl',
                ],
                self::mtCancela => [
                    'method' => 'CancelamentoNFe',
                    'op' => 'PedidoCancelamentoNFe',
                    'versao' => '1',
                    'url' => 'https://nfse.blumenau.sc.gov.br/ws/lotenfe.asmx?wsdl',
                ],
            ],
            self::ambHomologacao => [
                self::mtAutoriza => [
                    'method' => 'TesteEnvioLoteRPS',
                    'op' => 'PedidoEnvioLoteRPS',
                    'versao' => '1',
                    'url' => 'https://nfse.blumenau.sc.gov.br/ws/lotenfe.asmx?wsdl',
                ],
            ],

            'configs' => [
                'natOp' => [
                    '101' => 'T',
                    '111' => 'F',
                    '301' => 'I',
                    '121' => 'T',
                    '201' => 'F',
                    '501' => 'T',
                    '511' => 'F',
                    '541' => 'T',
                    '551' => 'T',
                    '601' => 'F',
                    '701' => 'I',
                ],
                'tipoRPS' => [
                    '1' => 'RPS',
                    '2' => 'RPS-M',
                    '3' => 'RPS-C',
                ],
                'statusRPS' => [
                    'N' => 'N',
                    'C' => 'C',
                    'E' => 'E',
                ],
                'boolean' => [
                    'true' => 'true',
                    'false' => 'false',
                ],
                'rps' => 'RPS',
            ],
        ],
        // Itajaí
        '4208203' => [
            self::ambProducao => [
                self::mtAutoriza => [
                    'method' => 'GerarNfse',
                    'op' => 'GerarNfseEnvio',
                    'versao' => '1',
                    'url' => 'http://nfse.itajai.sc.gov.br/nfse_integracao/Services?wsdl',
                ],
                self::mtCancela => [
                    'method' => 'CancelarNfse',
                    'op' => 'CancelarNfseEnvio',
                    'versao' => '1',
                    'url' => 'http://nfse.itajai.sc.gov.br/nfse_integracao/Services?wsdl',
                ],
                self::mtCartaCorrecao => [
                    'method' => 'CartaCorrecaoNfseEnvio',
                    'op' => 'CartaCorrecaoNfseEnvio',
                    'versao' => '1',
                    'url' => 'http://nfse.itajai.sc.gov.br/nfse_integracao/Services?wsdl',
                ],
            ],
            self::ambHomologacao => [
                self::mtAutoriza => [
                    'method' => 'GerarNfse',
                    'op' => 'GerarNfseEnvio',
                    'versao' => '1',
                    'url' => 'http://nfse-teste.publica.inf.br/itajai_nfse_integracao/Services?wsdl',
                ],
                self::mtCancela => [
                    'method' => 'CancelarNfse',
                    'op' => 'CancelarNfseEnvio',
                    'versao' => '1',
                    'url' => 'http://nfse-teste.publica.inf.br/itajai_nfse_integracao/Services?wsdl',
                ],
            ],

            'configs' => [
                'natOp' => [
                    '101' => '101',
                    '111' => '111',
                    '121' => '121',
                    '201' => '201',
                    '301' => '301',
                    '501' => '501',
                    '511' => '511',
                    '541' => '541',
                    '551' => '551',
                    '601' => '601',
                    '701' => '701',
                ],
                'tipoRPS' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],
                'statusRPS' => [
                    'N' => '1',
                    'C' => '2',
                ],
                'boolean' => [
                    'true' => '1',
                    'false' => '2',
                ],
                'rps' => 'Rps',
            ],
        ],
    ];

    protected static $pedidos = [
        // Blumenau
        '4202404' => [
            self::mtAutoriza => [
                'pedido' => 'PedidoEnvioRPS',
                'schema' => 'PedidoEnvioRPS_v01',
            ],
            self::mtCancela => [
                'pedido' => 'PedidoCancelamentoNFe',
                'schema' => 'PedidoCancelamentoNFe_v01',
            ],
        ],
        // Itajai
        '4208203' => [
            self::mtAutoriza => [
                'pedido' => 'schema_nfse',
                'schema' => 'schema_nfse_v03',
            ],
            self::mtCancela => [
                'pedido' => 'schema_nfse',
                'schema' => 'schema_nfse_v03',
            ],
        ],
    ];

    const blumenau = 'Blumenau';
    const itajai = 'Itajai';

    /**
     * @param $cod - Código Municipal da cidade.
     * @return string
     */
    public static function getCidade($cod)
    {
        switch ($cod) {
            case '4202404':
                return self::blumenau;
            case '4208203':
                return self::itajai;
            default:
                return '';

        }
    }

    /**
     * Pegar o código municipal da cidade.
     * @param $cidade
     * @return string
     * @throws \Exception
     */
    public static function getCodMun($cidade)
    {
        switch ($cidade) {
            case 'blumenau':
                return '4202404';
            case 'itajai':
                return '4208203';
            default:
                throw new \Exception(sprintf('Cidade %s não encontrada!', $cidade));
        }
    }

    /**
     * Pegar o nome do node do Rps.
     * @param $codMun
     * @return mixed
     */
    public static function getNomeRps($codMun)
    {
        return self::$servers[$codMun]['configs']['rps'];
    }

    /**
     * @param $cod - Código Municipal da cidade.
     * @param $metodo
     * @return mixed
     */
    public static function getPedido($cod, $metodo)
    {
        return self::$pedidos[$cod][$metodo]['pedido'];
    }

    /**
     * @param $cod
     * @param $metodo
     * @return string
     */
    public static function getSchema($cod, $metodo)
    {
        return self::$pedidos[$cod][$metodo]['schema'];
    }

    /**
     * Pegar a info do config.
     * @param $ambiente
     * @param $cMun
     * @param $method
     * @return MethodConfig
     * @throws \Exception
     */
    public static function getMethodInfo($ambiente, $cMun, $method)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = 'producao';
                break;
            case '2':
                $ambiente = 'homologacao';
        }

        // Verificar se estado foi definido
        if (array_key_exists($cMun, self::$servers) != true) {
            throw new \Exception(sprintf('Código do município %s não foi encontrado na definição de servidores' . $cMun));
        }

        // Verificar se ambiente foi definido no estado
        if (array_key_exists($ambiente, self::$servers[$cMun]) != true) {
            throw new \Exception(sprintf('Ambiente %s não foi definido nas configurações do estado ' . $ambiente));
        }

        // Verificar se metodo foi definido no ambiente
        if (array_key_exists($method, self::$servers[$cMun][$ambiente]) != true) {
            throw new \Exception(sprintf('metodo %s não foi definido nas configurações do ambiente' . $method));
        }

        $info = array_merge(['cuf' => $cMun, 'amb' => $ambiente, 'configs' => self::$servers[$cMun]['configs']], self::$servers[$cMun][$ambiente][$method]);

        return new MethodConfig($info);
    }
}