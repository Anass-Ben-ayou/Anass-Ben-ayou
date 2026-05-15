<?php

namespace App\Support;

use Illuminate\Support\Str;

class SanitizesInput
{
    public static function plain(mixed $value, ?int $limit = null): string
    {
        $value = trim(strip_tags((string) $value));
        $value = preg_replace('/[^\P{C}\t\r\n]+/u', '', $value) ?? $value;
        $value = preg_replace('/[ \t]+/u', ' ', $value) ?? $value;

        return $limit ? Str::limit($value, $limit, '') : $value;
    }

    public static function paragraph(mixed $value, ?int $limit = null): string
    {
        $value = trim(strip_tags((string) $value));
        $value = preg_replace('/\R/u', "\n", $value) ?? $value;
        $value = preg_replace('/[^\P{C}\n]+/u', '', $value) ?? $value;
        $value = preg_replace('/[ \t]+/u', ' ', $value) ?? $value;
        $value = preg_replace("/\n{3,}/u", "\n\n", $value) ?? $value;

        return $limit ? Str::limit($value, $limit, '') : $value;
    }

    public static function email(mixed $value): string
    {
        return mb_strtolower(self::plain($value, 255));
    }

    public static function urlOrPath(mixed $value): ?string
    {
        $value = self::plain($value, 500);

        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $scheme = parse_url($value, PHP_URL_SCHEME);

            return in_array($scheme, ['http', 'https'], true) ? $value : null;
        }

        $normalized = ltrim($value, '/');

        if (str_contains($normalized, '..') || str_starts_with($normalized, '//')) {
            return null;
        }

        return Str::startsWith($normalized, ['storage/', 'catalog-import/'])
            ? '/'.$normalized
            : null;
    }

    public static function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($item) => self::urlOrPath($item))
            ->filter()
            ->values()
            ->all();
    }

    public static function plainMap(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->mapWithKeys(function ($item, $key) {
                $safeKey = self::plain($key, 80);
                $safeValue = self::plain($item, 500);

                return $safeKey !== '' && $safeValue !== '' ? [$safeKey => $safeValue] : [];
            })
            ->all();
    }
}
