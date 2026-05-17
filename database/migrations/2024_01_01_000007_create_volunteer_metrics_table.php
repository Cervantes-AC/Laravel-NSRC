<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('full_name');
            $table->integer('total_regular_minutes')->default(0);
            $table->integer('total_overtime_minutes')->default(0);
            $table->integer('total_undertime_minutes')->default(0);
            $table->integer('invalid_record_count')->default(0);
            $table->integer('session_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_metrics');
    }
};
