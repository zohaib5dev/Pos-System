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
        Schema::table('customers', function (Blueprint $table) {
            // User relationship
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            
            // Address fields
            $table->string('tempId')->nullable()->after('is_active');
            $table->text('address')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->string('country', 100)->nullable()->after('postal_code');
            
            // Financial fields
            $table->string('tax_number', 50)->nullable()->after('country');
            $table->decimal('opening_balance', 15, 2)->default(0)->after('tax_number');
            $table->decimal('current_balance', 15, 2)->default(0)->after('opening_balance');
            $table->decimal('credit_limit', 15, 2)->default(0)->after('current_balance');
            $table->boolean('allow_credit')->default(false)->after('credit_limit');
            
            // Loyalty
            $table->integer('loyalty_points')->default(0)->after('allow_credit');
            
            // Additional
            $table->text('notes')->nullable()->after('loyalty_points');
            $table->boolean('is_active')->default(true)->after('notes');
            $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('city');
            $table->index('state');
            $table->index('country');
            $table->index('is_active');
            $table->index(['name', 'email', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by']);
            
            // Drop columns
            $table->dropColumn([
                'user_id',
                'address',
                'city',
                'state',
                'postal_code',
                'country',
                'tax_number',
                'opening_balance',
                'current_balance',
                'credit_limit',
                'allow_credit',
                'loyalty_points',
                'notes',
                'is_active',
                'created_by',
            ]);
        });
    }
};