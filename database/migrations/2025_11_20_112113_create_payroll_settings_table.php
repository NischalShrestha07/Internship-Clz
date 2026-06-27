<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('overtime_rate', 8, 2)->default(200.00);
            $table->integer('working_days_per_month')->default(26);
            $table->decimal('provident_fund_rate', 5, 2)->default(12.00);
            $table->decimal('tax_deduction_rate', 5, 2)->default(5.00);
            $table->json('salary_components')->nullable();
            $table->json('deduction_components')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
