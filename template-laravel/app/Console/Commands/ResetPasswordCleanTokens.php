<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetPasswordCleanTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset_password:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean tokens from reset_passwords table';

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
        DB::table('password_resets')->delete();
        return Command::SUCCESS;
    }
}
