<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit\Task\Bundle;

use Syde\PocWpLiteIntegrationTestHelper\Task\Bundle\TaskBundle;
use Syde\PocWpLiteIntegrationTestHelper\Task\Task;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;

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
