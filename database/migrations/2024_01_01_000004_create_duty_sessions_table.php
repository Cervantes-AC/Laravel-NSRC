<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->date('date');
            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('status')->default('ONGOING');
            $table->string('trace_id')->nullable();
            $table->string('location')->nullable();
            $table->string('sector')->nullable();
            $table->float('integrity_score')->default(0);
            $table->foreignId('volunteer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_sessions');
    }
};
