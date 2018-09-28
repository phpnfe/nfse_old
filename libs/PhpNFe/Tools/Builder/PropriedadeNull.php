<?php namespace PhpNFe\Tools\Builder;

class PropriedadeNull
{
    /**
     * @var string
     */
    protected $__class;

    /**
     * @var object
     */
    protected $__obj;

    public function __construct($class)
    {
        $this->__class = $class;
    }

    protected function __load()
    {
        if (is_null($this->__obj)) {
            return $this->__obj = new $this->__class();
        }

        return $this->__obj;
    }

    public function isNull()
    {
        return is_null($this->__obj);
    }

    public function getObject()
    {
        return $this->__obj;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->__load(), $name], $arguments);
    }

    public function &__get($name)
    {
        return $this->__load()->{$name};
    }

    public function __set($name, $value)
    {
        $this->__load()->{$name} = $value;
    }
}