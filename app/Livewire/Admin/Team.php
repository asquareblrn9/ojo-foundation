<?php

namespace App\Livewire\Admin;

use App\Enums\Permission;
use App\Models\TeamMember;
use App\Services\ImageService;
use App\Support\Concerns\HandlesImageUpload;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use App\Support\ResponsiveImage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/** Leadership / team members shown on the About page. */
#[Layout('layouts.admin')]
#[Title('Team')]
class Team extends Component
{
    use HandlesImageUpload, RequiresPermission, WithFileUploads, WithToasts;

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public string $name = '';

    public string $position = '';

    public string $bio = '';

    public bool $is_active = true;

    public $photo;

    public bool $removePhoto = false;

    protected function permission(): Permission
    {
        return Permission::ManagePages;
    }

    public function create(): void
    {
        $this->reset('editingId', 'name', 'position', 'bio', 'photo', 'removePhoto');
        $this->is_active = true;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        $member = TeamMember::findOrFail($id);
        $this->reset('photo', 'removePhoto');
        $this->editingId = $member->id;
        $this->name = $member->name;
        $this->position = (string) $member->position;
        $this->bio = (string) $member->bio;
        $this->is_active = $member->is_active;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function clearPhoto(): void
    {
        $this->reset('photo');
        $this->removePhoto = true;
    }

    public function save(ImageService $images): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'position' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
            'photo' => $this->imageRule(),
        ]);
        unset($data['photo']);

        $member = $this->editingId ? TeamMember::findOrFail($this->editingId) : new TeamMember(['sort_order' => (int) TeamMember::max('sort_order') + 1]);
        $member->fill($data);

        if ($this->photo) {
            $images->deleteWithVariants($member->photo);
            $member->photo = $images->store($this->photo, 'team')['path'];
        } elseif ($this->removePhoto && $member->photo) {
            $images->deleteWithVariants($member->photo);
            $member->photo = null;
        }

        $member->save();
        $this->showForm = false;
        $this->toast('Team member saved.');
    }

    public function reorder(int $id, int $position): void
    {
        $ids = TeamMember::orderBy('sort_order')->pluck('id')->reject(fn ($i) => $i === $id)->values();
        $ids->splice($position, 0, [$id]);
        $ids->each(fn ($memberId, $index) => TeamMember::whereKey($memberId)->update(['sort_order' => $index]));
    }

    public function delete(int $id, ImageService $images): void
    {
        $member = TeamMember::findOrFail($id);
        $images->deleteWithVariants($member->photo);
        $member->delete();
        $this->toast('Team member removed.');
    }

    public function render()
    {
        $current = $this->editingId && ! $this->removePhoto ? TeamMember::find($this->editingId)?->photo : null;

        return view('livewire.admin.team', [
            'members' => TeamMember::orderBy('sort_order')->get(),
            'currentPhotoUrl' => ResponsiveImage::variant($current, 'sm'),
        ]);
    }
}
