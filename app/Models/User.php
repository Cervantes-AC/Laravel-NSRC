<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'full_name',
        'role',
        'status',
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
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function dutySessions(): HasMany
    {
        return $this->hasMany(DutySession::class, 'volunteer_id');
    }

    public function attendanceSummaries(): HasMany
    {
        return $this->dutySessions();
    }

    public function sessions(): HasMany
    {
        return $this->dutySessions();
    }

    public function metrics(): HasOne
    {
        return $this->hasOne(VolunteerMetrics::class, 'volunteer_id');
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function conversationHistory(): HasMany
    {
        return $this->hasMany(ConversationHistory::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
