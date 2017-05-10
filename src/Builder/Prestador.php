<?php

namespace PhpNFe\NFSe\Builder;

use PhpNFe\Tools\Builder\Builder;
use PhpNFe\NFSe\Builder\Identificacao\Identificacao;

/**
 * Informacoes do Prestador.
 * Class Prestador.
 */
class Prestador extends Builder
{
    /**
     * Identificacao do Prestador.
     *
     * @var Identificacao
     */
    public $identificacao;

    /**
     * Incentivador Cultural.
     * 1 - Sim
     * 2 - Nao.
     * @var string
     */
    public $incentivadorCultural;

    /**
     * Optante simples ou nacional.
     * 1 - Sim
     * 2 - Nao.
     * @var string
     */
    public $simplesNacional;

    /**
     * Prestador constructor.
     */
    public function __construct()
    {
        $this->identificacao = new Identificacao();
    }
}