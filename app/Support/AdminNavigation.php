<?php

namespace App\Support;

use App\Enums\Permission;
use App\Models\ContactMessage;
use App\Models\PartnershipEnquiry;
use App\Models\VolunteerApplication;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * Admin sidebar definition. Items are shown only when the route exists and
 * the user holds the permission. Each item resolves to:
 *   label, url, icon, active (bool), badge (int|null)
 */
class AdminNavigation
{
    public const EVENTS_CATEGORY = 'events-activities';

    /** @return array<int, array{heading: string, items: array}> */
    public static function for(User $user): array
    {
        $request = request();
        $isEvents = $request->routeIs('admin.posts.*') && $request->query('category') === self::EVENTS_CATEGORY;

        $groups = [
            ['heading' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'can' => Permission::AccessDashboard],
            ]],
            ['heading' => 'Content', 'items' => [
                ['label' => 'Blog posts', 'route' => 'admin.posts.index', 'icon' => 'newspaper', 'can' => Permission::ManagePosts,
                    'active' => $request->routeIs('admin.posts.*') && ! $isEvents],
                ['label' => 'Events & activities', 'route' => 'admin.posts.index', 'params' => ['category' => self::EVENTS_CATEGORY], 'icon' => 'calendar-days', 'can' => Permission::ManagePosts,
                    'active' => $isEvents],
                ['label' => 'Categories & tags', 'route' => 'admin.categories', 'icon' => 'tags', 'can' => Permission::ManagePosts],
                ['label' => 'Programmes', 'route' => 'admin.programmes.index', 'icon' => 'hand-heart', 'can' => Permission::ManageProgrammes, 'pattern' => 'admin.programmes.*'],
                ['label' => 'Gallery photos', 'route' => 'admin.gallery.images', 'icon' => 'images', 'can' => Permission::ManageGallery, 'pattern' => ['admin.gallery.images', 'admin.gallery.categories']],
                ['label' => 'Albums', 'route' => 'admin.gallery.albums', 'icon' => 'folder-open', 'can' => Permission::ManageGallery],
                ['label' => 'Pages', 'route' => 'admin.pages.index', 'icon' => 'file-text', 'can' => Permission::ManagePages, 'pattern' => 'admin.pages.*'],
                ['label' => 'Team', 'route' => 'admin.team', 'icon' => 'contact', 'can' => Permission::ManagePages],
            ]],
            ['heading' => 'Engagement', 'items' => [
                ['label' => 'Messages', 'route' => 'admin.messages.index', 'icon' => 'mail', 'can' => Permission::ManageEnquiries, 'pattern' => 'admin.messages.*',
                    'badge' => fn () => ContactMessage::unread()->count()],
                ['label' => 'Volunteers', 'route' => 'admin.volunteers.index', 'icon' => 'users', 'can' => Permission::ManageEnquiries, 'pattern' => 'admin.volunteers.*',
                    'badge' => fn () => VolunteerApplication::where('status', 'pending')->count()],
                ['label' => 'Partnerships', 'route' => 'admin.partnerships.index', 'icon' => 'handshake', 'can' => Permission::ManageEnquiries, 'pattern' => 'admin.partnerships.*',
                    'badge' => fn () => PartnershipEnquiry::where('status', 'pending')->count()],
                ['label' => 'Subscribers', 'route' => 'admin.subscribers', 'icon' => 'at-sign', 'can' => Permission::ManageSubscribers],
            ]],
            ['heading' => 'System', 'items' => [
                ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'settings', 'can' => Permission::ManageSettings],
                ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'shield-check', 'can' => Permission::ManageUsers, 'pattern' => 'admin.users.*'],
                ['label' => 'Activity log', 'route' => 'admin.activity', 'icon' => 'activity', 'can' => Permission::ViewActivityLog],
            ]],
        ];

        return collect($groups)
            ->map(function (array $group) use ($user, $request) {
                $group['items'] = collect($group['items'])
                    ->filter(fn (array $item) => Route::has($item['route']) && $user->can($item['can']->value))
                    ->map(fn (array $item) => [
                        'label' => $item['label'],
                        'url' => route($item['route'], $item['params'] ?? []),
                        'icon' => $item['icon'],
                        'active' => $item['active'] ?? $request->routeIs(...(array) ($item['pattern'] ?? $item['route'])),
                        'badge' => isset($item['badge']) ? (($item['badge'])() ?: null) : null,
                    ])
                    ->values()
                    ->all();

                return $group;
            })
            ->filter(fn (array $group) => $group['items'] !== [])
            ->values()
            ->all();
    }
}
