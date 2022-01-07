<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Notifications\NotificationEmail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;


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
        $schedule->call(function() {
            foreach(User::get() as $user) {
                if (!$user->receive_email) continue;
            
                $notifications = $user->notifications()->wherePivot('to_email', true)->wherePivot('read', false)->get();
                foreach($notifications as $notification) {
                    $user->notify(new NotificationEmail($notification));
                }
            }
            DB::table('receive_not')->where('to_email', true)->update(['to_email' => false]);
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
