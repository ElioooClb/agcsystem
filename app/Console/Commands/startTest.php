<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class startTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tests:run';

    public function handle()
    {
        $command = "php artisan test --v --filter=WorkSiteStatesServiceTest";
        // $this->info("Running tests...");
        shell_exec($command);
    }
}
