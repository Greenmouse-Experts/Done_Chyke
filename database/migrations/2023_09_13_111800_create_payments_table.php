<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('receipt_title', ['Tin', 'Columbite', 'Lower Grade Columbite'])->nullable();
            $table->enum('receipt_type', ['pound', 'kg'])->nullable();
            $table->foreignId('receipt_id')->nullable()->onDelete('cascade');
            $table->string('payment_action')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_amount')->nullable();
            $table->date('date_paid')->nullable();
            $table->string('final_payment_type')->nullable();
            $table->string('final_payment_amount')->nullable();
            $table->date('final_date_paid')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
