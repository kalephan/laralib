<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserV0001 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = date('Y-m-d H:i:s', time());
        
        DB::table('users')->insert(array(
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('123456789'),
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
