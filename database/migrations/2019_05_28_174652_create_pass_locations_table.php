<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pass_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pass_id');
            $table->string('latitude', 255);
            $table->string('longitude', 255);
            $table->string('params', 1000)->nullable();
            $table->timestamps();

            $table->foreign('pass_id')->references('id')->on('passes')
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
        Schema::dropIfExists('pass_locations');
    }
}
