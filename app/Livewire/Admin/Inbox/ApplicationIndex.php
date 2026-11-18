<?php

namespace App\Livewire\Admin\Inbox;

use App\Enums\ApplicationStatus;
use App\Enums\Permission;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Shared listing for status-tracked submissions (volunteers, partnerships). */
#[Layout('layouts.admin')]
abstract class ApplicationIndex extends Component
{
    use RequiresPermission, WithPagination, WithToasts;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** @return list<string> Columns searched by the search box. */
    abstract protected function searchable(): array;

    abstract protected function config(): array;

    protected function permission(): Permission
    {
        return Permission::ManageEnquiries;
    }

    public function updated($property): void
    {
        $this->resetPage();
    }

    public function setStatus(int $id, string $status): void
    {
        $newStatus = ApplicationStatus::tryFrom($status) ?? abort(422);
        ($this->model())::findOrFail($id)->update([
            'status' => $newStatus,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        $this->toast('Status updated to '.$newStatus->label().'.');
    }

    public function delete(int $id): void
    {
        ($this->model())::findOrFail($id)->delete();
        $this->toast('Deleted.');
    }

    public function render()
    {
        $model = $this->model();
        $term = trim($this->search);

        $records = $model::query()
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => collect($this->searchable())->each(fn ($col) => $w->orWhere($col, 'like', "%{$term}%"))))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(20);

        $counts = $model::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('livewire.admin.inbox.applications', [
            'records' => $records,
            'counts' => $counts,
            'statuses' => ApplicationStatus::cases(),
            ...$this->config(),
        ])->title($this->config()['title']);
    }
}
