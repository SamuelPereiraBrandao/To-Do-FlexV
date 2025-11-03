<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_id',
        'performed_by',
        'action',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

