<?php namespace PhpNFe\Tools\Builder;

class Colecoes
{
    /**
     * @var array
     */
    protected $array = [];

    /**
     * Nome do atributo index.
     * @var string
     */
    protected $attrIndex = '';

    /**
     * Colecoes constructor.
     * @param array $array
     * @param $classe
     * @param string $attrIndex
     */
    public function __construct(array $array, $attrIndex = '')
    {
        $this->array = $array;
        $this->attrIndex = $attrIndex;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->array;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function add($item)
    {
        return $this->array[] = $item;
    }

    /**
     * @return string
     */
    public function getAttrIndex()
    {
        return $this->attrIndex;
    }
}