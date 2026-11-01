<?php

namespace App\Domain\Experience\Support;

use NumberFormatter;

/** Shared display formats for experience cards and pages (locale-aware). */
final class ExperienceFormatter
{
    /** "2 hours" — short activities keep their hours. */
    public function hours(float $hours): string
    {
        return trans_choice('ui.units.hours', (int) ceil($hours), [
            'count' => rtrim(rtrim(number_format($hours, 1), '0'), '.'),
        ]);
    }

    /** "Half day" / "1 day" / "5 days" like the design. */
    public function duration(float $hours): string
    {
        return match (true) {
            $hours < 3 => $this->hours($hours),
            $hours <= 5 => __('experiences.duration.half_day'),
            $hours <= 12 => trans_choice('experiences.duration.days', 1, ['count' => 1]),
            default => trans_choice('experiences.duration.days', (int) ceil($hours / 24), ['count' => (int) ceil($hours / 24)]),
        };
    }

    /** "€320" / "MAD 3,200" — no decimals for whole amounts. */
    public function money(float $amount, string $currency, ?string $locale = null): string
    {
        $formatter = new NumberFormatter($locale ?? app()->getLocale(), NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, fmod($amount, 1.0) === 0.0 ? 0 : 2);

        return (string) $formatter->formatCurrency($amount, $currency);
    }
}
