<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Task\Bundle;

use Syde\WpPhpUnitIntegration\Task\Bundle\TaskBundle;
use Syde\WpPhpUnitIntegration\Task\Task;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class TaskBundleTest extends UnitTestCase
{
    public function testAllTasksAreExecuted(): void
    {
        $taskA = $this->createMock(Task::class);
        $taskA->expects($this->once())->method('execute');

        $taskB = $this->createMock(Task::class);
        $taskB->expects($this->once())->method('execute');

        (new class ($taskA, $taskB) extends TaskBundle {
        })->execute();
    }
}
