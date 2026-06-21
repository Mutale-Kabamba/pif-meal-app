<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnomalyLog extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'project_id', 'served_by_user_id', 'meal_type', 'attempted_at', 'reason'];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function servedBy()
    {
        return $this->belongsTo(User::class, 'served_by_user_id');
    }
}
