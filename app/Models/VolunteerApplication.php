<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Support\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'full_name', 'email', 'phone', 'location', 'area_of_interest', 'skills', 'availability',
    'motivation', 'status', 'internal_notes', 'reviewed_by', 'reviewed_at', 'ip_address',
])]
class VolunteerApplication extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
