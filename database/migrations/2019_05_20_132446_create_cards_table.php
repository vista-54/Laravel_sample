<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loyalty_program_id');
            $table->string('background_color')->default('#2a3947');
            $table->string('background_main_color')->default('#2a3947');
            $table->string('foreground_color')->default('#000000');
            $table->string('label_color')->default('#54b095');
            $table->string('points_head')->default('points')->nullable();
            $table->string('points_value')->default('${points}')->nullable();
            $table->string('customer_head')->default('Privilege Member')->nullable();
            $table->string('customer_value')->default('${firstName} ${lastName}')->nullable();
            $table->string('flip_head')->default('Conditions')->nullable();
            $table->string('flip_value')->default('Flip card')->nullable();
            $table->boolean('loyalty_profile')->default(0);
            $table->boolean('loyalty_offers')->default(0);
            $table->boolean('loyalty_contact')->default(0);
            $table->boolean('loyalty_terms')->default(0);
            $table->string('loyalty_terms_value', 2000)->nullable();
            $table->boolean('loyalty_message')->default(0);
            $table->string('icon', 255)->nullable();
            $table->string('background_image')->nullable();
            $table->string('customer_id')->default('${customerId}')->nullable();

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
        Schema::dropIfExists('cards');
    }
}
