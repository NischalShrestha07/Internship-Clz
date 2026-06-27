<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->string('priority')->default('Medium')->after('notice_type');
            $table->boolean('is_read')->default(false)->after('message');
            $table->timestamp('read_at')->nullable()->after('is_read');
            $table->foreignId('employee_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['priority', 'is_read', 'read_at']);
            $table->foreignId('employee_id')->nullable(false)->change();
        });
    }
};
