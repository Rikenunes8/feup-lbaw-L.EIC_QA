<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });

        // UPDATE ROWS ALREADY IN TABLE - NOT SURE IF IT IS THE BEST WAY
        $rows = DB::table('users')->get(['id']);
        foreach ($rows as $row) {
            DB::table('users')
                ->where('id', $row->id)
                ->update(['email_verified_at' => now(), 'remember_token' => Str::random(10)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
        });
    }
}
