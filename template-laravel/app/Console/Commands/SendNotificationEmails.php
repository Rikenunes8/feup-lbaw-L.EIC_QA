<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\NotificationEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SendNotificationEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notification_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all pendent notifications to email to users subscribed and set all fields "to_email" of receive_not table to false';

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
        $delay = now()->addSeconds(10);
        foreach(User::get() as $user) {
            if (!$user->receive_email) continue;

            $notifications = $user->notifications()->wherePivot('to_email', true)->wherePivot('read', false)->get();
            foreach($notifications as $notification) {
            $delay = $delay->addSeconds(8);
            $user->notify((new NotificationEmail($notification))->delay($delay));
            }
        }
        DB::table('receive_not')->where('to_email', true)->update(['to_email' => false]);
        return Command::SUCCESS;
    }
}
