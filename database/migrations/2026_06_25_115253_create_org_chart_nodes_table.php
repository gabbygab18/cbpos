<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_chart_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('org_chart_nodes')->nullOnDelete();
            $table->string('name');
            $table->string('title');
            $table->text('facilities')->nullable(); // comma-separated or JSON list
            $table->string('color')->default('blue'); // blue|teal|amber|pink|green|gray|purple|coral|red
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_chart_nodes');
    }
};
