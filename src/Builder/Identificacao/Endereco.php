<?php

namespace PhpNFe\NFSe\Builder\Identificacao;

use PhpNFe\Tools\Builder\Builder;

/**
 * Informacoes de Endereco.
 * Class Endereco.
 */
class Endereco extends Builder
{
    /**
     * Endereco.
     *
     * @var string
     */
    public $endereco;

    /**
     * Numero do endereco.
     *
     * @var string
     */
    public $numero;

    /**
     * Complemento do Endereco.
     *
     * @var string
     */
    public $complemento;

    /**
     * Nome do Bairro.
     *
     * @var string
     */
    public $bairro;

    /**
     * Codigo do Municipio.
     *
     * @var string
     */
    public $codMun;

    /**
     * Sigla do Estado.
     *
     * @var string
     */
    public $uf;

    /**
     * CEP da localidade.
     *
     * @var string
     */
    public $cep;
}