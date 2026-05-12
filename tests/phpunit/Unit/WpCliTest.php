<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit;

use Syde\WpPhpUnitIntegration\SymfonyProcessFactory;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;
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
