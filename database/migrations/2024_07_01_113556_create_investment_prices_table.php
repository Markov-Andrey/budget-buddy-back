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
        Schema::create('investment_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_type_id')->constrained('investment_types')->onDelete('cascade');
            $table->date('date');
            $table->decimal('price', 30, 10);
            $table->timestamps();

            $table->unique(['investment_type_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_prices');
    }
};
