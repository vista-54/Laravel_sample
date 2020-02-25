<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePassTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pass_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pass_id');
            $table->string('background_color')->default('#2a3947');
            $table->string('background_main_color')->default('#2a3947');
            $table->string('foreground_color')->default('#000000');
            $table->string('label_color')->default('#54b095');
            $table->string('points_head')->default('score')->nullable();
            $table->string('points_value')->default('${points}')->nullable();
            $table->string('offer_head')->default('50 Baht')->nullable();
            $table->string('offer_value')->default('REWARD')->nullable();
            $table->string('customer_head')->default('EXPIRES')->nullable();
            $table->string('customer_value')->default('31/12/2018')->nullable();
            $table->string('flip_head')->default('TERMS & CONDITIONS')->nullable();
            $table->string('flip_value')->default('TAP & BELOW')->nullable();
            $table->string('back_side_head')->default('TERMS AND CONDITIONS')->nullable();
            $table->string('back_side_value', 5000)->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum')->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('background_image')->nullable();
            $table->string('stripe_image')->nullable();
            $table->string('customer_id')->default('${pidNumber}')->nullable();
            $table->boolean('unlimited')->default(false);
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
        Schema::dropIfExists('pass_templates');
    }
}
