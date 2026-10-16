<?php

namespace App\Enums\Concerns;

/**
 * Turns a backed enum into the shapes the admin forms and API resources need.
 *
 * The admin selects are built by `html()->select($name, $options)`, which wants
 * a plain `value => label` array — this is what lets an enum replace the
 * hand-maintained arrays that used to live in app/Helpers/Functions.php without
 * touching a single Blade file.
 *
 * The using enum must declare `label(): string`.
 */
trait HasOptions
{
    /**
     * Every case as `value => translated label`.
     *
     * @return array<string|int, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * Options prefixed with a placeholder row, for filter dropdowns where
     * "no choice" is a valid choice.
     *
     * @return array<string|int, string>
     */
    public static function optionsWithPlaceholder(?string $label = null, string|int $value = ''): array
    {
        return [$value => $label ?? __('All')] + self::options();
    }

    /**
     * Options limited to the given cases, in the order they were passed —
     * a dropdown's order is part of its behaviour, so the caller decides it.
     *
     * @param  array<int, self>  $cases
     * @return array<string|int, string>
     */
    public static function optionsOnly(array $cases): array
    {
        $options = [];

        foreach ($cases as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * Options with the given cases removed, in enum declaration order.
     *
     * @param  array<int, self>  $cases
     * @return array<string|int, string>
     */
    public static function optionsExcept(array $cases): array
    {
        return array_diff_key(
            self::options(),
            array_flip(array_map(fn (self $case) => $case->value, $cases)),
        );
    }

    /**
     * The raw stored values, e.g. for a validation `in:` rule.
     *
     * @return array<int, string|int>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * The label for a stored value, falling back to the value itself.
     *
     * Admin tables render legacy rows whose column may hold a value no longer
     * in the enum, so this must never return null or throw.
     */
    public static function labelFor(string|int|null $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return self::tryFrom($value)?->label() ?? (string) $value;
    }
}
