<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\DeleteDbDropIn;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;

final class DeleteDbDropInTest extends FunctionalTestCase
{
    public function testRemovesDbDropInFileFromWpContent(): void
    {
        $this->filesystem->dumpFile($this->workspace . '/wordpress/wp-content/db.php', '');

        (new DeleteDbDropIn(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        ))->execute();

        $this->assertFileDoesNotExist($this->workspace . '/wordpress/wp-content/db.php');
    }
}
