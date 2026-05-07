<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit\Task;

use Syde\PocWpLiteIntegrationTestHelper\Task\EnableWpDebug;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;

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
                    };
                    $this->assertSame($expects, $args);
                    ++$calls;

                    return true;
                }),
            );

        (new EnableWpDebug($wpCli))->execute();
    }
}
