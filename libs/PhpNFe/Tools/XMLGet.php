<?php namespace PhpNFe\Tools;

use Carbon\Carbon;

class XMLGet
{
    /**
     * @var \DOMElement
     */
    protected $elem;

    /**
     * @var string
     */
    protected $value = null;

    /**
     * XMLGet constructor.
     * @param $elem
     * @param $value
     */
    public function __construct($elem, $value = null)
    {
        $this->elem = $elem;
        $this->value = $value;
    }

    /**
     * Retorna o valor.
     *
     * @return string
     */
    public function value()
    {
        if (is_null($this->value)) {
            if (is_null($this->elem)) {
                $this->value = '';
            } else {
                $this->value = $this->elem->textContent;
            }
        }

        return $this->value;
    }

    /**
     * Aplicar PAD.
     *
     * @param $num
     * @param string $char
     * @param int $dir
     * @return $this
     */
    public function pad($num, $char = '0', $dir = STR_PAD_LEFT)
    {
        $this->value = str_pad($this->value(), $num, $char, $dir);

        return $this;
    }

    /**
     * Aplica formatação numérica.
     *
     * @param $dec
     * @return $this
     */
    public function number($dec, $zeroNull = true)
    {
        $val = floatval($this->value());
        if (($val == 0) && ($zeroNull)) {
            $this->value = '';
        } else {
            $this->value = number_format($val, $dec, ',', '.');
        }

        return $this;
    }

    /**
     * Retorna o texto certo para o valor atribuído ao frete.
     * @param $valor
     * @return string
     */
    public function frete()
    {
        switch ($this->value()) {
            case '0':
                $this->value = '0 - EMIT';
                break;
            case '1':
                $this->value = '1 - DEST/REM';
                break;
            case '2':
                $this->value = '2 - TERC';
                break;
            case '9':
                $this->value = '3 - S/F';
                break;
            default:
                $this->value = '';
                break;
        }

        return $this;
    }

    /**
     * Aplica a mascara de formação do value.
     * @param $format
     * @return $this
     */
    public function format($format)
    {
        $string = str_replace(' ', '', $this->value());
        for ($i = 0; $i < strlen($string); $i++) {
            $pos = strpos($format, '#');

            // verificar se string eh maior que a qtdade de #.
            if ($pos === false) {
                break;
            }
            $format[$pos] = $string[$i];
        }

        // verificar se sobrou # para trocar
        if (strpos($format, '#') !== false) {
            $format = $this->value();
        }

        $this->value = $format;

        return $this;
    }

    /**
     * @param $format
     * @return $this
     */
    public function datetime($format, $formatInput = false)
    {
        if ($this->value() != '') {
            $formatInput = ($formatInput !== false) ? $formatInput : Carbon::ATOM;
            $dt = Carbon::createFromFormat($formatInput, $this->value());

            $this->value = $dt->format($format);
        }

        return $this;
    }

    /**
     * @param $elem
     * @return array
     */
    public function toArray($elem)
    {
        if (is_null($this->elem)) {
            return [];
        }

        $ret = [];
        $lista = $this->elem->getElementsByTagName($elem);
        for ($i = 0; $i < $lista->length; $i++) {
            $ret[] = XML::createByXml($lista->item($i)->C14N());
        }

        return $ret;
    }

    /**
     * Retorna o primeiro child.
     *
     * @return XML
     */
    public function first()
    {
        return XML::createByXml($this->elem->firstChild->C14N());
    }

    /**
     * Retorna se elemento eh nulo.
     *
     * @return bool
     */
    public function isNull()
    {
        return is_null($this->elem);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value();
    }
}