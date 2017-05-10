<?php

namespace PhpNFe\NFSe\Builder\Servico;

use PhpNFe\Tools\Builder\Builder;

/**
 * Dados dos valores do servico.
 * Class Valores.
 */
class Valores extends Builder
{
    /**
     * Valor total dos servicos.
     *
     * @var float
     */
    public $valorServicos;

    /**
     * Valor total das deducoes.
     *
     * @var float
     */
    public $valorDeducoes;

    /**
     * Valor total do PIS.
     *
     * @var float
     */
    public $valorPIS;

    /**
     * Valor total do COFINS.
     *
     * @var float
     */
    public $valorCOFINS;

    /**
     * Valor total do INSS.
     *
     * @var float
     */
    public $valorINSS;

    /**
     * Valor total do IR.
     *
     * @var float
     */
    public $valorIR;

    /**
     * Valor total do CSLL.
     *
     * @var float
     */
    public $valorCSLL;

    /**
     * ISSRetido.
     * 1 - True
     * 2 - False.
     *
     * @var
     */
    public $ISSRetido;

    /**
     * Valor total do ISS.
     *
     * @var float
     */
    public $valorISS;

    /**
     * Base do calculo.
     * (Valor dos serviços - Valor das deduções - descontos
     * incondicionais).
     *
     * @var float
     */
    public $baseCalculo;

    /**
     * Aliquota do servico.
     *
     * @var float
     */
    public $aliquota;

    /**
     * Valor liquido da Nfse.
     * (ValorServicos - ValorPIS - ValorCOFINS - ValorINSS -
     * ValorIR - ValorCSLL - OutrasRetençoes -
     * ValorISSRetido - DescontoIncondicionado -
     * DescontoCondicionado).
     *
     * @var float
     */
    public $valorLiquidoNfse;

    /**
     * Valor do ISS Retido.
     *
     * @var float
     */
    public $valorISSRetido;

    /**
     * Valor do desconto Condicional.
     *
     * @var float
     * @dec 2
     */
    public $descontoCondicionado;

    /**
     * Valor do dexonto Incondicional.
     *
     * @var float
     * @dec 2
     */
    public $descontoIncondicionado;
}