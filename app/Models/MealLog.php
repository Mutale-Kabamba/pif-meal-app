<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'project_id', 'served_by_user_id', 'meal_type', 'served_at'];

    protected $casts = [
        'served_at' => 'datetime',
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

    public function scopeToday($query)
    {
        return $query->whereDate('served_at', today());
    }

    public function scopeForMealType($query, string $mealType)
    {
        return $query->where('meal_type', $mealType);
    }
}
