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
        Schema::table('balances', function (Blueprint $table) {
            $table->string('expense')->nullable()->default(0.00);
            $table->string('cash')->nullable()->default(0.00);
            $table->string('transfer')->nullable()->default(0.00);
            $table->string('transfer_by_cheques')->nullable()->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->dropColumn('expense');
            $table->dropColumn('cash');
            $table->dropColumn('transfer');
            $table->dropColumn('transfer_by_cheques');
        });
    }
};
