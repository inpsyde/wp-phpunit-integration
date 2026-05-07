<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Task\Bundle;

use Syde\WpPhpunitIntegration\Task\Task;

abstract class TaskBundle implements Task
{
    /**
     * @var Task[]
     */
    private array $tasks;

    public function __construct(Task ...$tasks)
    {
        $this->tasks = $tasks;
    }

    public function execute(): void
    {
        foreach ($this->tasks as $task) {
            $task->execute();
        }
    }
}
