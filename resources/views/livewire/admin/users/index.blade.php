<div>
    <x-admin.page-header title="Users" description="People who can sign in to this dashboard.">
        <x-slot:actions>
            <x-ui.button :href="route('admin.users.create')" wire:navigate><x-lucide-user-plus class="size-4" /> Add user</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($issuedPassword)
        <div class="mb-6 rounded-2xl border border-gold-300 bg-gold-50 p-5" x-data="{ copied: false }">
            <div class="flex items-start gap-3">
                <x-lucide-key-round class="mt-0.5 size-5 shrink-0 text-gold-700" />
                <div class="flex-1">
                    <p class="font-semibold text-navy-900">Temporary password for {{ $issuedPassword['email'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Share it securely. It is shown only once, and they must choose a new password when they sign in.</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <code class="rounded-lg bg-white px-3 py-2 font-mono text-base tracking-wider text-navy-900 ring-1 ring-gold-200">{{ $issuedPassword['password'] }}</code>
                        <button type="button" class="text-sm font-semibold text-navy-700 hover:text-navy-900"
                                @click="navigator.clipboard.writeText(@js($issuedPassword['password'])); copied = true">
                            <span x-show="!copied">Copy</span><span x-cloak x-show="copied">Copied</span>
                        </button>
                    </div>
                </div>
                <button type="button" wire:click="$set('issuedPassword', null)" class="icon-btn" aria-label="Dismiss"><x-lucide-x class="size-4" /></button>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="border-b border-slate-100 p-4">
            <div class="relative max-w-sm">
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search users…" class="form-input pl-9" aria-label="Search users">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead><tr><th>User</th><th>Role</th><th class="hidden md:table-cell">Status</th><th class="hidden lg:table-cell">Last sign-in</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        @php $isMe = $user->is(auth()->user()); $role = $user->roles->first()?->name; @endphp
                        <tr wire:key="user-{{ $user->id }}" @class(['opacity-60' => ! $user->is_active])>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-admin.avatar :user="$user" class="size-9 text-xs" />
                                    <div>
                                        <p class="font-medium text-navy-900">{{ $user->name }} @if ($isMe)<span class="text-xs font-normal text-slate-400">(you)</span>@endif</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><x-admin.badge :color="$roleColors[$role] ?? 'slate'">{{ $user->roleLabel() }}</x-admin.badge></td>
                            <td class="hidden md:table-cell">
                                @if (! $user->is_active) <x-admin.badge color="rose">Deactivated</x-admin.badge>
                                @elseif ($user->must_change_password) <x-admin.badge color="amber">Must change password</x-admin.badge>
                                @else <x-admin.badge color="emerald">Active</x-admin.badge> @endif
                            </td>
                            <td class="hidden text-slate-500 lg:table-cell">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $user) }}" wire:navigate class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></a>
                                    @can('resetPassword', $user)
                                        <button type="button" wire:click="resetPassword({{ $user->id }})" wire:confirm="Reset {{ $user->name }}'s password? They will need the new temporary password to sign in." class="icon-btn" title="Reset password"><x-lucide-key-round class="size-4" /></button>
                                    @endcan
                                    @if ($user->is_active)
                                        @can('deactivate', $user)
                                            <button type="button" wire:click="toggleActive({{ $user->id }})" wire:confirm="Deactivate {{ $user->name }}? They will be signed out immediately." class="icon-btn" title="Deactivate"><x-lucide-ban class="size-4" /></button>
                                        @endcan
                                    @else
                                        <button type="button" wire:click="toggleActive({{ $user->id }})" class="icon-btn" title="Reactivate"><x-lucide-power class="size-4" /></button>
                                    @endif
                                    @can('delete', $user)
                                        <button type="button" wire:click="delete({{ $user->id }})" wire:confirm="Delete {{ $user->name }}'s account?" class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-3">
        @foreach (\App\Enums\Role::cases() as $r)
            <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm">
                <p class="font-semibold text-navy-900">{{ $r->label() }}</p>
                <p class="mt-1 text-slate-500">
                    @switch($r)
                        @case(\App\Enums\Role::SuperAdmin) Full access, including users and the activity log. @break
                        @case(\App\Enums\Role::Administrator) All content, enquiries, subscribers and settings. @break
                        @default Blog, events, programmes, gallery and page content only.
                    @endswitch
                </p>
            </div>
        @endforeach
    </div>
</div>
