<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'shortcode', 'qr_token', 'is_active', 'literacy_enrolled'];

    protected $casts = [
        'is_active'          => 'boolean',
        'literacy_enrolled'  => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function mealLogs()
    {
        return $this->hasMany(MealLog::class);
    }

    public function anomalyLogs()
    {
        return $this->hasMany(AnomalyLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
