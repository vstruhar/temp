<?php

namespace App\Services;

use DragonBe\Vies\Vies;
use DragonBe\Vies\ViesServiceException;
use Exception;
use Log;

class ValidateVatNumber
{
    protected $supportedCountries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'ES', 'FI', 'FR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU',
        'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'XI',
    ];

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function handle($value)
    {
        $vies = new Vies();

        // skip validation if service is unavailable
        if ($vies->getHeartBeat()->isAlive() === false) {
            throw new Exception('Failed to contact the server');
        }

        $countryCode = substr($value, 0, 2);
        $vatNumber   = substr($value, 2, strlen($value));

        // skip validation if selected country is not supported
        if (!in_array($countryCode, $this->supportedCountries, true)) {
            throw new Exception('Country not supported');
        }

        try {
            $result = $vies->validateVat($countryCode, $vatNumber);
        } catch (ViesServiceException|Exception $e) {
            Log::error('Failed to validate VAT', [
                'message'   => $e->getMessage(),
                'country'   => $countryCode,
                'vatNumber' => $vatNumber,
            ]);
            throw new Exception('Failed validate VAT: ' . $e->getMessage());
        }

        return $result->isValid();
    }
}
