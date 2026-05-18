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
        Schema::create('attendance_source', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('attendance'); // 'Time in' or 'Time out'
            $table->dateTime('date_time');
            $table->string('location')->nullable();
            $table->string('shift_type')->nullable();
            $table->timestamps();

            // Indexes for common queries
            $table->index('full_name');
            $table->index('date_time');
            $table->index(['full_name', 'date_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_source');
    }
};
