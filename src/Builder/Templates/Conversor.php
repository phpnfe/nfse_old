<?php

namespace PhpNFe\NFSe\Builder\Templates;

/**
 * Conversor para a montagem do RPS.
 * Class Conversor.
 */
class Conversor
{
    const CNPJ = 'Cnpj';
    const CPF = 'Cpf';

    protected static $cpfCnpj = [
        'blumenau' => [
            self::CNPJ => 'CNPJ',
            self::CPF => 'CPF',
        ],

        'itajai' => [
            self::CNPJ => 'Cnpj',
            self::CPF => 'Cpf',
        ],

        'balneariocamboriu' => [
            self::CNPJ => 'Cnpj',
            self::CPF => 'Cpf',
        ],
    ];

    /**
     * Converter para cada cidade.
     *
     * @param $cpfcnpj
     * @param $cidade
     *
     * @return mixed
     */
    public static function converter($cpfcnpj, $cidade)
    {
        return self::$cpfCnpj[$cidade][$cpfcnpj];
    }
}