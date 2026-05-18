<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('priority')->default('normal');
            $table->string('status')->default('draft');
            $table->string('audience')->default('members');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'audience', 'published_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
