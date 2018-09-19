<?php namespace PhpNFe\NFSe\Modelos;

use PhpNFe\NFSe\NFSe;
use PhpNFe\Tools\Validar;
use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use PhpNFe\Tools\Certificado\Certificado;

abstract class Modelo
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $version;

    /**
     * Codigo do Municipio.
     * @var string
     */
    protected $codMun;

    /**
     * @var Certificado
     */
    protected $cert;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param $codMun
     * @param $version
     * @param Certificado $cert
     */
    public function __construct($codMun, Certificado $cert)
    {
        $this->codMun = $codMun;
        $this->cert = $cert;

        $this->files = new Filesystem();
    }

    /**
     * Enviar requisicao.
     *
     * @param $metodo
     * @param $amb
     * @param $xml
     * @return Retorno
     */
    protected function enviar($metodo, $amb, $xml)
    {
        // Criar diretorio temporario.
        $certDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $this->files->makeDirectory($certDir);
        try {
            // Salvar chaves no diretorio temp
            $this->cert->salvaChave($certDir);

            // Executar comando de envio
            return $this->enviarMetodo($metodo, $amb, $xml, $certDir);
        } finally {
            $this->files->deleteDirectory($certDir);
        }
    }

    /**
     * @param $metodo
     * @param $amb
     * @param $xml
     * @param $certDir
     * @return Retorno
     */
    abstract protected function enviarMetodo($metodo, $amb, $xml, $certDir);

    /**
     * Enviar para autorizar RPS.
     *
     * @param $xml
     * @param $amb
     * @return Retorno
     */
    public function autorizar($xml, $amb)
    {
        return $this->enviar(NFSe::mtdAutorizar, $amb, $xml);
    }

    /**
     * Enviar para cancelar NFSe.
     *
     * @param $numNFse
     * @param $cnpj
     * @param $inscMun
     * @param $motivo
     * @param $amb
     * @return Retorno
     */
    public function cancelar($numNFse, $cnpj, $inscMun, $motivo, $amb)
    {
        $xml = $this->getXmlcancelar($numNFse, $cnpj, $inscMun, $motivo, $amb);

        return $this->enviar(NFSe::mtdCancelar, $amb, $xml);
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
    abstract public function getXmlcancelar($numNFse, $cnpj, $inscMun, $motivo, $amb);

    /**
     * Retorna o arquivo de schema para validacao.
     *
     * @param $metodo
     * @return string
     */
    abstract public function getSchemaFile($metodo);

    /**
     * Retorna o arquivo de WSDL.
     *
     * @param $amb
     * @return string
     */
    abstract public function getWsdlFile($amb);

    /**
     * Validar XML pelo Schema.
     *
     * @param $xml
     * @param $metodo
     * @return mixed
     */
    public function validarXML($xml, $metodo)
    {
        $arquivo_schema = $this->getSchemaFile($metodo);

        return Validar::validar($xml, $arquivo_schema);
    }

    /**
     * Assinar XML para envio.
     *
     * @param $xml
     * @return string
     */
    abstract public function assinar($xml);

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Carregar configuracoes.
     *
     * @param $dirModelo
     */
    protected function loadConfig($dirModelo)
    {
        $this->config = [];

        // Carregar config do modelo
        $arquivo = $dirModelo . '/config.php';
        if ($this->files->exists($arquivo)) {
            $this->config = $this->files->getRequire($arquivo);
        }

        // Carregar config do municipio
        $arquivo = __DIR__ . '/../Prefeituras/M' . $this->codMun . '/config.php';
        if ($this->files->exists($arquivo)) {
            $mun_config = $this->files->getRequire($arquivo);
            $this->config = array_merge([], $this->config, $mun_config);
        }
    }
}