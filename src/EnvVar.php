<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration;

class EnvVar
{
    public function get(string $key): ?string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return null;
        }

        return $value;
    }

    public function set(string $key, string $value): bool
    {
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv
        return putenv($key . '=' . $value);
    }
}
