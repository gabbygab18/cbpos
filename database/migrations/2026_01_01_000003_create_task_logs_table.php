<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_type_id')->nullable()->constrained('report_types')->onDelete('set null');
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->onDelete('set null');
            $table->date('work_date');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('status', ['running', 'completed'])->default('running');
            $table->text('notes')->nullable();

            // Audit: who created/last touched the time fields.
            // Members can only start/stop their own task; only admins may edit started_at/ended_at/work_date after the fact.
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('last_edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('time_edited_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_logs');
    }
};
