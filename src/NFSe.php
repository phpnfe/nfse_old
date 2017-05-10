<?php namespace PhpNFe\NFSe;

use PhpNFe\Tools\XML;
use PhpNFe\Tools\Validar;
use PhpNFe\NFSe\Prefeituras\Config;
use PhpNFe\NFSe\Prefeituras\Retorno;
use Illuminate\Filesystem\Filesystem;
use PhpNFe\NFSe\Prefeituras\Roteador;
use PhpNFe\Tools\Certificado\Certificado;

/**
 * Class NFSe.
 */
class NFSe
{
    const version = 'NetForce EmiteNFS-e';

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
     * @var Filesystem
     */
    protected $file;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Objeto para distribuição das cidades.
     * @var Roteador
     */
    protected $roteador;

    public function __construct(Certificado $cert, $codMun)
    {
        $this->certificado = $cert;
        $this->file = new Filesystem();
        $this->roteador = new Roteador($codMun, $this->certificado);
        $this->codMun = $codMun;
    }

    /**
     * @param $rps
     * @param $tpAmb
     * @param $metodo Config::mtAutoriza|Config::mtCancela
     * @return Retorno
     * @throws \Exception
     */
    public function enviar($rps, $tpAmb, $metodo)
    {
        $config = Config::getMethodInfo($tpAmb, $this->codMun, $metodo);

        return $this->roteador->retorno($this->codMun, $rps, $config);
    }

    /**
     * Autorizar.
     * @param $rps
     * @param $tpAmb
     * @return Retorno
     */
    public function autorizar($rps, $tpAmb)
    {
        return $this->enviar($rps, $tpAmb, Config::mtAutoriza);
    }

    /**
     * Cancelar.
     * @param $numNFse
     * @param $cnpj
     * @param $inscMun
     * @param $codMun
     * @param $motivo
     * @param $tpAmb
     * @return Retorno
     */
    public function cancela($numNFse, $cnpj, $inscMun, $codMun, $motivo, $tpAmb)
    {
        $xml = $this->roteador->montarCancela($numNFse, $cnpj, $inscMun, $codMun, $motivo);

        return $this->enviar($xml, $tpAmb, Config::mtCancela);
    }

    /**
     * Validar um xml.
     * @param $xml
     * @param $metodo
     * @return array|bool
     */
    public function validarXML($xml, $metodo)
    {
        $path = __DIR__ . '/Prefeituras/' . Config::getCidade($this->codMun) . '/schemas/' . Config::getSchema($this->codMun, $metodo) . '.xsd';

        return Validar::validar($xml, $path);
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
        // Descorbri o nome da cidade
        $cidade = strtolower($this->roteador->distribuir($this->codMun));

        switch ($cidade) {

            case 'blumenau':
                $dom = XML::createByXml($xml);
                \PhpNFe\NFSe\Prefeituras\Blumenau\Sign::signRPS($dom, $this->certificado);
                \PhpNFe\NFSe\Prefeituras\Blumenau\Sign::sign($dom, $this->certificado);

                return $dom->saveXML();

            case 'itajai':
                return $this->certificado->assinarXML($xml, 'InfRps');

            default:
                throw new \Exception('Cidade nao implementada %s para assinar', $cidade);
                break;
        }
    }
}