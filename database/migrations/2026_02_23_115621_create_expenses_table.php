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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 100)->unique();
            $table->foreignId('expense_category_id')->constrained();
            $table->date('expense_date')->index();
            $table->decimal('amount', 15, 2);
            $table->foreignId('payment_method_id')->constrained();
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_image', 2048)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
