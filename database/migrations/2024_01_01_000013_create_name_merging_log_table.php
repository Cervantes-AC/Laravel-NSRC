<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('name_merging_log', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('merged_name');
            $table->float('similarity_score');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('name_merging_log');
    }
};
