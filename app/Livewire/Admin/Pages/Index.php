<?php

namespace App\Livewire\Admin\Pages;

use App\Enums\Permission;
use App\Models\Page;
use App\Support\Concerns\RequiresPermission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Pages')]
class Index extends Component
{
    use RequiresPermission;

    /** Public URL for each system page. */
    public const URLS = [
        'home' => 'home', 'about' => 'about', 'programmes' => 'programmes.index', 'get-involved' => 'get-involved',
        'contact' => 'contact', 'privacy-policy' => 'privacy', 'terms' => 'terms',
    ];

    protected function permission(): Permission
    {
        return Permission::ManagePages;
    }

    public function render()
    {
        $order = array_flip(array_keys(self::URLS));

        return view('livewire.admin.pages.index', [
            'pages' => Page::withCount('sections')->get()->sortBy(fn (Page $p) => $order[$p->slug] ?? PHP_INT_MAX)->values(),
            'urls' => self::URLS,
        ]);
    }
}
