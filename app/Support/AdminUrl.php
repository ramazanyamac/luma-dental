<?php

namespace App\Support;

class AdminUrl
{
    public static function route(
        string $name,
        array $parameters = [],
        ?string $locale = null
    ): string {
        if (str_starts_with($name, 'tr.')) {
            $name = substr($name, 3);
        }

        $locale ??= app()->getLocale();

        return route(
            ($locale === 'tr' ? 'tr.' : '') . $name,
            $parameters
        );
    }
}
