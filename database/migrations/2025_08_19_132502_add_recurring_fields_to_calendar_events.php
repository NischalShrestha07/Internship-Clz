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
        Schema::table('calendar_events', function (Blueprint $table) {
            if (!Schema::hasColumn('calendar_events', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('is_holiday');
            }
            if (!Schema::hasColumn('calendar_events', 'recurrence_pattern')) {
                $table->string('recurrence_pattern')->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('calendar_events', 'recurrence_interval')) {
                $table->integer('recurrence_interval')->default(1)->after('recurrence_pattern');
            }
            if (!Schema::hasColumn('calendar_events', 'end_date')) {
                $table->date('end_date')->nullable()->after('recurrence_interval');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn(['is_recurring', 'recurrence_pattern', 'recurrence_interval', 'end_date']);
        });
    }
};
