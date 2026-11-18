<?php

namespace App\Support\Concerns;

use App\Services\ActivityLogger;

/**
 * Records create/update/delete of the model to the activity log
 * when performed by an authenticated user.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn ($model) => $model->recordActivity('created'));
        static::updated(fn ($model) => $model->recordActivity('updated'));
        static::deleted(fn ($model) => $model->recordActivity('deleted'));
    }

    protected function recordActivity(string $action): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = $action === 'updated'
            ? array_keys(array_diff_key($this->getChanges(), array_flip(['updated_at', 'remember_token', 'password'])))
            : [];

        if ($action === 'updated' && $changes === []) {
            return;
        }

        app(ActivityLogger::class)->log(
            $action,
            sprintf('%s %s "%s"', ucfirst($action), $this->activityLabel(), $this->activityName()),
            $this,
            $changes ? ['changed' => $changes] : [],
        );
    }

    public function activityLabel(): string
    {
        return strtolower(trim(preg_replace('/(?<!^)[A-Z]/', ' $0', class_basename($this))));
    }

    public function activityName(): string
    {
        return (string) ($this->title ?? $this->name ?? $this->full_name ?? $this->email ?? "#{$this->getKey()}");
    }
}
