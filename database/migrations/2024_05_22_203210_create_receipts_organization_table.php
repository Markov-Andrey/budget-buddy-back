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
        Schema::create('receipts_organization', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipts_id')->nullable();
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('entrance')->nullable();
            $table->timestamps();

            $table->foreign('receipts_id')->references('id')->on('receipts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts_organization');
    }
};
