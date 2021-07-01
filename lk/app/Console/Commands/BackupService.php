<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Spatie\DbDumper\Databases\PostgreSql as PGSQLD;
use Chipk4\Selectel\SelectelApiFacade as SelectelApi;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            PGSQLD::create()
                ->setDbName(env('DB_DATABASE'))
                ->setUserName(env('DB_USERNAME'))
                ->setPassword(env('DB_PASSWORD'))
                ->dumpToFile(storage_path("app/private/database/" . date("d.m.Y") . '.sql'));
            SelectelApi::storeFile('LM.RESERVE', storage_path("app/private/database/" . date("d.m.Y") . '.sql'), 'database/' . date("d.m.Y") . '.sql');
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
