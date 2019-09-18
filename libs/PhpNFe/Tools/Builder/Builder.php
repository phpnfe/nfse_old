<?php namespace PhpNFe\Tools\Builder;

use Carbon\Carbon;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlockFactory;

abstract class Builder
{
    protected function geraXmlPropriedades()
    {
        $xml = '';

        $props = get_object_vars($this);
        foreach ($props as $key => $value) {
            $xml .= $this->geraXmlPropriedade($key, $value);
        }

        return $xml;
    }

    protected function geraXmlPropriedade($nome, $valor)
    {
        // Se for array, trocar por Coleções.
        if (is_array($valor)) {
            if (count($valor) == 0) {
                return '';
            }

            $valor = new Colecoes($valor, get_class($valor[0]));
        }

        // Se for Colecoes
        if ($valor instanceof Colecoes) {
            $xml = '';
            $attrIndex = $valor->getAttrIndex();
            foreach ($valor->getItems() as $i => $item) {
                if ($item instanceof self) {
                    $attr = ($attrIndex != '') ? sprintf(' %s="%s"', $attrIndex, ($i + 1)) : '';

                    $xml .= sprintf('<%s%s>', $nome, $attr);
                    $xml .= $item->geraXmlPropriedades();
                    $xml .= sprintf('</%s>', $nome);
                }
            }

            return $xml;
        }

        // Se for PropriedadeNull
        if ($valor instanceof PropriedadeNull) {
            $valor = $valor->getObject();
        }

        // Se for Builder
        if ($valor instanceof self) {
            $xml = sprintf('<%s>', $nome);
            $xml .= $valor->geraXmlPropriedades();
            $xml .= sprintf('</%s>', $nome);

            return $xml;
        }

        // igonorar outros objetos, não Builder
        if (is_object($valor) && (! ($valor instanceof \DateTime))) {
            return '';
        }

        // Ignorar campos nulos
        if (is_null($valor)) {
            return '';
        }

        $valor = $this->formatar($nome, $valor);

        return sprintf('<%s>%s</%s>', $nome, $valor, $nome);
    }

    /**
     * Formatar float para o número de casas decimais correto.
     * @param $nome
     * @param $valor
     * @return string
     */
    protected function formatar($nome, $valor)
    {
        $ref = new \ReflectionProperty(get_class($this), $nome);
        $factory = DocBlockFactory::createInstance();
        $info = $factory->create($ref->getDocComment());

        // Pegar o tipo da variavel
        $tipo = $info->getTagsByName('var');
        $tipo = (count($tipo) == 0) ? 'string' : $tipo[0]->getType();
        $tipo = strtolower($tipo);

        switch ($tipo) {

            case 'string':
            case 'string|null':
            case '\string':
            case '\string|null':
                $valor = str_replace(['@'], ['#1#'], utf8_encode($valor)); // Ignorar alguns caracteres no ascii
                $valor = Str::ascii($valor);
                $valor = str_replace(['&'], ['e'], $valor);
                $valor = str_replace(['#1#'], ['@'], $valor); // Retornar caracteres ignorados
                $valor = Str::upper($valor);
                $valor = trim($valor);

                // Max
                $max = $info->getTagsByName('max');
                if (count($max) > 0) {
                    $max = intval($max[0]->getDescription()->render());
                    $valor = trim(substr($valor, 0, $max));
                }

                return $valor;

            case 'float|null':
            case 'float':
                $dec = $info->getTagsByName('dec');
                if (count($dec) == 0) {
                    return $valor;
                }

                // Valor do @dec
                $dec = $dec[0]->getDescription()->render();

                return number_format($valor, $dec, '.', '');

            case 'datetime':
            case '\datetime':
            case '\datetime|null':
            case 'date':
            case 'date|null':
            case '\date':
            case '\date|null':
                if (is_int($valor)) {
                    $valor = Carbon::createFromTimestamp($valor);
                }

                if (is_string($valor)) {
                    return $valor;
                }

                $format = in_array($tipo, ['date', 'date|null', '\date', '\date|null']) ? 'Y-m-d' : Carbon::ATOM;

                return $valor->format($format);

            default:
                return $valor;
        }
    }

    /**
     * Verificar se o objeto eh vazio.
     * @param $valor
     * @return bool
     */
    protected function objEhVazio($valor)
    {
        if (is_object($valor)) {
            return $this->verificaObj($valor);
        } else {
            return true;
        }
    }
}