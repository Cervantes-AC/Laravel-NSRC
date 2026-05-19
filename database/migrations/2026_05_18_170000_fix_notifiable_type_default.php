<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Set default value for notifiable_type column
            $table->string('notifiable_type')->default('App\\Models\\User')->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Remove the default value
            $table->string('notifiable_type')->nullable()->change();
        });
    }
};
