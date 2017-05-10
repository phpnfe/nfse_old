<?php namespace PhpNFe\NFSe\Prefeituras\Itajai;

use PhpNFe\Tools\XML;
use PhpNFe\NFSe\Prefeituras\Retorno;

class ItajaiRetorno extends Retorno
{
    /**
     * Xml do retorno.
     * @var XML
     */
    protected $xml;

    /**
     * ItajaiRetorno constructor.
     * @param XML $xml
     */
    public function __construct($xml)
    {
        $this->xml = XML::createByXml($xml);
    }

    /**
     * Retorna o xml recebido do servidor.
     * @return string
     */
    public function getXml()
    {
        return $this->xml->C14N();
    }

    /**
     * Pegar o número da Nfse.
     * @return string
     */
    public function getNfse()
    {
        return $this->xml->getElementsByTagName('Numero')->item(0)->textContent;
    }

    /**
     * Retorna o protocolo da operação.
     * @return string
     */
    public function getProt()
    {
        return $this->xml->getElementsByTagName('CodigoVerificacao')->item(0)->textContent;
    }

    /**
     * Pega o codigo da mensagem retornada pela requisicao servidor.
     * @return string
     */
    public function getCodigoErro()
    {
        return $this->xml->getElementsByTagName('Codigo')->item(0)->textContent;
    }

    /**
     * Retorna a mensagem de erro caso tiver.
     * @return string
     */
    public function getErro()
    {
        return $this->xml->getElementsByTagName('Mensagem')->item(0)->textContent;
    }

    /**
     * Retorna se teve erro ou não.
     * @return bool
     */
    public function isError()
    {
        return $this->xml->getElementsByTagName('Nfse')->item(0) == null ? true : false;
    }

    /**
     * Retorna se teve erro ou não no cancelamento.
     * @return bool
     */
    public function isErrorCancela()
    {
        return $this->xml->getElementsByTagName('Confirmacao')->item(0) == null ? true : false;
    }
}