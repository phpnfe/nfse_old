<?php namespace PhpNFe\NFSe\Prefeituras\Blumenau;

use PhpNFe\Tools\Certificado\Certificado;

/**
 * Classe para as assinaturas do xml no padrão Blumenau.
 * Class Sign.
 */
class Sign
{
    public static function sign(\DOMDocument $xmlDoc, Certificado $cert)
    {
        $root = $xmlDoc->documentElement;
        // DigestValue is a base64 sha1 hash with root tag content without Signature tag
        $digestValue = base64_encode(hash('sha1', $root->C14N(false, false, null, null), true));
        $signature = $xmlDoc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
        $root->appendChild($signature);
        $signedInfo = $xmlDoc->createElement('SignedInfo');
        $signature->appendChild($signedInfo);
        $newNode = $xmlDoc->createElement('CanonicalizationMethod');
        $signedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $newNode = $xmlDoc->createElement('SignatureMethod');
        $signedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $reference = $xmlDoc->createElement('Reference');
        $signedInfo->appendChild($reference);
        $reference->setAttribute('URI', '');
        $transforms = $xmlDoc->createElement('Transforms');
        $reference->appendChild($transforms);
        $newNode = $xmlDoc->createElement('Transform');
        $transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $newNode = $xmlDoc->createElement('Transform');
        $transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $newNode = $xmlDoc->createElement('DigestMethod');
        $reference->appendChild($newNode);
        $newNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $newNode = $xmlDoc->createElement('DigestValue', $digestValue);
        $reference->appendChild($newNode);
        // SignedInfo Canonicalization (Canonical XML)
        $signedInfoC14n = $signedInfo->C14N(false, false, null, null);
        // SignatureValue is a base64 SignedInfo tag content
        $signatureValue = '';
        $pkeyId = openssl_get_privatekey($cert->getChavePri());
        openssl_sign($signedInfoC14n, $signatureValue, $pkeyId);
        $newNode = $xmlDoc->createElement('SignatureValue', base64_encode($signatureValue));
        $signature->appendChild($newNode);
        $keyInfo = $xmlDoc->createElement('KeyInfo');
        $signature->appendChild($keyInfo);
        $x509Data = $xmlDoc->createElement('X509Data');
        $keyInfo->appendChild($x509Data);
        $newNode = $xmlDoc->createElement('X509Certificate', static::zCleanPubKey($cert->getChavePub()));
        $x509Data->appendChild($newNode);
        openssl_free_key($pkeyId);
    }

