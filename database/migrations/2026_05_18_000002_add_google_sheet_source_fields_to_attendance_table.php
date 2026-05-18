<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance', 'source_signature')) {
                $table->string('source_signature')->nullable()->unique()->after('shift_type');
            }

            if (! Schema::hasColumn('attendance', 'source_payload')) {
                $table->json('source_payload')->nullable()->after('source_signature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['source_signature', 'source_payload']);
        });
    }
};
