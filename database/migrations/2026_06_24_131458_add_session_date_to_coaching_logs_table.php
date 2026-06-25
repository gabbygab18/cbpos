<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_logs', function (Blueprint $table) {
            // Date the coaching session itself took place (distinct from created_at,
            // which only records when the admin saved the form).
            $table->date('session_date')->nullable()->after('week_number');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_logs', function (Blueprint $table) {
            $table->dropColumn('session_date');
        });
    }
};
