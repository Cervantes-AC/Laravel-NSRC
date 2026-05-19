<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add columns for better failure tracking
            $table->string('severity')->default('info')->after('type'); // info, warning, error, critical
            $table->string('category')->nullable()->after('severity'); // system, security, validation, external_service, database, file, import_export, backup, scheduled_task, authorization
            $table->text('failure_reason')->nullable()->after('category');
            $table->json('failure_context')->nullable()->after('failure_reason');
            $table->timestamp('acknowledged_at')->nullable()->after('read_at');
            $table->string('acknowledged_by')->nullable()->after('acknowledged_at');

            // Index for better query performance
            $table->index(['severity', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index(['notifiable_id', 'notifiable_type', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['severity', 'created_at']);
            $table->dropIndex(['category', 'created_at']);
            $table->dropIndex(['notifiable_id', 'notifiable_type', 'severity']);

            $table->dropColumn([
                'severity',
                'category',
                'failure_reason',
                'failure_context',
                'acknowledged_at',
                'acknowledged_by',
            ]);
        });
    }
};
