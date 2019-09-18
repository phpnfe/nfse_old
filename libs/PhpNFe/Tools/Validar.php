<?php namespace PhpNFe\Tools;

use DOMDocument;

class Validar
{
    /**
     * Valida um xml assinado.
     *
     * @param $xml
     * @param $schemaFile
     * @return bool
     * @throws \Exception
     */
    public static function validar($xml, $schemaFile)
    {
        //Para poder pegar os erros caso houver
        libxml_use_internal_errors(true);
        libxml_clear_errors();

        if (is_file($xml)) {
            $xml = file_get_contents($xml);
        }

        $dom = new DOMDocument('1.0', 'utf-8');

        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        if (! $dom->schemaValidate($schemaFile)) {
            $errors = libxml_get_errors();
            $returnErrors = [];
            foreach ($errors as $error) {
                $returnErrors[] = $error->message . 'at line ' . $error->line;
            }

            $msg = "Erro ao validar XML:\r\n" . implode("\r\n", $returnErrors);

            throw new \Exception($msg);
        }

        return true;
    }
}