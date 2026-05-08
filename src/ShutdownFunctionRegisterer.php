<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

class ShutdownFunctionRegisterer
{
    public function register(callable $callback): void
    {
        register_shutdown_function($callback);
    }
}
