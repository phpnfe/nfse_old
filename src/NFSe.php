<?php namespace PhpNFe\NFSe;

use PhpNFe\NFSe\Builder\Rps;
use PhpNFe\Tools\XML;
use PhpNFe\NFSe\Modelos\Modelo;
use PhpNFe\NFSe\Modelos\Retorno;
use PhpNFe\Tools\Certificado\Certificado;

/**
 * Class NFSe.
 */
class NFSe
{
    const version = 'NetForce EmiteNFS-e';

    const mtdAutorizar = 'autorizar';
    const mtdCancelar  = 'cancelar';

    const ambProducao    = 'producao';
    const ambHomologacao = 'homologacao';

    /**
     * Classe de controle do certificado.
     * @var Certificado
     */
    protected $certificado;

    /**
     * Codigo do Municipio.
     * @var
     */
    protected $codMun;

    /**
     * @var Modelo
     */
    protected $modelo;

    /**
     * @param Certificado $cert
     * @param $codMun
     */
    public function __construct(Certificado $cert, $codMun)
    {
        $this->certificado = $cert;
        $this->codMun = $codMun;

        $this->loadModelo();
    }

    /**
     * Carregar modelo pelo codigo do municipio.
     *
     * @return Modelo
     * @throws \Exception
     */
    protected function loadModelo()
    {
        $class = 'PhpNFe\NFSe\Prefeituras\M' . $this->codMun . '\Modelo';
        if (! class_exists($class)) {
            throw new \Exception("Modelo do municipio [$this->codMun] nao implementado");
        }

        return $this->modelo = new $class($this->codMun, $this->certificado);
    }

    /**
     * Inciiar manipulador da RPS.
     *
     * @return Rps
     */
    public function makeRps()
    {
        return new Rps($this->codMun);
    }

    /**
     * Autorizar.
     *
     * @param $xml
     * @param $amb
     * @return Retorno
     */
    public function autorizar($xml, $amb)
    {
        return $this->modelo->autorizar($xml, $amb);
    }

    /**
     * Cancelar.
     *
     * @param $numNFse
     * @param $cnpj
     * @param $inscMun
     * @param $motivo
     * @param $tpAmb
     * @return Retorno
     */
    public function cancela($numNFse, $cnpj, $inscMun, $motivo, $amb)
    {
        return $this->modelo->cancelar($numNFse, $cnpj, $inscMun, $motivo, $amb);
    }

    /**
     * Validar um xml.
     *
     * @param $xml
     * @param $metodo
     * @return array|bool
     */
    public function validarXML($xml, $metodo)
    {
        return $this->modelo->validarXML($xml, $metodo);
    }

    /**
     * Assinar XML do RPS.
     *
     * @param $xml
     * @return string
     * @throws \Exception
     */
    public function assinar($xml)
    {
        return $this->modelo->assinar($xml);
    }
}