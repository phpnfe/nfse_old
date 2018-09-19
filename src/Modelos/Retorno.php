<?php namespace PhpNFe\NFSe\Modelos;

use PhpNFe\NFSe\Danfe\DanfeNFSe;

abstract class Retorno
{
    /**
     * Retorna a Nfse.
     * @return string
     */
    abstract public function getNfse();

    /**
     * Retorna o protocolo da operação.
     * @return string
     */
    abstract public function getProt();

    /**
     * Retorna se teve erro ou não.
     * @return bool
     */
    abstract public function isError();

    /**
     * Retorna se teve erro ou não no cancelamento.
     * @return bool
     */
    abstract public function isErrorCancela();

    /**
     * Retorna a mensagem de erro caso tiver.
     * @return string
     */
    abstract public function getErro();

    /**
     * Retorna o código do erro caso tiver.
     * @return string
     */
    abstract public function getCodigoErro();

    /**
     * Retorna o xml recebido do servidor.
     * @return string
     */
    abstract public function getXml();
}