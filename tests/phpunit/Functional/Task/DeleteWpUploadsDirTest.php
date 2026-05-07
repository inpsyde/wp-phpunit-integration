<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\DeleteWpUploadsDir;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

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
