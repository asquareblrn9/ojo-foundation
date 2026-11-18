{{-- Editable list of short text items bound to an array property (e.g. programme objectives). --}}
@props(['model', 'label', 'placeholder' => 'Add an item…', 'help' => null])

<div {{ $attributes }} x-data="{ items: $wire.entangle('{{ $model }}'), draft: '' ,
        add() { const v = this.draft.trim(); if (v) { this.items = [...(this.items || []), v]; } this.draft = ''; },
        remove(i) { this.items = this.items.filter((_, idx) => idx !== i); },
        move(i, d) { const a = [...this.items]; const j = i + d; if (j < 0 || j >= a.length) return; [a[i], a[j]] = [a[j], a[i]]; this.items = a; } }">
    <p class="form-label">{{ $label }}</p>
    <ul class="space-y-2">
        <template x-for="(item, i) in (items || [])" :key="i + ':' + item">
            <li class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2">
                <input type="text" class="flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 focus:ring-0 focus:outline-none" :value="item"
                       @change="items = items.map((v, idx) => idx === i ? $event.target.value : v)" aria-label="Item">
                <button type="button" @click="move(i, -1)" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Move up"><x-lucide-chevron-up class="size-4" /></button>
                <button type="button" @click="move(i, 1)" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Move down"><x-lucide-chevron-down class="size-4" /></button>
                <button type="button" @click="remove(i)" class="rounded p-1 text-slate-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Remove"><x-lucide-x class="size-4" /></button>
            </li>
        </template>
    </ul>
    <div class="mt-2 flex gap-2">
        <input type="text" x-model="draft" @keydown.enter.prevent="add()" placeholder="{{ $placeholder }}" class="form-input">
        <button type="button" @click="add()" class="shrink-0 rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Add</button>
    </div>
    @error($model) <p class="form-error">{{ $message }}</p> @enderror
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
</div>
