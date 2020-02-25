<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('offer_id');
            $table->string('background_color')->default('#EFB334');
            $table->string('background_main_color')->default('#2a3947');
            $table->string('foreground_color')->default('#000000');
            $table->string('label_color')->default('#000000');
            $table->string('background_image')->nullable();
            $table->string('stripe_image')->nullable();
            $table->string('points_head')->default('points')->nullable();
            $table->string('points_value')->default('${points}')->nullable();
            $table->string('offer_head')->default('50 Baht')->nullable();
            $table->string('offer_value')->default('REWARD')->nullable();
            $table->string('customer_head')->default('Your Reward')->nullable();
            $table->string('customer_value')->default('${Description}')->nullable();
            $table->boolean('loyalty_active_offer')->default(0);
            $table->boolean('loyalty_offers')->default(0);
            $table->boolean('loyalty_profile')->default(0);
            $table->boolean('loyalty_contact')->default(0);
            $table->boolean('loyalty_terms')->default(0);
            $table->boolean('loyalty_last_message')->default(0);
            $table->boolean('loyalty_message')->default(0);
            $table->string('icon', 255)->nullable();
            $table->string('customer_id')->default('${pidNumber}')->nullable();
            $table->timestamps();

            $table->foreign('offer_id')->references('id')->on('offers')
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
        Schema::dropIfExists('offer_cards');
    }
}
