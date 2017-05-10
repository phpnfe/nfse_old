<?php namespace PhpNFe\NFSe\Prefeituras\Blumenau;

use PhpNFe\Tools\XML;
use PhpNFe\NFSe\Prefeituras\Retorno;

class BlumenauRetorno extends Retorno
{
    /**
     * XML de Retorno.
     * @var
     */
    protected $xml;

    /**
     * BlumenauRetorno constructor.
     * @param $xml
     */
    public function __construct($xml)
    {
        $this->xml = XML::createByXml($xml);
    }

    /**
     * Get Xml retornado do servidor.
     * @return string
     */
    public function getXml()
    {
        return $this->xml->C14N();
    }

    /**
     * Retorna o número da chave da nfse.
     * @return string
     */
    public function getNfse()
    {
        return $this->xml->getElementsByTagName('NumeroNFe')->item(0)->textContent;
    }

    public function getProt()
    {
        return $this->xml->getElementsByTagName('CodigoVerificacao')->item(0)->textContent;
    }

    /**
     * Retorna se a requisição teve sucesso ou não.
     * @return bool
     */
    public function isError()
    {
        return $this->xml->getElementsByTagName('Sucesso')->item(0)->textContent == 'false' ? true : false;
    }

    /**
     * Retorna se teve erro ou não no cancelamento.
     * @return bool
     */
    public function isErrorCancela()
    {
        return $this->xml->getElementsByTagName('Sucesso')->item(0)->textContent == 'false' ? true : false;
    }

    /**
     * Retorna o xml de Erro.
     * @return null|string
     */
    public function getErro()
    {
        return $this->xml->getElementsByTagName('Descricao')->item(0)->textContent;
    }

    /**
     * Retorna o código do erro.
     * @return null|string
     */
    public function getCodigoErro()
    {
        return $this->xml->getElementsByTagName('Codigo')->item(0)->textContent;
    }
}