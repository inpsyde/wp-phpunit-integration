<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper\Tests\Unit;

use Syde\PocWpLiteIntegrationTestHelper\SymfonyProcessFactory;
use Syde\PocWpLiteIntegrationTestHelper\Tests\UnitTestCase;
use Syde\PocWpLiteIntegrationTestHelper\WpCli;
use Symfony\Component\Process\Process;

final class WpCliTest extends UnitTestCase
{
    public function testRunsWpCliCommandViaSymfonyProcess(): void
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('run');

        $symfonyProcessFactory = $this->createMock(SymfonyProcessFactory::class);
        $symfonyProcessFactory
            ->expects($this->once())
            ->method('create')
            ->with([
                '/vendor/bin/wp',
                'core',
                'version',
                '--skip-plugins',
                '--skip-themes',
                '--path=/vendor/wordpress/wordpress',
            ])
            ->willReturn($process);

        (new WpCli(
            $symfonyProcessFactory,
            $this->localDependencyPath('/vendor/wordpress/wordpress'),
            $this->localDependencyPath('/vendor/bin/wp'),
        ))->run([
            'core',
            'version',
        ]);
    }
}
