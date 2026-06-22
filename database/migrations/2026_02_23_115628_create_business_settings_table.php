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
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_logo', 2048)->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_phone', 20)->nullable();
            $table->string('business_email')->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->string('registration_number', 100)->nullable();
            $table->string('currency_code', 3)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->string('timezone', 100)->default('UTC');
            $table->string('date_format', 50)->default('Y-m-d');
            $table->text('receipt_footer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
