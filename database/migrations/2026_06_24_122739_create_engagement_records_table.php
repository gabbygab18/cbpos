<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('engagement_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('record_type'); // team_meeting, feedback, recognition, suggestion, kpi, scorecard, coaching_session, pip
            $table->string('title');
            $table->text('notes')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->date('record_date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('engagement_records'); }
};
