<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Task;

use Syde\WpPhpUnitIntegration\Task\DeleteWpUploadsDir;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;

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
