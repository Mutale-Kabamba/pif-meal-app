<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'assigned_project_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public const ROLE_HEAD_OF_PROGRAMMES = 'head_of_programmes';
    public const ROLE_SYSTEM_MANAGER = 'system_manager';
    public const ROLE_PROJECT_OFFICER = 'project_officer';
    public const ROLE_COOK = 'cook';
    public const ROLE_COACH = 'coach';

    /**
     * Determine if the user can access the Filament admin panel in production.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Allows access to your seeded users or anyone with valid credentials on the cloud
        return true;
    }

    public function isHeadOfProgrammes(): bool
    {
        return $this->role === self::ROLE_HEAD_OF_PROGRAMMES;
    }

    public function isSystemManager(): bool
    {
        return $this->role === self::ROLE_SYSTEM_MANAGER;
    }

    public function isProjectOfficer(): bool
    {
        return $this->role === self::ROLE_PROJECT_OFFICER;
    }

    public function isCook(): bool
    {
        return $this->role === self::ROLE_COOK;
    }

    public function isCoach(): bool
    {
        return $this->role === self::ROLE_COACH;
    }

    /** The football team(s) this user coaches. */
    public function coachedTeams()
    {
        return $this->hasMany(Team::class, 'coach_id');
    }

    public function assignedProject()
    {
        return $this->belongsTo(Project::class, 'assigned_project_id');
    }

    public function mealLogs()
    {
        return $this->hasMany(MealLog::class, 'served_by_user_id');
    }
}