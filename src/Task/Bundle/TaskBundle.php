<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Task\Bundle;

use Syde\PocWpLiteIntegrationTestHelper\Task\Task;

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
