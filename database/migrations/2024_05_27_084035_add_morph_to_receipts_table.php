<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receipts_data', function (Blueprint $table) {
            $table->unsignedBigInteger('morph_id')->nullable();
            $table->string('morph_type')->nullable();
            $table->index(['morph_id', 'morph_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts_data', function (Blueprint $table) {
            $table->dropIndex(['morph_id', 'morph_type']);
            $table->dropColumn(['morph_id', 'morph_type']);
        });
    }
};
