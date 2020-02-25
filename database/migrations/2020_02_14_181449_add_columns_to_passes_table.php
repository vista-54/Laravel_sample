<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passes', function (Blueprint $table) {
            $table->string('margin_value')->nullable();
            $table->timestamp('expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passes', function (Blueprint $table) {
            $table->dropColumn('margin_value');
            $table->dropColumn('expiration_date');
        });
    }
}
