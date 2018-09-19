<?php

use PhpNFe\NFSe\NFSe;

/**
 * Configurações da estrutura de Itajai
 *
 * Mucipicio: Itajai - SC
 * Codigo: 4208203
 * Modelo: Publica
 */
return [
    'metodos' => [
        NFSe::ambProducao => [
            NFSe::mtdAutorizar => [
                'method' => 'GerarNfse',
                'op' => 'GerarNfseEnvio',
                'versao' => '1',
                'url' => 'http://nfse.itajai.sc.gov.br/nfse_integracao/Services?wsdl',
            ],
            NFSe::mtdCancelar => [
                'method' => 'CancelarNfse',
                'op' => 'CancelarNfseEnvio',
                'versao' => '1',
                'url' => 'http://nfse.itajai.sc.gov.br/nfse_integracao/Services?wsdl',
            ],
        ],

        NFSe::ambHomologacao => [
            NFSe::mtdAutorizar => [
                'method' => 'GerarNfse',
                'op' => 'GerarNfseEnvio',
                'versao' => '1',
                'url' => 'http://nfse-teste.publica.inf.br/itajai_nfse_integracao/Services?wsdl',
            ],
            NFSe::mtdCancelar => [
                'method' => 'CancelarNfse',
                'op' => 'CancelarNfseEnvio',
                'versao' => '1',
                'url' => 'http://nfse-teste.publica.inf.br/itajai_nfse_integracao/Services?wsdl',
            ],
        ],
    ],
];