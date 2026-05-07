<?php

declare(strict_types=1);

namespace Syde\WpPhpunitIntegration\Tests\Unit\Task;

use Syde\WpPhpunitIntegration\Task\DefineRequiredWpConstants;
use Syde\WpPhpunitIntegration\Tests\UnitTestCase;
use Syde\WpPhpunitIntegration\WpCli;

final class DefineRequiredWpConstantsTest extends UnitTestCase
{
    public function testSetsRequiredWpConstantsViaWpCli(): void
    {
        $wpCli = $this->createMock(WpCli::class);
        $wpCli
            ->expects($this->exactly(3))
            ->method('run')
            ->with(
                $this->callback(function (array $args): bool {
                    static $calls = 1;

                    $expects = match ($calls) {
                        1 => [
                            'config',
                            'set',
                            'DISABLE_WP_CRON',
                            'true',
                            '--raw',
                        ],
                        2 => [
                            'config',
                            'set',
                            'WP_SQLITE_AST_DRIVER',
                            'true',
                            '--raw',
                        ],
                        3 => [
                            'config',
                            'set',
                            'SQLITE_MAIN_FILE',
                            'true',
                            '--raw',
                        ],
                        default => $this->fail(sprintf('Unexpected call number %d.', $calls)),
                    };
                    $this->assertSame($expects, $args);
                    ++$calls;

                    return true;
                }),
            );


        (new DefineRequiredWpConstants($wpCli))->execute();
    }
}
