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
        Schema::create('stock_adjustments', function (Blueprint $table) {
              $table->id();
            $table->string('adjustment_number', 100)->unique();
            $table->foreignId('product_id')->constrained();
            $table->enum('adjustment_type', ['addition', 'deduction']);
            $table->integer('quantity');
            $table->integer('current_quantity');
            $table->integer('new_quantity');
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
