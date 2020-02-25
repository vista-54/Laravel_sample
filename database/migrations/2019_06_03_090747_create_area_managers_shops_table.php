<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaManagersShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_managers_shops', function (Blueprint $table) {
            $table->unsignedBigInteger('area_manager_id');
            $table->unsignedBigInteger('shop_id');
            $table->primary(['area_manager_id', 'shop_id']);
            
            $table->foreign('area_manager_id')->references('id')->on('area_managers')
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
        Schema::dropIfExists('area_managers_shops');
    }
}
