<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UnverifiedUsersClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clean_unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all users not verified yet';

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
        User::unverified()->delete();
        return Command::SUCCESS;
    }
}
