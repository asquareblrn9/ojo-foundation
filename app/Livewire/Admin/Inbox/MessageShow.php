<?php

namespace App\Livewire\Admin\Inbox;

use App\Enums\Permission;
use App\Models\ContactMessage;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Message')]
class MessageShow extends Component
{
    use RequiresPermission, WithToasts;

    #[Locked]
    public int $messageId;

    protected function permission(): Permission
    {
        return Permission::ManageEnquiries;
    }

    public function mount(ContactMessage $message): void
    {
        $this->messageId = $message->id;

        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }
    }

    public function markUnread(): void
    {
        ContactMessage::findOrFail($this->messageId)->update(['read_at' => null]);
        $this->flashToast('Marked as unread.');
        $this->redirectRoute('admin.messages.index', navigate: true);
    }

    public function toggleResolved(): void
    {
        $message = ContactMessage::findOrFail($this->messageId);
        $message->update(['resolved_at' => $message->resolved_at ? null : now()]);
        $this->toast($message->resolved_at ? 'Marked as resolved.' : 'Reopened.');
    }

    public function delete(): void
    {
        ContactMessage::findOrFail($this->messageId)->delete();
        $this->flashToast('Message deleted.');
        $this->redirectRoute('admin.messages.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.inbox.message-show', ['message' => ContactMessage::findOrFail($this->messageId)]);
    }
}
