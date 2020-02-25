<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('shop_id');
            $table->bigInteger('amount')->default(0);
            $table->integer('point')->default(0);
            $table->string('type');
            $table->string('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')
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
        Schema::dropIfExists('client_shop');
    }
}
