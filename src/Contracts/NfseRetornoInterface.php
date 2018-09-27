<?php namespace PhpNFe\NFSe\Contracts;

use Carbon\Carbon;

interface NfseRetornoInterface
{
    /**
     * Retorna se teve erro ou não.
     * @return bool
     */
    public function isError();

    /**
     * Retorna a lista das mensagens de erro se tiver.
     * @return string
     */
    public function getErros();

    /**
     * Retorna o numero da Nfse.
     * @return string
     */
    public function getNumNfse();

    /**
     * Retorna o protocolo da operação.
     * @return string
     */
    public function getNumProt();

    /**
     * Retorna a data do evento.
     *
     * @return Carbon
     */
    public function getData();

    /**
     * Retorna o protocolo como XML.
     * @return string
     */
    public function getXmlProt();

    /**
     * @return string
     */
    public function getDanfePDF();
}