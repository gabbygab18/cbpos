<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coaching_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('coaching_type');
            $table->string('week_number')->nullable();
            $table->text('root_cause_coachee')->nullable();
            $table->text('coach_comments')->nullable();
            $table->string('tools')->nullable();
            $table->text('coachee_action_plan')->nullable();
            $table->text('coach_commitment')->nullable();
            $table->string('duration')->nullable();
            $table->text('targets')->nullable();
            $table->text('employee_combined_text')->nullable();
            $table->string('client_classification')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('coaching_logs'); }
};
