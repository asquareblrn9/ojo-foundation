<?php

namespace App\Livewire\Admin\Inbox;

use App\Enums\ApplicationStatus;
use App\Enums\Permission;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

/** Shared detail view: status, internal notes and delete. */
#[Layout('layouts.admin')]
abstract class ApplicationShow extends Component
{
    use RequiresPermission, WithToasts;

    #[Locked]
    public int $recordId;

    public string $status = '';

    public string $internal_notes = '';

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** @return array{title: string, indexRoute: string, nameField: string, fields: array<string, string>} */
    abstract protected function config(): array;

    protected function permission(): Permission
    {
        return Permission::ManageEnquiries;
    }

    protected function load(Model $record): void
    {
        $this->recordId = $record->getKey();
        $this->status = $record->status->value;
        $this->internal_notes = (string) $record->internal_notes;
    }

    public function save(): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(ApplicationStatus::class)],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        ($this->model())::findOrFail($this->recordId)->update([
            'status' => ApplicationStatus::from($this->status),
            'internal_notes' => $this->internal_notes ?: null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $this->toast('Saved.');
    }

    public function delete(): void
    {
        ($this->model())::findOrFail($this->recordId)->delete();
        $this->flashToast('Deleted.');
        $this->redirectRoute($this->config()['indexRoute'], navigate: true);
    }

    public function render()
    {
        $record = ($this->model())::with('reviewer:id,name')->findOrFail($this->recordId);

        return view('livewire.admin.inbox.application-show', [
            'record' => $record,
            'statuses' => ApplicationStatus::cases(),
            ...$this->config(),
        ])->title($this->config()['title']);
    }
}
