<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'programme_type', 'daily_meal_limit_per_beneficiary', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_meal_limit_per_beneficiary' => 'integer',
    ];

    public const PROGRAMME_FOOTBALL  = 'football';
    public const PROGRAMME_EDUCATION = 'education';

    public static function programmeTypes(): array
    {
        return [
            self::PROGRAMME_FOOTBALL  => 'Football',
            self::PROGRAMME_EDUCATION => 'Education',
        ];
    }

    public function beneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class, 'beneficiary_project')->withTimestamps();
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function mealLogs()
    {
        return $this->hasMany(MealLog::class);
    }

    public function cooks()
    {
        return $this->hasMany(User::class, 'assigned_project_id');
    }
}
