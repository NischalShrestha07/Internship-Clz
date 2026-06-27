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
        Schema::table('holidays', function (Blueprint $table) {
            $table->renameColumn('date', 'start_date')->after('description');
            $table->date('end_date')->after('start_date')->nullable();
            $table->boolean('is_recurring')->default(false)->after('description');
            $table->string('recurrence_pattern')->nullable()->after('is_recurring');
            $table->boolean('is_active')->default(true)->after('recurrence_pattern');
            $table->foreignId('created_by')->constrained('users')->after('applies_to_departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            //
        });
    }
};
