<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoyaltyProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255)->default('My Loyalty card')->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->string('currency')->default('USD')->nullable();
            $table->string('currency_value')->default(1);
            $table->string('start_at')->default(0);
            $table->string('link', 500)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('loyalty_programs');
    }
}
