<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Functional\Path;

use Composer\InstalledVersions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Syde\WpPhpUnitIntegration\Path\ComposerPackagePath;
use Syde\WpPhpUnitIntegration\Tests\FunctionalTestCase;
use Throwable;

#[RunTestsInSeparateProcesses]
final class ComposerPackagePathTest extends FunctionalTestCase
{
    #[DataProvider('packagePathProvider')]
    public function testItReturnsInstalledPath(string $installPath, string $expectedPath): void
    {
        $workspace = $this->workspace . '/acme';
        $this->filesystem->mkdir($workspace . $expectedPath);

        // @phpstan-ignore argument.type
        InstalledVersions::reload([
            'versions' => [
                'some/package' => [
                    'install_path' => $workspace . $installPath,
                ],
            ],
        ]);

        $this->assertSame(
            $workspace . $expectedPath,
            (new ComposerPackagePath())->installedPath('some/package'),
        );
    }

    public static function packagePathProvider(): \Generator
    {
        yield [
            '/vendor/some/package',
            '/vendor/some/package',
        ];

        yield [
            '/vendor/../vendor/some/package',
            '/vendor/some/package',
        ];
    }

    public function testThrowsExceptionWhenPackageIsNotInstalled(): void
    {
        $this->expectException(Throwable::class);

        $this->filesystem->mkdir($this->workspace . '/acme');

        // @phpstan-ignore argument.type
        InstalledVersions::reload([
            'versions' => [],
        ]);

        (new ComposerPackagePath())->installedPath('some/package');
    }
}
