<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'assignee_id',
        'title',
        'description',
        'theme_id',
        'done',
        'is_public',
        'due_at',
    ];

    protected $casts = [
        'done' => 'boolean',
        'due_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_todo');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TodoHistory::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TodoComment::class)->with('user:id,name,email');
    }
}
