<?php

namespace App\Support\Concerns;

/** Shared validation rule for admin image uploads. */
trait HandlesImageUpload
{
    protected function imageRule(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:'.implode(',', config('foundation.uploads.image_mimes')),
            'max:'.config('foundation.uploads.image_max_kb'),
            'dimensions:max_width=8000,max_height=8000',
        ];
    }
}
