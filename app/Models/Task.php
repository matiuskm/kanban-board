<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted() {
        static::creating(function (Task $task) {
            if ($task->sort !== null) {
                return;
            }
            $lastSortPosition = $task->group->tasks->max('sort');
            $task->sort = $lastSortPosition == null ? 0 : $lastSortPosition + 1;
        });
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function scopeInOrder(Builder $query) {
        $query->orderBy('sort');
    }
}
