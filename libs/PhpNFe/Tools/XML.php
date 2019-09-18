<?php namespace PhpNFe\Tools;

use Illuminate\Support\Str;

class XML extends \DOMDocument
{
    /**
     * @param $xml
     * @return XML
     */
    public static function createByXml($xml)
    {
        $instance = new static('1.0', 'utf-8');
        $instance->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        return $instance;
    }

    /**
     * Retorna informações do XML com base no caminho separado por ponto.
     *
     * @param $key
     * @return XMLGet
     */
    public function get($key)
    {
        if (Str::contains($key, '|')) {
            $opcoes = explode('|', $key);
            foreach ($opcoes as $op) {
                $achado = $this->get($op);
                if (! $achado->isNull()) {
                    return $achado;
                }
            }

            return new XMLGet(null);
        }

        $base = $this;
        $niveis = ($key == '') ? [] : explode('.', $key);

        foreach ($niveis as $i => $nivel) {

            // Verificar se base eh nula
            if (is_null($base)) {
                return new XMLGet(null);
            }

            $elem = $base->getElementsByTagName($nivel);
            $base = ($elem->length > 0) ? $elem->item(0) : null;
        }

        // Retornar o conteúdo do ultimo nível
        if (! is_null($base)) {
            return new XMLGet($base);
        }

        return new XMLGet(null);
    }

    /**
     * Pega a quantidade de duplicatas do xml.
     * @return int
     * @deprecated
     */
    public function getDups()
    {
        return $this->getElementsByTagName('dup')->length;
    }

    /**
     * Pega a propriedade do Dup.
     * @param $contador
     * @return XMLGet
     * @deprecated
     */
    public function getDup($contador)
    {
        $dup = $this->getElementsByTagName('dup')->item($contador);

        return self::createByXml($dup->C14N());
    }

    /**
     * @return XMLGet
     * @deprecated
     */
    public function getChNFe()
    {
        $id = str_replace('NFe', '', $this->getElementsByTagName('infNFe')->item(0)->getAttribute('Id'));

        return new XMLGet(null, $id);
    }

    /**
     * @return XMLGet
     * @deprecated
     */
    public function getChNFeTag($tag, $prefixo = 'NFe')
    {
        $id = str_replace($prefixo, '', $this->getElementsByTagName($tag)->item(0)->getAttribute('Id'));

        return new XMLGet(null, $id);
    }

    /**
     * Monta o nome padrão do arquivo XML e PDF.
     *
     * @param $cnpj
     * @param $serie
     * @param $nnf
     * @param $ext
     * @param string $modelo
     * @return string
     * @deprecated
     */
    public static function makeFileName($cnpj, $serie, $nnf, $ext, $modelo = '-Nfe')
    {
        $nome = $cnpj;
        $nome .= '_S' . str_pad($serie, 3, '0', STR_PAD_LEFT);
        $nome .= '_N' . str_pad($nnf, 9, '0', STR_PAD_LEFT);
        $nome .= $modelo . '.' . $ext;

        return $nome;
    }
}