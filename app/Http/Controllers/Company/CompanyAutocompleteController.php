<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Hubipe\FinStat\Exception\ResponseException;
use Hubipe\FinStat\FinStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class CompanyAutocompleteController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|min:3',
            'country' => 'required|in:SK,CZ',
        ]);

        $method = $request->country === 'CZ' ? 'czAutocomplete' : 'autocomplete';

        try {
            /** @var \Hubipe\FinStat\Response\BaseResponse $response */
            $response = app(FinStat::class)->{$method}($data['name']);
        } catch (ResponseException $e) {
            throw new RuntimeException('FinStat: ' . $e->getMessage());
        }

        $results = $response->getResults();

        if ($response->getDailyLimitCurrent() >= $response->getDailyLimitMax()) {
            throw new RuntimeException('FinStat: Daily limit was reached');
        }
        if ($response->getMonthlyLimitCurrent() >= $response->getMonthlyLimitMax()) {
            throw new RuntimeException('FinStat: Monthly limit was reached');
        }

        $items = collect($results)
            ->reject(static function ($result) {
                return $result->isCancelled();
            })
            ->values()
            ->map(function ($result) {
                return [
                    'business_id' => $result->getIco(),
                    'name'        => $result->getName(),
                    'city'        => $result->getCity(),
                ];
            });

        return response()->json($items);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyDetails(Request $request): JsonResponse
    {
        $data = $request->validate([
            'business_id' => 'required',
            'country'     => 'required|in:SK,CZ',
        ]);

        $isSk = $request->country === 'SK';

        $method = $isSk ? 'detail' : 'czDetail';

        /** @var \Hubipe\FinStat\Response\Detail\DetailResponse $response */
        $response = app(FinStat::class)->{$method}($data['business_id']);

        return response()->json([
            'name'            => $response->getName(),
            'address'         => $response->getStreet() . ' ' . $response->getStreetNumber(),
            'postal_code'     => $response->getZipCode(),
            'city'            => $response->getCity(),
            'organization_id' => $response->getIco(),
            'tax'             => $isSk ? $response->getDic() : null,
            'value_added_tax' => $isSk ? $response->getIcDph() : null,
            'register'        => $isSk ? $response->getRegisterNumberText() : null,
            'tax_profile'     => $isSk ? $this->getTaxProfile($response->getIcDphParagraph()) : null,
            'is_company'      => $isSk ? Str::endsWith($response->getLegalFormText(), 's r. o.') : null,
        ]);
    }

    /**
     * @param string|null $taxProfile
     *
     * @return int|null
     */
    private function getTaxProfile(?string $taxProfile): ?int
    {
        if ($taxProfile === null)
            return 0;
        if ($taxProfile === '§4')
            return 1;
        if (Str::contains('§7a', $taxProfile))
            return 2;

        Log::warning('Finstat has returned an unexpected tax profile', [$taxProfile]);

        return 0;
    }
}
