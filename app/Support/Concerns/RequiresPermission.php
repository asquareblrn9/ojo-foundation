<?php

namespace App\Support\Concerns;

use App\Enums\Permission;

/**
 * Re-checks the component's permission on every Livewire request
 * (initial render and every subsequent action), in addition to the
 * route middleware.
 */
trait RequiresPermission
{
    abstract protected function permission(): Permission;

    public function bootRequiresPermission(): void
    {
        abort_unless(auth()->user()?->can($this->permission()->value), 403);
    }
}
