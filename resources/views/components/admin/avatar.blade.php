@props(['user'])

@if ($url = $user->avatarUrl())
    <img src="{{ $url }}" alt="" {{ $attributes->class('shrink-0 rounded-full object-cover') }}>
@else
    <span {{ $attributes->class('grid shrink-0 place-items-center rounded-full bg-navy-900 font-semibold text-gold-300') }} aria-hidden="true">{{ $user->initials() }}</span>
@endif
