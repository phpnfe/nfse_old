<?php namespace PhpNFe\NFSe\Builder;

use PhpNFe\Tools\Builder\Builder;

class RCancela extends Builder
{
    /**
     * Número da NFSe.
     * @var string
     */
    public $numNfse;

    /**
     * CNPJ do prestador.
     * @var string
     */
    public $cnpj;

    /**
     * Inscricao municipal do prestador.
     * @var string
     */
    public $inscrMun;

    /**
     * Codigo do municipio.
     * @var string
     */
    public $codMun;

    /**
     * Motivo do cancelamento.
     * @var string
     */
    public $motivo;
}