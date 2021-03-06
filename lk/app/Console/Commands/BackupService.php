<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Spatie\DbDumper\Databases\PostgreSql as PGSQLD;
use Chipk4\Selectel\SelectelApiFacade as SelectelApi;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Aws\S3\S3Client;
use \FtpClient\FtpClient;

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
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            SelectelApi::storeFile('LM.RESERVE', storage_path("app/private/database/" . date("d.m.Y") . '.sql'), date("d.m.Y") . '.sql');
            Storage::disk('s3')->put(date("d.m.Y") . '.sql', Storage::disk('private')->get('database/' . date("d.m.Y") . '.sql'));
            // $ftp = new \FtpClient\FtpClient();
            // $ftp->connect('ftp.selcdn.ru');
            // $ftp->login('163622_devers', 'X2nb(\ZU?7');
            // $ftp->putAll(
            //     storage_path(
            //         "app/private/database/" . date("d.m.Y") . '.sql',
            //         'LM.RESERVE/' . date("d.m.Y") . '.sql',
            //         FTP_ASCII
            //     )
            // );
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
