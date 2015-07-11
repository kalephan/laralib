<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TaxonomyV0001Dev1 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vocabulary', function ($table) {
            $table->string('id', 32)->primary();
            $table->string('title', 256);
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        Schema::create('taxonomy', function ($table) {
            $table->increments('id');
            $table->string('vocabulary_id', 32);
            $table->string('title', 256);
            $table->integer('parent')->default(0)->unsigned();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('taxonomy');
        Schema::drop('vocabulary');
    }
}
