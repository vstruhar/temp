<?php

namespace App\Services;

class CurrencyService
{
    public static $currencies = [
        'EUR' => [
            'symbol'    => '€',
            'name'      => 'Euro',
            'precision' => 2,
        ],
        'CZK' => [
            'symbol'    => 'Kč',
            'name'      => 'Koruna',
            'precision' => 2,
        ],
        'USD' => [
            'symbol'    => '$',
            'name'      => 'Dollar',
            'precision' => 2,
        ],
        'GBP' => [
            'symbol'    => '£',
            'name'      => 'Pound',
            'precision' => 2,
        ],
        'HUF' => [
            'symbol'    => 'Ft',
            'name'      => 'Forint',
            'precision' => 0,
        ],
        'PLN' => [
            'symbol'    => 'zł',
            'name'      => 'Złoty',
            'precision' => 2,
        ],
        'CHF' => [
            'symbol'    => 'CHf',
            'name'      => 'Franc',
            'precision' => 2,
        ],
        'RUB' => [
            'symbol'    => '₽',
            'name'      => 'Ruble',
            'precision' => 2,
        ],
        'CNY' => [
            'symbol'    => '¥',
            'name'      => 'Yuan',
            'precision' => 2,
        ],
        'SEK' => [
            'symbol'    => 'kr',
            'name'      => 'Kronor',
            'precision' => 2,
        ],
    ];

    /**
     * @param ?string $code
     *
     * @return string|null
     */
    public static function getSymbol(?string $code): ?string
    {
        if (isset(self::$currencies[$code])) {
            return self::$currencies[$code]['symbol'];
        }
        return null;
    }

    /**
     * @param string $code
     *
     * @return int|null
     */
    public static function getPrecision(string $code): ?int
    {
        if (isset(self::$currencies[$code])) {
            return self::$currencies[$code]['precision'];
        }
        return null;
    }
}
