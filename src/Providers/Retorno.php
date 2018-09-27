<?php namespace PhpNFe\NFSe\Providers;

use PhpNFe\NFSe\Contracts\NfseRetornoInterface;

abstract class Retorno implements NfseRetornoInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
}