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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 100)->unique()->index();
            $table->string('invoice_number', 100)->nullable()->unique()->index();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->integer('tax_rate_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->datetime('order_date')->useCurrent()->index();
            $table->date('due_date')->nullable();
            $table->enum('order_type', ['pos', 'online', 'wholesale'])->default('pos');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])->default('pending')->index();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'refunded'])->default('pending')->index();
            $table->enum('shipping_status', ['pending', 'shipped', 'delivered', 'returned'])->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('shipping_cost', 15, 2)->default(0.00);
            $table->decimal('other_charges', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('due_amount', 15, 2)->default(0.00);
            $table->decimal('change_amount', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
