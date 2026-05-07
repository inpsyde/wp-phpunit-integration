<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Functional\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\DeleteWpUploadsDir;
use Syde\PocWpLiteIntegrationTestHelper\Tests\FunctionalTestCase;

final class DeleteWpUploadsDirTest extends FunctionalTestCase
{
    public function testRecursivelyRemovesUploadsDirFromWpContent(): void
    {
        $this->filesystem->appendToFile(
            $this->workspace . '/wordpress/wp-content/uploads/2026/03/sample.jpg',
            '',
        );

        (new DeleteWpUploadsDir(
            $this->localDependencyPath($this->workspace . '/wordpress'),
        ))->execute();

        $this->assertDirectoryDoesNotExist($this->workspace . '/wordpress/wp-content/uploads');
    }
}
