<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'position', 'bio', 'photo', 'social_links', 'sort_order', 'is_active'])]
class TeamMember extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /** Initials, ignoring common honorifics (e.g. "Pastor Fola Ojo" → "FO"). */
    public function initials(): string
    {
        $name = preg_replace('/^(pastor|rev\.?|dr\.?|mr\.?|mrs\.?|ms\.?|prof\.?)\s+/i', '', trim($this->name));

        return mb_strtoupper(collect(preg_split('/\s+/', $name))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode(''));
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? Media::disk()->url($this->photo) : null;
    }
}
