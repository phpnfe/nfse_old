<?php namespace PhpNFe\NFSe\Prefeituras;

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

    /**
     * Instancia o retorno da cidade passada pelo código municipal.
     * @param $codMun
     * @param $xml
     * @return mixed
     */
    public static function make($codMun, $xml)
    {
        $cidade = Config::getCidade($codMun);

        $class = 'PhpNFe\\NFSe\\Prefeituras\\' . $cidade . '\\' . $cidade . 'Retorno';

        return new $class($xml);
    }
}