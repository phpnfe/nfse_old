<?php

namespace PhpNFe\NFSe\Builder;

use PhpNFe\Tools\Builder\Builder;
use PhpNFe\NFSe\Builder\Identificacao\Identificacao;

/**
 * Informacoes do Prestador.
 * Class Tomador.
 */
class Tomador extends Builder
{
    /**
     * Identificacao do Tomador.
     *
     * @var Identificacao
     */
    public $identificacao;

    /**
     * Tomador constructor.
     */
    public function __construct()
    {
        $this->identificacao = new Identificacao();
    }
}