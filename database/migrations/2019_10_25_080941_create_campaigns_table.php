<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('campaign_start')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('campaign_end')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('campaign_name', 255)->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('race', 255)->nullable();
            $table->string('age')->nullable();
            $table->integer('month')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('type', 255)->nullable();
            $table->integer('distribution')->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
