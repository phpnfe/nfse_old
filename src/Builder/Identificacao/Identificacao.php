<?php

namespace PhpNFe\NFSe\Builder\Identificacao;

use PhpNFe\Tools\Builder\Builder;

/**
 * Identificacao do Prestador ou Tomador.
 * Class Identificacao.
 */
class Identificacao extends Builder
{
    /**
     * CPF.
     *
     * @var string
     */
    public $cpf;

    /**
     * CNPJ.
     *
     * @var string
     */
    public $cnpj;

    /**
     * Inscricao Municipal.
     *
     * @var string
     */
    public $inscricaoMun;

    /**
     * Razao Social.
     *
     * @var string
     */
    public $razaoSocial;

    /**
     * Endereco.
     *
     * @var Endereco
     */
    public $endereco;

    /**
     * Contato.
     *
     * @var Contato
     */
    public $contato;

    /**
     * Identificacao constructor.
     */
    public function __construct()
    {
        $this->endereco = new Endereco();
        $this->contato = new Contato();
    }
}