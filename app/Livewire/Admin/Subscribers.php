<?php

namespace App\Livewire\Admin;

use App\Enums\Permission;
use App\Models\NewsletterSubscriber;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Newsletter subscribers')]
class Subscribers extends Component
{
    use RequiresPermission, WithPagination, WithToasts;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** active | pending | unsubscribed | all */
    #[Url(except: 'active')]
    public string $filter = 'active';

    protected function permission(): Permission
    {
        return Permission::ManageSubscribers;
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        $this->toast('Subscriber removed.');
    }

    public function render()
    {
        $term = trim($this->search);
        $scopes = [
            'active' => fn ($q) => $q->active(),
            'pending' => fn ($q) => $q->whereNull('confirmed_at')->whereNull('unsubscribed_at'),
            'unsubscribed' => fn ($q) => $q->whereNotNull('unsubscribed_at'),
            'all' => fn ($q) => $q,
        ];

        return view('livewire.admin.subscribers', [
            'subscribers' => NewsletterSubscriber::query()
                ->tap($scopes[$this->filter] ?? $scopes['active'])
                ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('email', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%")))
                ->latest()
                ->paginate(25),
            'counts' => collect($scopes)->map(fn ($scope) => NewsletterSubscriber::query()->tap($scope)->count()),
        ]);
    }
}
