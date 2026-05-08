<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Task\Bundle;

use Syde\WpPhpUnitIntegration\Task\Task;

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
