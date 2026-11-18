{{--
    Single image upload with preview, progress and removal.
    <x-admin.image-upload model="image" :upload="$image" :current="$existingUrl" remove="removeImage" />
--}}
@props(['model', 'upload' => null, 'current' => null, 'label' => 'Image', 'help' => 'JPG, PNG or WebP up to 5MB.', 'remove' => null, 'aspect' => 'aspect-[16/9]', 'accept' => 'image/jpeg,image/png,image/webp'])

@php
    $preview = $current;
    if ($upload && is_object($upload) && method_exists($upload, 'isPreviewable') && $upload->isPreviewable()) {
        try { $preview = $upload->temporaryUrl(); } catch (\Throwable) { $preview = $current; }
    }
@endphp

<div {{ $attributes }} x-data="{ progress: null }"
     x-on:livewire-upload-start="progress = 0" x-on:livewire-upload-finish="progress = null"
     x-on:livewire-upload-error="progress = null" x-on:livewire-upload-progress="progress = $event.detail.progress">
    <p class="form-label">{{ $label }}</p>
    <div @class(['relative overflow-hidden rounded-xl border-2 border-dashed bg-slate-50', $aspect, 'border-slate-300' => ! $errors->has($model), 'border-rose-400' => $errors->has($model)])>
        @if ($preview)
            <img src="{{ $preview }}" alt="" class="absolute inset-0 size-full object-cover">
        @else
            <div class="absolute inset-0 grid place-items-center text-center text-slate-400">
                <div><x-lucide-image class="mx-auto size-8" /><p class="mt-2 text-xs">No image</p></div>
            </div>
        @endif

        <div x-cloak x-show="progress !== null" class="absolute inset-x-0 bottom-0 h-1.5 bg-slate-200">
            <div class="h-full bg-gold-500 transition-all" :style="`width: ${progress}%`"></div>
        </div>
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
            <x-lucide-upload class="size-4" /> {{ $preview ? 'Replace' : 'Upload' }}
            <input type="file" wire:model="{{ $model }}" accept="{{ $accept }}" class="sr-only">
        </label>
        @if ($remove && $preview)
            <button type="button" wire:click="{{ $remove }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">
                <x-lucide-trash-2 class="size-4" /> Remove
            </button>
        @endif
    </div>
    @error($model) <p class="form-error">{{ $message }}</p> @enderror
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
</div>
