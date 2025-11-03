<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

class EloquentTagRepository implements TagRepositoryInterface
{
    public function ensure(array $names): array
    {
        $names = array_values(array_unique(array_filter(array_map(fn ($n) => trim((string) $n), $names))));
        if (!$names) return [];
        $existing = Tag::whereIn('name', $names)->pluck('id', 'name')->all();
        $toCreate = array_diff($names, array_keys($existing));
        foreach ($toCreate as $name) {
            $tag = Tag::create(['name' => $name]);
            $existing[$name] = $tag->id;
        }
        return array_values($existing);
    }
}

