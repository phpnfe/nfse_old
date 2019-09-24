<?php

echo "Carregar certificado\r\n";
echo "------------------------------------------------\r\n";
try {
// Carregar certificado
    $cert = new \PhpNFe\Tools\Certificado\Certificado();
    $cert->carregarPfx(__DIR__ . '/cristoluz_2018-2019.p12', '220680');

// Carregar CNPJ
        $cnpj = $cert->getCNPJ();
        echo "CNPJ certificado: $cnpj\r\n";

    // Carregar Vencimento
        $val = $cert->getValidade()->format('d/m/Y H:i:s');
        echo "Validade certificado: $val\r\n";

        if (! $cert->ehValido()) {
            throw new Exception("Certificado Cristoluz vencido");
        }
    echo "------------------------------------------------\r\n";
} catch (Exception $e)
{
    echo "------------------------------------------------\r\n";
    echo ">>> " . $e->getMessage() . "\r\n";
}