<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/cert_cristoluz.php';
require __DIR__ . '/rps_bal_camoboriu.php';

use PhpNFe\NFSe\NFSe;
use PhpNFe\NFSe\Contracts\NfseProviderInterface;

$amb = NfseProviderInterface::ambNfseHomologacao;
//$amb = NfseProviderInterface::ambNfseProducao;

$config = [
    'portal' => [
        'user' => '01799858000120',
        'pass' => 'CRISTO@2018', // Homologacao
        //'pass' => '3967b6', // Producao
    ],
];

$nfse = new NFSe($cert, $config);

$nfse->extend($cod_municipio, function(NFSe $nfse) {
    return new \PhpNFe\NFSe\Providers\Simpliss\SimplissProvider($nfse);
});

// Assinar
$ret = $nfse->autorizar($rps, $amb);
if ($ret->isError()) {
    throw new Exception($ret->getErros());
}

$xml = $ret->getXmlProt();
file_put_contents(__DIR__ . '/ret.xml', $xml);


// Cancelar
$can           = new \PhpNFe\NFSe\Builder\RCancela();
$can->cnpj     = $rps->prestador->identificacao->cnpj;
$can->codMun   = $rps->codMun;
$can->inscrMun = $rps->prestador->identificacao->inscricaoMun;
$can->numNfse  = $ret->getNumNfse();
$can->motivo   = 'Teste de cancelamento de NFSE';

$ret = $nfse->cancela($can, $amb);
if ($ret->isError()) {
    throw new Exception($ret->getErros());
}

$xml = $ret->getXmlProt();
file_put_contents(__DIR__ . '/can.xml', $xml);


print_r($ret);
