<?php

use Tests\TestCase;

uses(TestCase::class);

it('turns relative paths into absolute site URLs', function () {
    expect(safe_url('/get-involved'))->toBe(url('/get-involved'));
});

it('allows safe links and neutralises dangerous ones', function (?string $input, string $expected) {
    expect(safe_url($input))->toBe($expected);
})->with([
    'anchor' => ['#donate', '#donate'],
    'https' => ['https://example.org', 'https://example.org'],
    'mailto' => ['mailto:info@example.org', 'mailto:info@example.org'],
    'javascript' => ['javascript:alert(1)', '#'],
    'protocol-relative' => ['//evil.example', '#'],
    'data uri' => ['data:text/html,hi', '#'],
    'empty' => [null, '#'],
]);
