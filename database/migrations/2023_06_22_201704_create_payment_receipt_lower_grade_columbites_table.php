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
        Schema::create('payment_receipt_lower_grade_columbites', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pound', 'kg'])->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('supplier')->nullable();
            $table->string('staff')->nullable();
            $table->double('grade')->default(0.00);
            $table->double('bag')->default(0.00);
            $table->double('pound')->default(0.00);
            $table->double('kg')->default(0.00);
            $table->double('percentage_analysis')->default(0.00);
            $table->double('total_in_pound')->default(0.00);
            $table->double('total_in_kg')->default(0.00);
            $table->string('berating_rate_list')->nullable();
            $table->string('analysis_rate_list')->nullable();
            $table->double('price')->default(0.00);
            $table->double('amount_paid')->default(0.00);
            $table->string('receipt_no')->nullable();
            $table->longText('receipt_image')->nullable();
            $table->date('date_of_purchase')->nullable();
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
        Schema::dropIfExists('payment_receipt_lower_grade_columbites');
    }
};
