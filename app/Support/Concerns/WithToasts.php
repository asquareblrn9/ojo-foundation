<?php

namespace App\Support\Concerns;

/** Dispatches toast notifications to the admin layout's toast stack. */
trait WithToasts
{
    protected function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('toast', type: $type, message: $message);
    }

    /** Flash a toast that survives a redirect. */
    protected function flashToast(string $message, string $type = 'success'): void
    {
        session()->flash('toast', ['type' => $type, 'message' => $message]);
    }
}
