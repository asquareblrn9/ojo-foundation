<?php

namespace App\Livewire\Admin;

use App\Enums\PostStatus;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\GalleryImage;
use App\Models\Post;
use App\Models\Programme;
use App\Models\VolunteerApplication;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        $posts = Post::query()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as published', [PostStatus::Published->value])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as drafts', [PostStatus::Draft->value])
            ->first();

        return [
            ['label' => 'Total blog posts', 'value' => (int) $posts->total, 'icon' => 'newspaper'],
            ['label' => 'Published articles', 'value' => (int) $posts->published, 'icon' => 'circle-check'],
            ['label' => 'Draft articles', 'value' => (int) $posts->drafts, 'icon' => 'file-pen-line'],
            ['label' => 'Gallery images', 'value' => GalleryImage::count(), 'icon' => 'images'],
            ['label' => 'Programmes', 'value' => Programme::count(), 'icon' => 'hand-heart'],
            ['label' => 'Unread enquiries', 'value' => ContactMessage::unread()->count(), 'icon' => 'mail'],
            ['label' => 'Volunteer applications', 'value' => VolunteerApplication::count(), 'icon' => 'users'],
        ];
    }

    #[Computed]
    public function recentActivity()
    {
        return ActivityLog::with('user:id,name')->latest('created_at')->limit(8)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
