<?php

if (! function_exists('taka')) {
    /**
     * Format an amount as Bangladeshi Taka using lakh-style grouping,
     * e.g. 248500 => "৳2,48,500". Matches the reference design.
     */
    function taka($amount, bool $symbol = true): string
    {
        $amount = (float) $amount;
        $negative = $amount < 0;
        $amount = abs($amount);

        // Split whole / decimal (drop .00, keep real paisa if present).
        $whole = (int) floor($amount);
        $fraction = round($amount - $whole, 2);

        $digits = (string) $whole;
        if (strlen($digits) > 3) {
            $last3 = substr($digits, -3);
            $rest = substr($digits, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $formatted = $rest.','.$last3;
        } else {
            $formatted = $digits;
        }

        if ($fraction > 0) {
            $formatted .= '.'.str_pad((string) round($fraction * 100), 2, '0', STR_PAD_LEFT);
        }

        return ($negative ? '-' : '').($symbol ? '৳' : '').$formatted;
    }
}

if (! function_exists('percent_change')) {
    /**
     * Percentage change from $previous to $current, rounded to 1 decimal.
     * Returns null when there is no meaningful baseline.
     */
    function percent_change($current, $previous): ?float
    {
        $current = (float) $current;
        $previous = (float) $previous;

        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
