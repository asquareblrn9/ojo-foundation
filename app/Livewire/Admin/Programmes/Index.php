<?php

namespace App\Livewire\Admin\Programmes;

use App\Enums\Permission;
use App\Models\Programme;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Programmes')]
class Index extends Component
{
    use RequiresPermission, WithToasts;

    protected function permission(): Permission
    {
        return Permission::ManageProgrammes;
    }

    public function togglePublish(int $id, ActivityLogger $logger): void
    {
        $programme = Programme::findOrFail($id);
        $programme->update(['is_published' => ! $programme->is_published]);
        $logger->log($programme->is_published ? 'published' : 'unpublished', ($programme->is_published ? 'Published' : 'Unpublished')." programme \"{$programme->title}\"", $programme);
        $this->toast($programme->is_published ? 'Programme published.' : 'Programme unpublished.');
    }

    public function reorder(int $id, int $position): void
    {
        $ids = Programme::ordered()->pluck('id')->reject(fn ($i) => $i === $id)->values();
        $ids->splice($position, 0, [$id]);
        $ids->each(fn ($programmeId, $index) => Programme::whereKey($programmeId)->update(['sort_order' => $index]));
        $this->toast('Order updated.');
    }

    public function delete(int $id, ImageService $images): void
    {
        $programme = Programme::findOrFail($id);
        $images->deleteWithVariants($programme->featured_image);
        $programme->update(['featured_image' => null]);
        $programme->delete();
        $this->toast('Programme deleted.');
    }

    public function render()
    {
        return view('livewire.admin.programmes.index', ['programmes' => Programme::ordered()->get()]);
    }
}
