<?php

namespace PhpNFe\NFSe\Builder\Identificacao;

use PhpNFe\Tools\Builder\Builder;

/**
 * Informacoes de Contato.
 * Class Contato.
 */
class Contato extends Builder
{
    /**
     * Telefone para contato.
     * @var string
     */
    public $telefone;

    /**
     * Email para contato.
     * @var string
     */
    public $email;
}