    /**
     * Assinatura adicional do rps.
     * @param \DOMDocument $dom
     * @param Certificado $cert
     */
    public static function signRPS(\DOMDocument $dom, Certificado $cert)
    {
        $chaveNode = $dom->getElementsByTagName('ChaveRPS')->item(0);

        // Montar cadeia de informacoes do rps
        $cadeia = '';

        $cadeia .= str_pad($chaveNode->getElementsByTagName('InscricaoPrestador')->item(0)->textContent, 8, '0', STR_PAD_LEFT);
        $cadeia .= str_pad($chaveNode->getElementsByTagName('SerieRPS')->item(0)->textContent, 5, ' ', STR_PAD_RIGHT);
        $cadeia .= str_pad($chaveNode->getElementsByTagName('NumeroRPS')->item(0)->textContent, '12', '0', STR_PAD_LEFT);
        $cadeia .= str_replace('-', '', $dom->getElementsByTagName('DataEmissao')->item(0)->textContent);
        $cadeia .= $dom->getElementsByTagName('TributacaoRPS')->item(0)->textContent;
        $cadeia .= $dom->getElementsByTagName('StatusRPS')->item(0)->textContent;
        $cadeia .= ($dom->getElementsByTagName('ISSRetido')->item(0)->textContent) ? 'N' : 'S';
        $cadeia .= static::formatar($dom->getElementsByTagName('ValorServicos')->item(0)->textContent);
        $cadeia .= static::formatar($dom->getElementsByTagName('ValorDeducoes')->item(0)->textContent);
        $cadeia .= str_pad($dom->getElementsByTagName('CodigoServico')->item(0)->textContent, 5, '0', STR_PAD_LEFT);

        //Pegar Indicador de CPF||CNPJ
        $node = $dom->getElementsByTagName('CPFCNPJTomador')->item(0);
        if ($node != null) {
            if ($node->getElementsByTagName('CNPJ')->item(0) != null) {
                $ind = '2';
            } else {
                $ind = '1';
            }
        } else {
            $ind = '3';
        }

        $cadeia .= $ind;

        $cadeia .= sprintf('%014s', trim($dom->getElementsByTagName('CPFCNPJTomador')->item(0)->nodeValue == null
            ? '00000000000000' : $dom->getElementsByTagName('CPFCNPJTomador')->item(0)->nodeValue));

        // Gerar o hash e assinar o mesmo com o certificado passado por parâmetro
        //(openssl_sign)
        $ass = '';
        $pkeyId = openssl_get_privatekey($cert->getChavePri());
        openssl_sign($cadeia, $ass, $pkeyId, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyId);

        // Passar o hash assinado para base64
        $ass = base64_encode($ass);

        // Inserindo o node com a assinatura no rps passado por parâmetro
        $rpsNode = $dom->getElementsByTagName('RPS')->item(0);
        $signNode = $dom->createElement('Assinatura', $ass);
        $refNode = $rpsNode->firstChild;
        $rpsNode->insertBefore($signNode, $refNode);
    }

    /**
     * Assinatura adicional de cancelamento para o rps.
     * @param \DOMDocument $dom
     * @param Certificado $cert
     */
    public static function signCanc(\DOMDocument $dom, Certificado $cert)
    {
        $cadeia = '';

        $cadeia .= str_pad($dom->getElementsByTagName('InscricaoPrestador')->item(0)->textContent, 8, '0', STR_PAD_LEFT);
        $cadeia .= str_pad($dom->getElementsByTagName('NumeroNFe')->item(0)->textContent, 12, '0', STR_PAD_LEFT);

        $ass = '';
        $pkeyId = openssl_get_privatekey($cert->getChavePri());
        openssl_sign($cadeia, $ass, $pkeyId, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyId);

        // Passar o hash assinado para base64
        $ass = base64_encode($ass);

        // Inserindo o node com a assinatura no rps passado por parâmetro
        $rpsNode = $dom->getElementsByTagName('Detalhe')->item(0);
        $signNode = $dom->createElement('AssinaturaCancelamento', $ass);
        $rpsNode->appendChild($signNode);
    }

    /**
     * Formatar valores.
     * @param $valor
     * @return mixed|string
     */
    private static function formatar($valor)
    {
        // Aplicar o number_format
        $valor = number_format($valor, 2);

        // Tirar pontos e vírgulas
        $valor = preg_replace('/[.,]/', '', $valor);

        // Aplicar o str_pad
        $valor = str_pad($valor, 15, '0', STR_PAD_LEFT);

        return $valor;
    }

    /**
     * zCleanPubKey
     * Remove a informação de inicio e fim do certificado
     * contido no formato PEM, deixando o certificado (chave publica) pronta para ser
     * anexada ao xml da NFe.
     * @return string contendo o certificado limpo
     */
    private static function zCleanPubKey($pubKey)
    {
        //inicializa variavel
        $data = '';
        //carregar a chave publica
        //carrega o certificado em um array usando o LF como referencia
        $arCert = explode("\n", $pubKey);
        foreach ($arCert as $curData) {
            //remove a tag de inicio e fim do certificado
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 &&
                strncmp($curData, '-----END CERTIFICATE', 20) != 0
            ) {
                //carrega o resultado numa string
                $data .= trim($curData);
            }
        }

        return $data;
    }
}