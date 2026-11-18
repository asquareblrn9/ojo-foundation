{{--
    Rich-text editor bound to a Livewire property. Output is sanitised
    server-side with HTMLPurifier when saved.
    <x-admin.rich-editor model="body" label="Content" />
    The Livewire component must use WithFileUploads + WithEditorUploads for image uploads.
--}}
@props(['model', 'label' => null, 'placeholder' => 'Start writing…', 'images' => true, 'help' => null])

@php
    $buttons = [
        ['bold', 'toggleBold', 'bold', [], 'Bold'],
        ['italic', 'toggleItalic', 'italic', [], 'Italic'],
        ['underline', 'toggleUnderline', 'underline', [], 'Underline'],
        ['heading-2', 'toggleHeading', 'heading', ['level' => 2], 'Heading'],
        ['heading-3', 'toggleHeading', 'heading', ['level' => 3], 'Subheading'],
        ['list', 'toggleBulletList', 'bulletList', [], 'Bullet list'],
        ['list-ordered', 'toggleOrderedList', 'orderedList', [], 'Numbered list'],
        ['text-quote', 'toggleBlockquote', 'blockquote', [], 'Quote'],
    ];
@endphp

<div {{ $attributes }}>
    @if ($label)
        <p class="form-label">{{ $label }}</p>
    @endif

    <div wire:ignore x-data="richEditor($wire.entangle('{{ $model }}'), @js($placeholder))"
         @class(['overflow-hidden rounded-xl border bg-white shadow-sm focus-within:border-navy-500 focus-within:ring-4 focus-within:ring-navy-500/10', 'border-slate-300'])>
        <div class="flex flex-wrap items-center gap-0.5 border-b border-slate-200 bg-slate-50 px-2 py-1.5" role="toolbar" aria-label="Formatting">
            @foreach ($buttons as [$icon, $command, $type, $attrs, $title])
                <button type="button" title="{{ $title }}" aria-label="{{ $title }}"
                        @click="run('{{ $command }}'{{ $attrs ? ', '.json_encode($attrs) : '' }})"
                        :class="isActive('{{ $type }}', {{ json_encode((object) $attrs) }}) ? 'bg-navy-900 text-white' : 'text-slate-600 hover:bg-slate-200'"
                        class="grid size-8 place-items-center rounded-md transition">
                    {{ svg('lucide-'.$icon, ['class' => 'size-4']) }}
                </button>
            @endforeach
            <span class="mx-1 h-5 w-px bg-slate-300"></span>
            <button type="button" title="Link" aria-label="Link" @click="setLink()"
                    :class="isActive('link') ? 'bg-navy-900 text-white' : 'text-slate-600 hover:bg-slate-200'" class="grid size-8 place-items-center rounded-md">
                <x-lucide-link class="size-4" />
            </button>
            <button type="button" title="Divider" aria-label="Divider" @click="run('setHorizontalRule')" class="grid size-8 place-items-center rounded-md text-slate-600 hover:bg-slate-200">
                <x-lucide-minus class="size-4" />
            </button>
            @if ($images)
                <button type="button" title="Insert image" aria-label="Insert image" @click="pickImage()" :disabled="uploading"
                        class="grid size-8 place-items-center rounded-md text-slate-600 hover:bg-slate-200 disabled:opacity-50">
                    <x-lucide-image-plus class="size-4" />
                </button>
                <input type="file" x-ref="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="uploadImage($event)">
            @endif
            <span class="mx-1 h-5 w-px bg-slate-300"></span>
            <button type="button" title="Undo" aria-label="Undo" @click="run('undo')" class="grid size-8 place-items-center rounded-md text-slate-600 hover:bg-slate-200"><x-lucide-undo-2 class="size-4" /></button>
            <button type="button" title="Redo" aria-label="Redo" @click="run('redo')" class="grid size-8 place-items-center rounded-md text-slate-600 hover:bg-slate-200"><x-lucide-redo-2 class="size-4" /></button>
            <span x-cloak x-show="uploading" class="ml-auto pr-2 text-xs text-slate-500">Uploading image… <span x-text="uploadProgress + '%'"></span></span>
        </div>
        <div x-ref="editor"></div>
        <p x-cloak x-show="error" x-text="error" class="border-t border-rose-100 bg-rose-50 px-4 py-2 text-sm text-rose-700"></p>
    </div>

    @error($model) <p class="form-error">{{ $message }}</p> @enderror
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
</div>
