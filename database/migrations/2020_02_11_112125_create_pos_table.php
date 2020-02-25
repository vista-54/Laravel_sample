<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_id')->nullable();
            $table->string('ticket_date')->nullable();
            $table->string('store_id')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('stock_code')->nullable();
            $table->string('pack')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('discount')->nullable();
            $table->string('discount_2')->nullable();
            $table->string('discount_3')->nullable();
            $table->string('amount')->nullable();
            $table->string('coupon_id')->nullable();
            $table->string('loyalty_id')->nullable();
            $table->string('campaign_id')->nullable();
            $table->string('offer_id')->nullable();
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
        Schema::dropIfExists('pos');
    }
}
