<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            //company start/end time
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('17:00:00');
            $table->decimal('daily_working_hours', 5, 2)->default(8.00);
            $table->integer('default_break_minutes')->default(60);
            $table->decimal('overtime_threshold', 5, 2)->default(8.00);
            $table->integer('late_grace_minutes')->default(15);
            $table->integer('records_per_page')->default(10);

            $table->timestamps();
        });

        DB::table('attendance_settings')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
