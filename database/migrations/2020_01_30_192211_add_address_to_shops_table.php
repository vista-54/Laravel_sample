<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_type_id')->nullable()->after('name');
            $table->string('address')->nullable()->after('name');

            $table->foreign('shop_type_id')->references('id')->on('shop_types')
                ->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('store_type_id');
        });
    }
}
