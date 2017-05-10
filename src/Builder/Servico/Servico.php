<?php

namespace PhpNFe\NFSe\Builder\Servico;

use PhpNFe\Tools\Builder\Builder;

/**
 * Informacoes do Servico.
 * Class Servico.
 */
class Servico extends Builder
{
    /**
     * Valores do servico.
     *
     * @var Valores
     */
    public $valores;

    /**
     * Codigo do servico.
     *
     * @var string
     */
    public $codigoServico;

    /**
     * Discriminacao do servico.
     *
     * @var string
     */
    public $discriminacao;

    /**
     * Codigo do Municipio.
     *
     * @var string
     */
    public $codMun;

    /**
     * Codigo do País.
     *
     * @var string
     */
    public $codPais;

    public function __construct()
    {
        $this->valores = new Valores();
    }
}