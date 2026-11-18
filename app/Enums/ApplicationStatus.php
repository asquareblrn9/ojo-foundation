<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case Reviewing = 'reviewing';
    case Accepted = 'accepted';
    case Declined = 'declined';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Reviewing => 'sky',
            self::Accepted => 'emerald',
            self::Declined => 'rose',
        };
    }
}
