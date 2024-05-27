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
        Schema::create('auto_technical_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipts_data_id')->nullable();
            $table->integer('inspection_mileage')->nullable();
            $table->timestamps();

            $table->foreign('receipts_data_id')->references('id')->on('receipts_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_technical_inspections');
    }
};
