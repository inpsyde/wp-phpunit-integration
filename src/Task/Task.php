<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task;

interface Task
{
    public function execute(): void;
}
