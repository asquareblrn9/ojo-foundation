<div>
    <x-admin.page-header :title="'Welcome, '.strtok(auth()->user()->name, ' ')" description="Here's an overview of the foundation's website." />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->stats as $stat)
            <x-admin.stat-card :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" />
        @endforeach
    </div>

    <section class="card mt-8">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-navy-900">Recent activity</h3>
        </div>

        @forelse ($this->recentActivity as $log)
            <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-3.5 last:border-0">
                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-gold-500"></span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-slate-700">
                        <span class="font-medium text-navy-900">{{ $log->user?->name ?? 'System' }}</span>
                        — {{ $log->description }}
                    </p>
                    <p class="text-xs text-slate-400" title="{{ $log->created_at->toDayDateTimeString() }}">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <x-lucide-activity class="mx-auto size-8 text-slate-300" />
                <p class="mt-2 text-sm text-slate-500">No activity recorded yet.</p>
            </div>
        @endforelse
    </section>
</div>
