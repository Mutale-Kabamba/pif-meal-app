<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'project_id',
        'team_id',
        'recorded_by_user_id',
        'activity_type',
        'attended_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'attended_at' => 'date',
    ];

    public const ACTIVITY_TRAINING = 'training';
    public const ACTIVITY_CLASS_SESSION = 'class_session';

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attended_at', today());
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeForProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
