<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedSheet extends Model
{
    protected $fillable = ['project_id', 'filename', 'file_path', 'total_cards', 'generated_by'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}