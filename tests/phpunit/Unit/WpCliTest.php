<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit;

use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Path\WpCliPath;
use Syde\WpPhpUnitIntegration\SymfonyProcessFactory;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;
use Syde\WpPhpUnitIntegration\WpCli;
use Symfony\Component\Process\Process;
use Throwable;

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

        $this->runWpCli($symfonyProcessFactory);
    }

    public function testThrowsExceptionWhenWpCliWritesToStderr(): void
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessageMatches('/Something went wrong/');

        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(
                static function (callable $callback): void {
                    $callback(Process::ERR, 'Something went wrong');
                },
            );

        $symfonyProcessFactory = $this->createMock(SymfonyProcessFactory::class);
        $symfonyProcessFactory->method('create')->willReturn($process);

        $this->runWpCli($symfonyProcessFactory);
    }

    public function testDoesNotThrowWhenWpCliWritesToStdout(): void
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(
                static function (callable $callback): int {
                    $callback(Process::OUT, 'Some regular output');

                    return 0;
                },
            );

        $symfonyProcessFactory = $this->createMock(SymfonyProcessFactory::class);
        $symfonyProcessFactory->method('create')->willReturn($process);

        $this->runWpCli($symfonyProcessFactory);
    }

    private function runWpCli(SymfonyProcessFactory $symfonyProcessFactory): void
    {
        $wordPressPath = $this->createStub(WordPressPath::class);
        $wordPressPath
            ->method('path')
            ->willReturn('/vendor/wordpress/wordpress');

        $wpCliPath = $this->createStub(WpCliPath::class);
        $wpCliPath
            ->method('path')
            ->willReturn('/vendor/bin/wp');

        (new WpCli(
            $symfonyProcessFactory,
            $wordPressPath,
            $wpCliPath,
        ))->run([
            'core',
            'version',
        ]);
    }
}
