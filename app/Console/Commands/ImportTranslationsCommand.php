<?php

namespace App\Console\Commands;

use Throwable;
use Illuminate\Console\Command;

class ImportTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import languages from excel to json files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $languages = fastexcel()->import(resource_path() . '/lang/languages.xlsx');

            $preparedLanguages = [];

            foreach ($languages as $language) {
                foreach (array_keys($language) as $langAbbr) {
                    if ($langAbbr !== 'en' && $langAbbr !== '') {
                        $preparedLanguages[$langAbbr][$language['en']] = $language[$langAbbr];
                    }
                }
            }

            foreach ($preparedLanguages as $languageAbbr => $preparedLanguage) {
                $json = json_encode($preparedLanguage, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                if (file_put_contents(resource_path() . "/lang/{$languageAbbr}.json", $json)) {
                    $this->line("importing success, language: {$languageAbbr}");
                } else {
                    $this->error("importing failed, language: {$languageAbbr}");
                }
            }
        } catch (Throwable $error) {
            $this->error($error->getMessage());
        }
    }
}
