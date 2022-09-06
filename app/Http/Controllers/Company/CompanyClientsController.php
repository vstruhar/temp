<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyClientsController extends Controller
{
    /**
     * @param int $companyId
     *
     * @return JsonResponse
     */
    public function dropdownItems(int $companyId): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $hasAccess = $user->allTeams()->contains(function (Team $team) use ($companyId) {
            return $team->id === $companyId;
        });

        abort_if(!$hasAccess, Response::HTTP_FORBIDDEN);

        return response()->json(['items' => Client::dropdownOptions(null, ['id', 'name'], $companyId)]);
    }
}
