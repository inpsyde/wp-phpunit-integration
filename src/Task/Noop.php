<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task;

class Noop implements Task
{
    public function execute(): void
    {
        // Do nothing.
    }
}
