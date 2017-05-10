<?php

namespace PhpNFe\NFSe\Builder;

use PhpNFe\Tools\Builder\Builder;
use PhpNFe\NFSe\Prefeituras\Config;
use PhpNFe\NFSe\Builder\Servico\Servico;
use PhpNFe\NFSe\Builder\Templates\Manipulador;

/**
 * Builder do RPS (Recibo Provisorio de Servico).
 * Class Nfse.
 */
class Rps extends Builder
{
    /**
     * Tipo do ambiente.
     *
     *
     * @var string
     */
    public $tpAmb = Config::ambHomologacao;

    /**
     * Id da tag InfRps.
     *
     * @var
     */
    public $idInfRps = 'assinar';

    /**
     * Informacoes do Rps.
     *
     * @var IdeRPS
     */
    public $ideRPS;

    /**
     * Informacoes do prestador.
     *
     * @var Prestador
     */
    public $prestador;

    /**
     * Informacoes do tomador.
     *
     * @var Tomador
     */
    public $tomador;

    /**
     * Informacoes do Servico.
     *
     * @var Servico
     */
    public $servico;

    /**
     * Codigo Municipal.
     *
     * @var string
     */
    public $codMun;

    /**
     * Nfse constructor.
     */
    public function __construct($codMun)
    {
        $this->ideRPS = new IdeRPS();
        $this->prestador = new Prestador();
        $this->tomador = new Tomador();
        $this->servico = new Servico();
        $this->codMun = $codMun;
    }

    /**
     * @return string
     */
    public function getXML()
    {
        $xml = Manipulador::load($this, $this->codMun);

        return $xml->gerar();
    }

    /**
     * @param $arquivo
     */
    public function salvaXML($arquivo)
    {
        file_put_contents($arquivo, $this->getXML());
    }
}