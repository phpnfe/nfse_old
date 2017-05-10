<?php namespace PhpNFe\NFSe\Prefeituras;

use Illuminate\Support\Arr;

class MethodConfig
{
    public $cMun = '';
    public $amb = '';
    public $method = '';
    public $operation = '';
    public $version = '';
    public $url = '';
    public $configs = [];

    public function __construct(array $info)
    {
        $this->cMun = Arr::get($info, 'cMun');
        $this->amb = Arr::get($info, 'amb');
        $this->method = Arr::get($info, 'method');
        $this->operation = Arr::get($info, 'op');
        $this->version = Arr::get($info, 'versao');
        $this->url = Arr::get($info, 'url');
        $this->configs = Arr::get($info, 'configs');
    }
}