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
            $table->unsignedBigInteger('subcategory_id')->after('price')->nullable();

            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts_data', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn('subcategory_id');
        });
    }
};
