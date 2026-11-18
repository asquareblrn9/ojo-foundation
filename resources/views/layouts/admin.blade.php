<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ setting('site_name') }} Admin</title>
    <x-ui.favicons />
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @stack('head')
</head>
<body class="h-full font-sans text-slate-700 antialiased">
<div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-full">

    {{-- Mobile sidebar backdrop --}}
    <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-navy-950/60 backdrop-blur-sm lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-navy-950 transition-transform duration-200 lg:translate-x-0"
           aria-label="Admin navigation">
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('admin.dashboard') }}" wire:navigate>
                <x-ui.logo variant="light" size="sm" />
            </a>
            <button type="button" @click="sidebarOpen = false" class="rounded-md p-1 text-navy-200 hover:text-white lg:hidden" aria-label="Close menu">
                <x-lucide-x class="size-5" />
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-6">
            @foreach ($navigation as $group)
                <div>
                    <p class="px-3 text-[11px] font-semibold tracking-[0.18em] text-navy-300/70 uppercase">{{ $group['heading'] }}</p>
                    <ul class="mt-2 space-y-0.5">
                        @foreach ($group['items'] as $item)
                            @php $active = $item['active']; @endphp
                            <li>
                                <a href="{{ $item['url'] }}" wire:navigate
                                   @class([
                                       'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                                       'bg-white/10 text-white' => $active,
                                       'text-navy-200 hover:bg-white/5 hover:text-white' => ! $active,
                                   ])
                                   @if ($active) aria-current="page" @endif>
                                    {{ svg('lucide-'.$item['icon'], ['class' => 'size-[18px] shrink-0 '.($active ? 'text-gold-300' : 'text-navy-300 group-hover:text-gold-300')]) }}
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if ($item['badge'])
                                        <span class="rounded-full bg-gold-400 px-2 py-0.5 text-[11px] font-bold text-navy-950">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-3">
            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-navy-200 hover:bg-white/5 hover:text-white">
                <x-lucide-external-link class="size-[18px] text-navy-300" />
                View website
            </a>
        </div>
    </aside>

    <div class="lg:pl-72">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <button type="button" @click="sidebarOpen = true" class="-ml-1 rounded-md p-1.5 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Open menu">
                <x-lucide-menu class="size-5" />
            </button>

            <h1 class="truncate text-base font-semibold text-navy-900">{{ $title ?? 'Dashboard' }}</h1>

            <div class="ml-auto" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" class="flex items-center gap-3 rounded-full py-1 pr-2 pl-1 hover:bg-slate-100" :aria-expanded="open">
                    <x-admin.avatar :user="$user" class="size-8 text-xs" />
                    <span class="hidden text-left sm:block">
                        <span class="block text-sm leading-tight font-medium text-navy-900">{{ $user->name }}</span>
                        <span class="block text-xs leading-tight text-slate-500">{{ $user->roleLabel() }}</span>
                    </span>
                    <x-lucide-chevron-down class="size-4 text-slate-400" />
                </button>

                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-4 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg sm:right-6 lg:right-8">
                    <a href="{{ route('admin.profile') }}" wire:navigate class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <x-lucide-user class="size-4 text-slate-400" /> My profile
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-rose-600 hover:bg-rose-50">
                            <x-lucide-log-out class="size-4" /> Sign out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            {{ $slot }}
        </main>
    </div>

    {{-- Toasts --}}
    <div x-data="toastStack(@js(session('toast')))" aria-live="polite"
         class="pointer-events-none fixed right-0 bottom-0 z-[60] flex w-full flex-col items-end gap-3 p-4 sm:max-w-sm">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition.opacity.duration.300ms
                 class="pointer-events-auto flex w-full items-start gap-3 rounded-xl border bg-white p-4 shadow-lg"
                 :class="toast.type === 'error' ? 'border-rose-200' : 'border-emerald-200'">
                <span class="mt-0.5 size-2.5 shrink-0 rounded-full" :class="toast.type === 'error' ? 'bg-rose-500' : 'bg-emerald-500'"></span>
                <p class="flex-1 text-sm text-slate-700" x-text="toast.message"></p>
                <button type="button" @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600" aria-label="Dismiss">
                    <x-lucide-x class="size-4" />
                </button>
            </div>
        </template>
    </div>
</div>

@stack('scripts')
</body>
</html>
