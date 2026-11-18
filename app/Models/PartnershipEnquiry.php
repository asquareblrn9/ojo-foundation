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
    'organisation', 'contact_name', 'email', 'phone', 'website', 'partnership_type', 'message',
    'status', 'internal_notes', 'reviewed_by', 'reviewed_at', 'ip_address',
])]
class PartnershipEnquiry extends Model
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

    public function activityName(): string
    {
        return $this->organisation;
    }
}
