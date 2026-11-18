{{-- Text input bound with wire:model. Usage: <x-ui.input label="Email" model="email" type="email" /> --}}
@props(['label' => null, 'model', 'type' => 'text', 'help' => null, 'required' => false, 'live' => false])

@php $id = 'f-'.str_replace('.', '-', $model); @endphp

<x-ui.field :label="$label" :for="$id" :error="$model" :help="$help" :required="$required" :class="$attributes->get('class')">
    <input id="{{ $id }}" type="{{ $type }}"
           @if ($live) wire:model.live.blur="{{ $model }}" @else wire:model="{{ $model }}" @endif
           @error($model) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
           {{ $attributes->except('class')->class(['form-input', 'form-input-error' => $errors->has($model)]) }}>
</x-ui.field>
