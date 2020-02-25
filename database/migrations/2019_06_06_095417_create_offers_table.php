<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loyalty_program_id');
            $table->string('name', 255);
            $table->string('description', 1000);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('points_cost');
            $table->string('customer_limit')->nullable();
            $table->integer('availability_count')->default(0);
            $table->string('notify', 255) -> nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();

            $table->foreign('loyalty_program_id')->references('id')->on('loyalty_programs')
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
        Schema::dropIfExists('offers');
    }
}
