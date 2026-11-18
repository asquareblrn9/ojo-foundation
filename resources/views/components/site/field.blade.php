{{--
    Public form field. type: text|email|tel|url|textarea|select.
    Usage: <x-site.field label="Name" model="name" required />
--}}
@props(['label', 'model', 'type' => 'text', 'required' => false, 'options' => [], 'rows' => 5, 'placeholder' => null, 'autocomplete' => null])

@php $id = 'f-'.$model; @endphp

<div {{ $attributes }}>
    <label for="{{ $id }}" class="site-label">{{ $label }} @if ($required)<span class="text-rose-500" aria-hidden="true">*</span>@else<span class="font-normal text-slate-400">(optional)</span>@endif</label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" wire:model="{{ $model }}" rows="{{ $rows }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                  @if ($required) aria-required="true" @endif
                  @error($model) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
                  @class(['site-input resize-y', 'border-rose-400' => $errors->has($model)])></textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" wire:model="{{ $model }}" @if ($required) aria-required="true" @endif
                @error($model) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
                @class(['site-input', 'border-rose-400' => $errors->has($model)])>
            <option value="">Please choose…</option>
            @foreach ($options as $option)
                <option value="{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input id="{{ $id }}" type="{{ $type }}" wire:model="{{ $model }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               @if ($required) aria-required="true" @endif
               @error($model) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
               @class(['site-input', 'border-rose-400' => $errors->has($model)])>
    @endif

    @error($model)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
