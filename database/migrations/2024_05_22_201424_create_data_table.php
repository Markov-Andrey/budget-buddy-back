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
        Schema::create('receipts_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipts_id');
            $table->string('name')->nullable();
            $table->string('quantity')->nullable();
            $table->string('weight')->nullable();
            $table->string('price')->nullable();
            $table->timestamps();

            $table->foreign('receipts_id')->references('id')->on('receipts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts_data');
    }
};
