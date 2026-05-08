<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Task;

use Syde\WpPhpUnitIntegration\Task\EnableWpDebug;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;

final class EnableWpDebugTest extends UnitTestCase
{
    public function testSetsDebugConstantsViaWpCli(): void
    {
        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->exactly(4))
            ->method('run')
            ->with(
                $this->callback(function (array $args): bool {
                    static $calls = 1;

                    $expects = match ($calls) {
                        1 => [
                            'config',
                            'set',
                            'WP_DEBUG',
                            'true',
                        ],
                        2 => [
                            'config',
                            'set',
                            'WP_DEBUG_LOG',
                            'true',
                        ],
                        3 => [
                            'config',
                            'set',
                            'WP_DEBUG_DISPLAY',
                            'true',
                        ],
                        4 => [
                            'config',
                            'set',
                            'SAVEQUERIES',
                            'true',
                        ],
                        default => $this->fail(sprintf('Unexpected call number %d.', $calls)),
                    };
                    $this->assertSame($expects, $args);
                    ++$calls;

                    return true;
                }),
            );

        (new EnableWpDebug($wpCli))->execute();
    }
}
