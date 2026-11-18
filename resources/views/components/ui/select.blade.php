{{-- <x-ui.select label="Category" model="category_id" :options="[id => label]" placeholder="None" /> --}}
@props(['label' => null, 'model', 'options' => [], 'placeholder' => null, 'help' => null, 'required' => false, 'live' => false])

@php $id = 'f-'.str_replace('.', '-', $model); @endphp

<x-ui.field :label="$label" :for="$id" :error="$model" :help="$help" :required="$required" :class="$attributes->get('class')">
    <select id="{{ $id }}" @if ($live) wire:model.live="{{ $model }}" @else wire:model="{{ $model }}" @endif
            {{ $attributes->except('class')->class(['form-input', 'form-input-error' => $errors->has($model)]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}">{{ $optionLabel }}</option>
        @endforeach
    </select>
</x-ui.field>
