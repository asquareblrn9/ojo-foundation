{{--
    Renders plain CMS text safely: escapes HTML, splits paragraphs on blank
    lines and keeps single line breaks.
--}}
@props(['text' => null])

@if (filled($text))
    <div {{ $attributes->class('space-y-5') }}>
        @foreach (preg_split('/\R{2,}/', trim($text)) as $paragraph)
            <p>{!! nl2br(e($paragraph)) !!}</p>
        @endforeach
    </div>
@endif
