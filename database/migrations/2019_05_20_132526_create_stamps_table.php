<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('card_id');
            $table->integer('stamps_number')->default(10);
            $table->string('background_color')->default('#54b095');
            $table->string('background_image', 500)->nullable();
            $table->string('stamp_color')->default('#F00000');
            $table->string('unstamp_color')->default('#ffffff');
            $table->string('stamp_image', 500)->nullable();
            $table->string('unstamp_image', 500)->nullable();
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')
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
        Schema::dropIfExists('stamps');
    }
}
