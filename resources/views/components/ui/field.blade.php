{{-- Form field wrapper: label, control slot, help text and validation error. --}}
@props(['label' => null, 'for' => null, 'error' => null, 'help' => null, 'required' => false])

<div {{ $attributes }}>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif class="form-label">
            {{ $label }} @if ($required)<span class="text-rose-500" aria-hidden="true">*</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if ($error)
        @error($error)
            <p class="form-error" id="{{ $for }}-error">{{ $message }}</p>
        @enderror
    @endif

    @if ($help)
        <p class="form-help">{{ $help }}</p>
    @endif
</div>
