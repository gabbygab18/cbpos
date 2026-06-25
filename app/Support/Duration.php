<?php

namespace App\Support;

class Duration
{
    /**
     * Format total minutes as "Xh Ym" for tables, or decimal hours like "7.8h" for summary cards.
     */
    public static function short(?int $minutes): string
    {
        if ($minutes === null) {
            return '—';
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours > 0 && $remaining > 0) {
            return "{$hours}h {$remaining}m";
        }
        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$remaining}m";
    }

    public static function decimalHours(?int $minutes): string
    {
        if (!$minutes) {
            return '0.0h';
        }

        return number_format($minutes / 60, 1) . 'h';
    }
}
