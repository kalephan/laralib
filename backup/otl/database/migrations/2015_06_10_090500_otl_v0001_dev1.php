<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OtlV0001Dev1 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otl', function (Blueprint $table) {
            $table->string('token')->index();
            $table->timestamp('expired')->nullable();
            $table->timestamp('created_at');
            $table->text('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('otl');
    }
}
