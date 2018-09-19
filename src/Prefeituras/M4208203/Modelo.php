<?php namespace PhpNFe\NFSe\Prefeituras\M4208203;

use PhpNFe\Tools\Certificado\Certificado;
use PhpNFe\NFSe\Modelos\Publica\PublicaModelo;

class Modelo extends PublicaModelo
{
    /**
     * @param $codMun
     * @param Certificado $cert
     */
    public function __construct($codMun, Certificado $cert)
    {
        parent::__construct($codMun, $cert);

        $this->version = 'v03';
    }

}