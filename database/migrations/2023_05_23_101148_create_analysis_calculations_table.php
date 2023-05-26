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
        Schema::create('analysis_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('percentage_min')->nullable();
            $table->string('percentage_max')->nullable();
            $table->string('dollar_rate')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
        Schema::dropIfExists('analysis_calculations');
    }
};
