<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loyalty_program_id');
            $table->integer('set_email')->default(0);
            $table->integer('set_phone')->default(0);
            $table->integer('set_card')->default(0);
            $table->integer('scan_card')->default(0);
            $table->timestamps();

            $table->foreign('loyalty_program_id')->references('id')->on('loyalty_programs')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scores');
    }
}
