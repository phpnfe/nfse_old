<?php namespace PhpNFe\NFSe\Providers;

use PhpNFe\NFSe\NFSe;
use Illuminate\Filesystem\Filesystem;
use PhpNFe\NFSe\Contracts\NfseProviderInterface;

abstract class Provider implements NfseProviderInterface
{
    /**
     * @var NFSe
     */
    protected $nfse;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @param NFSe $nfse
     */
    public function __construct(NFSe $nfse)
    {
        $this->nfse = $nfse;
        $this->files = new Filesystem();
    }

    /**
     * Preparar certificados na pasta temp.
     *
     * @param $callback
     * @return mixed
     */
    protected function callWithCertPath($callback)
    {
        // Criar diretorio temporario.
        $certPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $this->files->makeDirectory($certPath);
        try {
            // Salvar certificados na pasta temp criada.
            $this->nfse->getCert()->salvaChave($certPath);

            return call_user_func_array($callback, [$certPath]);
        } finally {
            $this->files->deleteDirectory($certPath);
        }
    }

    /**
     * Retorno o path do arquivo do wsdl.
     *
     * @param $codMun
     * @param $amb
     * @return string
     * @throws \Exception
     */
    protected function getWsdlPath($codMun, $amb)
    {
        $file = __DIR__ . "/../Municipios/M$codMun/wsdl-$amb.xml";
        if (! $this->files->exists($file)) {
            throw new \Exception("WSDL do municipio [$codMun] do ambiente [$amb] nao foi encontrado");
        }

        return realpath($file);
    }
}