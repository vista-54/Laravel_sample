<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_shops', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('shop_id');
            $table->primary(['campaign_id', 'shop_id']);

            $table->foreign('campaign_id')->references('id')->on('campaigns')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')
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
        Schema::dropIfExists('campaign_shops');
    }
}
