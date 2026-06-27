<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('payroll_month', 2);
            $table->year('payroll_year');
            $table->decimal('basic_salary', 10, 2);
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('overtime_rate', 8, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('tax_deduction', 10, 2)->default(0);
            $table->decimal('provident_fund', 10, 2)->default(0);
            $table->decimal('total_earnings', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['generated', 'paid', 'pending'])->default('generated');
            $table->date('payment_date')->nullable();
            $table->text('remarks')->nullable();
            $table->json('salary_breakdown')->nullable();
            $table->json('deduction_breakdown')->nullable();

            // Component fields
            $table->decimal('house_rent_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('dearness_allowance', 10, 2)->default(0);
            $table->decimal('conveyance_allowance', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(0);
            $table->decimal('income_tax', 10, 2)->default(0);
            $table->decimal('loan_deduction', 10, 2)->default(0);

            $table->timestamps();

            // Unique constraint to prevent duplicate payroll entries
            $table->unique(['employee_id', 'payroll_month', 'payroll_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
