<?php namespace PhpNFe\NFSe;

use Illuminate\Support\Arr;
use PhpNFe\NFSe\Builder\Rps;
use PhpNFe\NFSe\Builder\RCancela;
use PhpNFe\NFSe\Contracts\NfseProviderInterface;
use PhpNFe\NFSe\Contracts\NfseRetornoInterface;
use PhpNFe\Tools\Certificado\Certificado;

class NFSe
{
    /**
     * Lista de municipios implementados.
     *
     * @var array
     */
    protected $municipios = [];

    /**
     * Classe de controle do certificado.
     *
     * @var Certificado
     */
    protected $certificado;

    /**
     * @var
     */
    protected $config;

    /**
     * @param Certificado $cert
     * @param array $config
     */
    public function __construct(Certificado $cert, $config = [])
    {
        $this->certificado = $cert;
        $this->config = $config;
    }

    /**
     * Autorizar um RPS em NFSe.
     *
     * @param Rps $rps
     * @param $amb
     * @return NfseRetornoInterface
     */
    public function autorizar(Rps $rps, $amb)
    {
        return $this->getMunicipio($rps->codMun)->autorizar($rps, $amb);
    }

    /**
     * Cancelar uma NFSe emitida.
     *
     * @param RCancela $can
     * @param $amb
     * @return NfseRetornoInterface
     */
    public function cancela(RCancela $can, $amb)
    {
        return $this->getMunicipio($can->codMun)->cancela($can, $amb);
    }

    /**
     * Adicionar implementacao do municipio.
     *
     * @param $codMun
     * @param $callback
     */
    public function extend($codMun, $callback)
    {
        $this->municipios[$codMun] = $callback;
    }

    /**
     * Carregar provider do municipio.
     *
     * @param $codMun
     * @return NfseProviderInterface
     * @throws \Exception
     */
    protected function getMunicipio($codMun)
    {
        // Verificar se municipio foi implementado
        if (! array_key_exists($codMun, $this->municipios)) {
            throw new \Exception("Municipio [$codMun] nao foi implementado [NFSe]");
        }

        // Verificar se municipio jah foi carregado
        $callback = $this->municipios[$codMun];
        if ($callback instanceof NfseProviderInterface) {
            return $callback;
        }

        // Carregar implementacao do municipio
        return $this->municipios[$codMun] = call_user_func_array($callback, [$this]);
    }

    /**
     * Retorna o manipulador do certificado.
     *
     * @return Certificado
     */
    public function getCert()
    {
        return $this->certificado;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}