<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientLoyaltyProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_loyalty_programs', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('loyalty_program_id');
            $table->unsignedInteger('point')->default(0);
            $table->integer('stamped_count')->default(0);
            $table->string('client_loyalty_id');
            $table->timestamps();
            $table->primary(['client_id', 'loyalty_program_id']);

            $table->foreign('client_id')->references('id')->on('clients')
                ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('client_loyalty_programs');
    }
}
