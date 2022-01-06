<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Notifications\NotificationEmail;

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
        /*$schedule->call(function() {
            $notificationsToSend = DB::table('receive_not')->where('toEmail', true)->get();

            foreach($notificationsToSend as $not_user) {
                $not_user->update(['toEmail' => false]);
                if ($not_user->read) continue;
                
                $notification = Notification::find($not_user->id_notification);
                $user = User::find($not_user->id_user);
                if ($user->receive_email)
                    $user->notify(new NotificationEmail($notification));
            }

        })->everyFiveMinutes();*/
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
