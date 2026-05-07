<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task;

class Noop implements Task
{
    public function execute(): void
    {
        // Do nothing.
    }
}
