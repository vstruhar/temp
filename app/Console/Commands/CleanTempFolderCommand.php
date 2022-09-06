<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CleanTempFolderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:temp-folder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete all files that are in storage/app/temp folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = new Filesystem;
        $dir = storage_path('app/temp');

        if ($file->cleanDirectory($dir)) {
            $this->info('Temp folder was cleaned successfully');
        } else {
            $this->error('Failed to clean temp folder');
        }
    }
}
