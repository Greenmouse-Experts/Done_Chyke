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
        Schema::create('columbite_payment_analyses', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pound', 'kg'])->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('customer')->nullable();
            $table->string('manager_id')->nullable();
            $table->double('berating')->default(0.00);
            $table->double('bags')->default(0.00);
            $table->double('pounds')->default(0.00);
            $table->double('percentage_analysis')->default(0.00);
            $table->double('bag_equivalent')->default(0.00);
            $table->double('pound_equivalent')->default(0.00);
            $table->double('price')->default(0.00);
            $table->longText('receipt')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('columbite_payment_analyses');
    }
};