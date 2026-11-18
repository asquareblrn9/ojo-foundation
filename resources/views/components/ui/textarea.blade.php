@props(['label' => null, 'model', 'rows' => 3, 'help' => null, 'required' => false, 'counter' => null])

@php $id = 'f-'.str_replace('.', '-', $model); @endphp

<x-ui.field :label="$label" :for="$id" :error="$model" :help="$help" :required="$required" :class="$attributes->get('class')">
    <div @if ($counter) x-data="{ n: ($wire.get('{{ $model }}') || '').length }" @endif>
        <textarea id="{{ $id }}" rows="{{ $rows }}" wire:model="{{ $model }}"
                  @if ($counter) x-on:input="n = $event.target.value.length" @endif
                  @error($model) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
                  {{ $attributes->except('class')->class(['form-input', 'form-input-error' => $errors->has($model)]) }}></textarea>
        @if ($counter)
            <p class="mt-1 text-right text-xs" :class="n > {{ $counter }} ? 'text-rose-600' : 'text-slate-400'"><span x-text="n"></span>/{{ $counter }}</p>
        @endif
    </div>
</x-ui.field>
