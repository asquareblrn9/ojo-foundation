<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a query as a CSV download. Values are protected against CSV /
 * formula injection (cells starting with = + - @ are prefixed with ').
 */
class CsvExporter
{
    /** @param array<string, callable|string> $columns heading => attribute or callback */
    public static function download(Builder $query, array $columns, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly
            fputcsv($out, array_keys($columns), escape: '');

            $query->chunk(500, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    fputcsv($out, array_map(
                        fn ($column) => static::safe(is_callable($column) ? $column($row) : data_get($row, $column)),
                        array_values($columns),
                    ), escape: '');
                }
            });

            fclose($out);
        }, $filename.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public static function safe(mixed $value): string
    {
        $value = $value instanceof \BackedEnum ? $value->value : (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
