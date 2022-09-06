<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanyApiKey;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CompanyApiKeyController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->authorize('viewAny', CompanyApiKey::class);

        $companyApiKey = auth()->user()->currentTeam->companyApiKey;

        if (!$companyApiKey) {
            do {
                $newKey = Str::random(46);
            } while (CompanyApiKey::where('key', $newKey)->exists());

            $companyApiKey = auth()->user()->currentTeam->companyApiKey()->create(['key' => $newKey]);
        }

        return Inertia::render('Settings', ['section' => 'CompanyApiKey', 'data' => $companyApiKey]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function reset()
    {
        $companyApiKey = auth()->user()->currentTeam->companyApiKey;

        $this->authorize('update', $companyApiKey);

        do {
            $newKey = Str::random(46);
        } while (CompanyApiKey::where('key', $newKey)->exists());

        $companyApiKey->update(['key' => $newKey]);

        return back()->banner(__('API key was reset successfully'));
    }
}
