@props(['label' => null, 'model', 'help' => null, 'autocomplete' => 'current-password', 'required' => false])

@php $id = 'f-'.$model; @endphp

<x-ui.field :label="$label" :for="$id" :error="$model" :help="$help" :required="$required" :class="$attributes->get('class')">
    <div x-data="{ show: false }" class="relative">
        <input id="{{ $id }}" :type="show ? 'text' : 'password'" type="password" wire:model="{{ $model }}" autocomplete="{{ $autocomplete }}"
               @error($model) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
               @class(['form-input pr-11', 'form-input-error' => $errors->has($model)])>
        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-slate-600"
                :aria-label="show ? 'Hide password' : 'Show password'">
            <x-lucide-eye x-show="!show" class="size-4" />
            <x-lucide-eye-off x-cloak x-show="show" class="size-4" />
        </button>
    </div>
</x-ui.field>
