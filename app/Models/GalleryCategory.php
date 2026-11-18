<?php

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'sort_order'])]
class GalleryCategory extends Model
{
    use HasFactory, HasSlug, LogsActivity;

    public function slugSource(): string
    {
        return 'name';
    }

    public function albums(): HasMany
    {
        return $this->hasMany(GalleryAlbum::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class);
    }
}
