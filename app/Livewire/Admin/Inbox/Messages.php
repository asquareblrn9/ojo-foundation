<?php

namespace App\Livewire\Admin\Inbox;

use App\Enums\Permission;
use App\Models\ContactMessage;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Messages')]
class Messages extends Component
{
    use RequiresPermission, WithPagination, WithToasts;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** all | unread | open | resolved */
    #[Url(except: 'all')]
    public string $filter = 'all';

    protected function permission(): Permission
    {
        return Permission::ManageEnquiries;
    }

    public function updated($property): void
    {
        $this->resetPage();
    }

    public function toggleRead(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['read_at' => $message->read_at ? null : now()]);
    }

    public function toggleResolved(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['resolved_at' => $message->resolved_at ? null : now(), 'read_at' => $message->read_at ?? now()]);
        $this->toast($message->resolved_at ? 'Marked as resolved.' : 'Reopened.');
    }

    public function delete(int $id): void
    {
        ContactMessage::findOrFail($id)->delete();
        $this->toast('Message deleted.');
    }

    public function render()
    {
        $term = trim($this->search);

        return view('livewire.admin.inbox.messages', [
            'messages' => ContactMessage::query()
                ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")->orWhere('subject', 'like', "%{$term}%")))
                ->when($this->filter === 'unread', fn ($q) => $q->whereNull('read_at'))
                ->when($this->filter === 'open', fn ($q) => $q->whereNull('resolved_at'))
                ->when($this->filter === 'resolved', fn ($q) => $q->whereNotNull('resolved_at'))
                ->latest()
                ->paginate(20),
            'counts' => [
                'all' => ContactMessage::count(),
                'unread' => ContactMessage::whereNull('read_at')->count(),
                'open' => ContactMessage::whereNull('resolved_at')->count(),
                'resolved' => ContactMessage::whereNotNull('resolved_at')->count(),
            ],
        ]);
    }
}
