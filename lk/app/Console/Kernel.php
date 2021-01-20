<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Helpers\SendPulse;
use App\Models\Option;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $sendPulse = new SendPulse;
            $bookIdWasOnline = Option::getValue('bookIdWasOnline');

            $users = \DB::table('users')
                ->selectRaw("*")
                ->whereRaw("(
                        date_trunc('days', now()) 
                        - date_trunc('days', was_online)
                    ) > '6 days'::interval
                    AND (
                        date_trunc('days', now()) 
                        - date_trunc('days', was_online)
                    ) < '29 days'::interval")->get();
            $usersBook = [];
            foreach ($users as $user) {
                $usersBook[] = [
                    'email' => $user->email,
                    'variables' => [
                        'phone' => $user->authUser->phone,
                        'name' => $user->authUser->name,
                        'was_online' => $user->was_online
                    ]
                ];
            }
            $sendPulse->addEmails($bookIdWasOnline, $usersBook);
        })
            ->weekly()
            ->mondays()
            ->at('09:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
