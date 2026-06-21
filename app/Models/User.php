<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
    public const ROLE_COOK = 'cook';

    public function isHeadOfProgrammes(): bool
    {
        return $this->role === self::ROLE_HEAD_OF_PROGRAMMES;
    }

    public function isSystemManager(): bool
    {
        return $this->role === self::ROLE_SYSTEM_MANAGER;
    }

    public function isCook(): bool
    {
        return $this->role === self::ROLE_COOK;
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
