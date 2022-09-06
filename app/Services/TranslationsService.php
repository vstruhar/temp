<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationsService
{
    /**
     * Get available language codes by reading directory names in the resource/lang folder
     *
     * @return array
     */
    public static function getAvailableLanguageCodes(): array
    {
        $getLanguageCodesFunction = static function () {
            $directories = File::directories(resource_path('lang'));

            return collect($directories)
                ->map(static function ($directory) {
                    $langCode = Str::of($directory)->explode('/')->last();
                    return (strlen($langCode) === 2) ? $langCode : null;
                })
                ->filter()
                ->toArray();
        };
      
        return app()->environment('production')
            ? Cache::rememberForever('available_translations', $getLanguageCodesFunction)
            : $getLanguageCodesFunction();
    }

    /**
     * This will return all translations for current locale
     *
     * @return mixed
     */
    public function all(): array
    {
        $locale = app()->getLocale();

        $prepareTranslationsFunction = function () use ($locale) {
            $phpTranslations  = [];
            $jsonTranslations = [];

            if (File::exists(resource_path("lang/$locale"))) {
                $phpTranslations = collect(File::allFiles(resource_path("lang/$locale")))
                    ->filter(function ($file) {
                        return $file->getExtension() === "php";
                    })->flatMap(function ($file) {
                        return Arr::dot(File::getRequire($file->getRealPath()));
                    })->toArray();
            }

            if (File::exists(resource_path("lang/$locale.json"))) {
                $jsonTranslations = json_decode(File::get(resource_path("lang/$locale.json")), true);
            }

            return array_merge($phpTranslations, $jsonTranslations);
        };
         
        return app()->environment('production')
            ? Cache::rememberForever("translations_{$locale}", $prepareTranslationsFunction)
            : $prepareTranslationsFunction();
    }
}
