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
        Schema::create('investment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_id')->nullable();
            $table->unsignedBigInteger('investment_type_id')->nullable();
            $table->decimal('size', 30, 10)->nullable();
            $table->decimal('cost_per_unit', 30, 10)->nullable();
            $table->timestamps();

            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            $table->foreign('investment_type_id')->references('id')->on('investment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_details');
    }
};
