<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pip_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('client_name')->nullable();
            $table->string('team_leader')->nullable();
            $table->string('manager')->nullable();
            $table->string('offense_occurrence')->nullable();
            $table->date('due_date')->nullable();
            $table->text('engagement')->nullable();
            $table->text('reinforce')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pip_records'); }
};
