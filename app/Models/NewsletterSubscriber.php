<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email', 'name', 'token', 'confirmed_at', 'unsubscribed_at', 'ip_address'])]
#[Hidden(['token'])]
class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            $subscriber->token ??= Str::random(48);
        });
    }

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }
}
