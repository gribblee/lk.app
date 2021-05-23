<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deal;
use App\Models\Option;
use Exception;
use App\Models\Status;

class DealsClose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deals:check';

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
            $options = Option::getKeyValue();
            $dayCalimClose = 5;
            if (isset($options->day_claim_close) && !empty($options->day_claim_close)) {
                $dayCalimClose = $options->day_claim_close;
            }
            $deals = Deal::with('status')->whereHas('status', function ($query) {
                return $query->whereNotIn('type', [1004, 1002, 1003]);
            })->whereRaw("(
            date_trunc('days', now()) 
            - date_trunc('days', created_at)
        ) > '$dayCalimClose days'::interval")->get();
            $ids = [];
            foreach ($deals as $deal) {
                $ids[] = $deal->id;
            }
            $status = Status::where('type', 1002)->first();
            if ($status) {
                Deal::whereIn('id', $ids)->update(['status_id' => $status->id]);
            }
        } catch (Exception $e) {
            return \Log::error($e->getMessage() . " - " . \Carbon\Carbon::now());
        }
    }
}
