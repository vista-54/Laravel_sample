<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loyalty_program_id');
            $table->string('company_name', 500)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('website', 500)->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('conditions')->nullable();
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
        Schema::dropIfExists('contacts_terms');
    }
}
