<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('member');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'school_id')) {
                $table->string('school_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'nsrc_serial_number')) {
                $table->string('nsrc_serial_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('users', 'college')) {
                $table->string('college')->nullable();
            }
            if (!Schema::hasColumn('users', 'major')) {
                $table->string('major')->nullable();
            }
            if (!Schema::hasColumn('users', 'year_level')) {
                $table->string('year_level')->nullable();
            }
            if (!Schema::hasColumn('users', 'primary_competency')) {
                $table->string('primary_competency')->nullable();
            }
            if (!Schema::hasColumn('users', 'personal_contact_number')) {
                $table->string('personal_contact_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'current_address')) {
                $table->text('current_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'home_address')) {
                $table->text('home_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_contact_person')) {
                $table->string('emergency_contact_person')->nullable();
            }
            if (!Schema::hasColumn('users', 'emergency_contact_number')) {
                $table->string('emergency_contact_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'serial_number')) {
                $table->string('serial_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'status',
                'full_name',
                'school_id',
                'nsrc_serial_number',
                'birthdate',
                'gender',
                'college',
                'major',
                'year_level',
                'primary_competency',
                'personal_contact_number',
                'current_address',
                'home_address',
                'emergency_contact_person',
                'emergency_contact_number',
                'avatar',
                'serial_number',
            ]);
        });
    }
};
