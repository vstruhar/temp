<?php

namespace App\Services;

class CountriesService
{
    public static $countries = [
        'AL' => 'Albania',
        'AD' => 'Andorra',
        'AM' => 'Armenia',
        'AT' => 'Austria',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BA' => 'Bosnia and Herzegovina',
        'BG' => 'Bulgaria',
        'HR' => 'Croatia',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'EE' => 'Estonia',
        'FO' => 'Faroe Islands',
        'FI' => 'Finland',
        'FR' => 'France',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'HU' => 'Hungary',
        'IE' => 'Ireland',
        'IT' => 'Italy',
        'LV' => 'Latvia',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MK' => 'Macedonia',
        'MT' => 'Malta',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'ME' => 'Montenegro',
        'NL' => 'Netherlands',
        'NO' => 'Norway',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RS' => 'Serbia',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'ES' => 'Spain',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'TR' => 'Turkey',
        'UA' => 'Ukraine',
        'GB' => 'United Kingdom',
        'US' => 'United States of America',
    ];

    /**
     * @return array
     */
    public static function dropdownOptions(): array
    {
        return collect(self::$countries)
            ->map(static function ($country, $key) {
                return ['value' => $key, 'label' => __($country)];
            })
            ->sortBy('label')
            ->values()
            ->toArray();
    }

    /**
     * @param  string  $code
     * @param  bool  $translated
     *
     * @return string
     */
    public static function getName(string $code, $translated = true): string
    {
        $countryName = self::$countries[$code] ?? $code;

        if (!$translated) {
            return $countryName;
        }

        return __($countryName);
    }
}
