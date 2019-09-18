<?php

namespace PhpNFe\NFSe\Builder;

use PhpNFe\Tools\Builder\Builder;

/**
 * Informacoes de identificacao do RPS.
 * Class IdentificacaoRPS.
 */
class IdeRPS extends Builder
{
    /**
     * Numero do RPS.
     *
     * @var string
     */
    public $numeroRPS;

    /**
     * Serie do RPS.
     *
     * @var string
     */
    public $serieRPS;

    /**
     * Tipo do RPS.
     * 1 - RPS
     * 2 - Nota fiscal conjugada (mista)
     * 3 - Cupom.
     * @var string
     */
    public $tipoRPS;

    /**
     * Status do RPS.
     * N – Normal;
     * C – Cancelada;
     * E – Extraviada.
     *
     * @var string
     */
    public $statusRPS;

    /**
     * Data da Emissao do RPS.
     *
     * @var string
     */
    public $dataEmissao;

    /**
     * Natureza de Operacao do RPS.
     *
     * 100 Tributado no Municipio
     * 200 Tributado fora do Municipio
     * 300 Isento
     * 400 Imune
     * 500 ISS retido pelo tomador
     * 900 Exigibilidade suspensa por decisão judicial
     * 901 Exigibilidade suspensa por procedimento administrativo
     * 902 ISS Fixo (Soc. Profissionais)
     *
     * OLDs:
     * 101 ISS devido para o Município prestador
     * 111 ISS devido para um Município que não é o do prestador
     * 121 ISS Fixo (Soc. Profissionais)
     * 201 ISS retido pelo tomador/intermediário
     * 301 Operação imune, isenta ou não tributada.
     */
    public $naturezaOperacao;
}