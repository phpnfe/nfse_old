<?php namespace PhpNFe\NFSe\Modelos\Publica;

use PhpNFe\NFSe\NFSe;
use PhpNFe\NFSe\Modelos\Modelo;
use PhpNFe\NFSe\Modelos\Retorno;
use PhpNFe\Tools\Certificado\Certificado;

class PublicaModelo extends Modelo
{
    /**
     * @param $codMun
     * @param Certificado $cert
     */
    public function __construct($codMun, Certificado $cert)
    {
        parent::__construct($codMun, $cert);

        $this->loadConfig(__DIR__);
    }

    /**
     * @param $metodo
     * @param $amb
     * @param $xml
     * @param $certDir
     * @return Retorno
     */
    protected function enviarMetodo($metodo, $amb, $xml, $certDir)
    {
        $params = [
            'local_cert' => $certDir . '/cert.key',
            'trace' => true,
        ];

        $method = $this->config("metodos.$amb.$metodo.method");
        if (is_null($method)) {
            throw new \Exception("Metodo [$metodo] nao definido no config da publica");
        }

        $wsdl = $this->getWsdlFile($amb);

        $soap = new \SoapClient($wsdl, $params);

        return new PublicaRetorno($soap->$method($xml));
    }

    /**
     * Retorna o arquivo de schema para validacao.
     *
     * @param $metodo
     * @return string
     */
    public function getSchemaFile($metodo)
    {
        $metodos = [
            NFSe::mtdAutorizar => 'schema_nfse',
        ];

        $mtd = array_key_exists($metodo, $metodos) ? $metodos[$metodo] : 'xxx';

        $arquivo = str_replace(['{version}','{metodo}'], [$this->version, $mtd], '/schemas/{version}/{metodo}_{version}.xsd');
        $arquivo = __DIR__ . $arquivo;
        if (! $this->files->exists($arquivo)) {
            throw new \Exception("Schema [$metodo] nao encontrado. [Padrao Publica]");
        }

        return $arquivo;
    }

    /**
     * Retorna o arquivo de WSDL.
     *
     * @param $amb
     * @return string
     */
    public function getWsdlFile($amb)
    {
        $arquivo = str_replace(['{amb}','{version}'], [$amb, $this->version], '/wsdl/{version}/wsdl-{amb}.xml');
        $arquivo = __DIR__ . $arquivo;
        if (! $this->files->exists($arquivo)) {
            throw new \Exception("WSDL [$amb] nao encontrado. [Padrao Publica]");
        }

        return $arquivo;
    }

    /**
     * Assinar XML para envio.
     *
     * @param $xml
     * @return string
     */
    public function assinar($xml)
    {
        return $this->cert->assinarXML($xml, 'InfRps');
    }

    /**
     * Nontar XML do cancelamento.
     *
     * @param $numNFse
     * @param $cnpj
     * @param $inscMun
     * @param $motivo
     * @param $amb
     * @return string
     */
    public function getXmlcancelar($numNFse, $cnpj, $inscMun, $motivo, $amb)
    {
        $modelo = __DIR__ . '/template_cancela.xml';
        if (! $this->files->exists($modelo)) {
            throw new \Exception("Template do cancela da publica nao foi encontrado");
        }

        $xml = $this->files->get($modelo);
        $xml = str_replace('{{numNfse}}', $numNFse, $xml);
        $xml = str_replace('{{cnpj}}', $cnpj, $xml);
        $xml = str_replace('{{inscMun}}', $inscMun, $xml);
        $xml = str_replace('{{codMun}}', $this->codMun, $xml);
        $xml = str_replace('{{motivo}}', $motivo, $xml);

        // Assinar XML
        $xml = $this->cert->assinarXML($xml, 'InfPedidoCancelamento');

        return $xml;
    }
}