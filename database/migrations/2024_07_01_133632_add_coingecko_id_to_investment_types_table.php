<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * https://api.coingecko.com/api/v3/coins/list - получить весь список
     *
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('investment_types', function (Blueprint $table) {
            $table->string('coingecko_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_types', function (Blueprint $table) {
            $table->dropColumn('coingecko_id');
        });
    }
};
