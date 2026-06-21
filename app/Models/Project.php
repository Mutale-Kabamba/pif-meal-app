<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'budget_code', 'daily_meal_limit_per_beneficiary', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_meal_limit_per_beneficiary' => 'integer',
    ];

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
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
