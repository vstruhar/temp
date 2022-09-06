<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    /**
     * @var \App\Models\Team
     */
    protected Team $company;

    public function __construct()
    {
        if (request()->server('PHP_SELF') === 'artisan') {
            return;
        }

        abort_if(!request()->has('api_key') || strlen(request('api_key')) !== 46, 400, 'Invalid API key');

        $company = Team::whereHas('companyApiKey', fn($q) => $q->where('key', request('api_key')))
                       ->with('companyDetails')
                       ->first();

        abort_if(!$company, 400, 'Company not found');

        $this->company = $company;
    }

    /**
     * @param mixed $data
     * @param int   $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($data = [], int $code = 200): JsonResponse
    {
        return response()->json($data, $code, ['X-Company-Name' => $this->company->companyDetails->name]);
    }
}
