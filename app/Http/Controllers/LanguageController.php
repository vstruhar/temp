<?php

namespace App\Http\Controllers;

use App\Services\TranslationsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class LanguageController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param $locale
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale): RedirectResponse
    {
        $supportedLanguages = TranslationsService::getAvailableLanguageCodes();

        if (in_array($locale, $supportedLanguages, true)) {
            session()->put('locale', $locale);
            auth()->user()->update(['language' => $locale]);
        }

        return redirect()->back();
    }
}
