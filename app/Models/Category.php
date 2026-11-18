<?php

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order'])]
class Category extends Model
{
    use HasFactory, HasSlug, LogsActivity;

    public function slugSource(): string
    {
        return 'name';
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
