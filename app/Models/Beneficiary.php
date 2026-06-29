<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'shortcode', 'qr_token', 'is_active', 'team_id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Many-to-many: football project, education project, or both. */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'beneficiary_project')->withTimestamps();
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
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

    /** Filter beneficiaries enrolled in a specific project (via pivot). */
    public function scopeInProject($query, int $projectId)
    {
        return $query->whereHas('projects', fn ($q) => $q->where('projects.id', $projectId));
    }

    public function isDualEnrolled(): bool
    {
        return $this->projects()->count() >= 2;
    }
}
