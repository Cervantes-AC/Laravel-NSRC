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
        Schema::table('volunteer_metrics', function (Blueprint $table) {
            $table->integer('total_minutes')->default(0)->after('total_undertime_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_metrics', function (Blueprint $table) {
            $table->dropColumn('total_minutes');
        });
    }
};
