<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Functional\Task;

use Syde\WpPhpunitIntegration\Task\DeleteDbDropIn;
use Syde\WpPhpunitIntegration\Tests\FunctionalTestCase;

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
