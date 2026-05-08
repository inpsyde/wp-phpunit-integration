<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration\Tests\Unit\Package;

use Syde\WpPhpUnitIntegration\Package\PackageComposerJsonReader;
use Syde\WpPhpUnitIntegration\Package\PackageType;
use Syde\WpPhpUnitIntegration\Package\PackageTypeDetector;
use Syde\WpPhpUnitIntegration\Tests\UnitTestCase;

final class PackageTypeDetectorTest extends UnitTestCase
{
    public function testReturnsOtherForEmptyComposerJson(): void
    {
        $packageComposerJsonReader = $this->createMock(PackageComposerJsonReader::class);
        $packageComposerJsonReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('{}');

        $packageTypeDetector = new PackageTypeDetector($packageComposerJsonReader);

        $this->assertSame(
            PackageType::Other,
            $packageTypeDetector->determine(),
        );
    }

    public function testReturnsOtherForUnknownType(): void
    {
        $packageComposerJsonReader = $this->createMock(PackageComposerJsonReader::class);
        $packageComposerJsonReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('{"type":"module"}');

        $packageTypeDetector = new PackageTypeDetector($packageComposerJsonReader);

        $this->assertSame(
            PackageType::Other,
            $packageTypeDetector->determine(),
        );
    }

    public function testReturnsPluginForWordPressPluginType(): void
    {
        $packageComposerJsonReader = $this->createMock(PackageComposerJsonReader::class);
        $packageComposerJsonReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('{"type":"wordpress-plugin"}');

        $packageTypeDetector = new PackageTypeDetector($packageComposerJsonReader);

        $this->assertSame(
            PackageType::Plugin,
            $packageTypeDetector->determine(),
        );
    }

    public function testReturnsThemeForWordPressThemeType(): void
    {
        $packageComposerJsonReader = $this->createMock(PackageComposerJsonReader::class);
        $packageComposerJsonReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('{"type":"wordpress-theme"}');

        $packageTypeDetector = new PackageTypeDetector($packageComposerJsonReader);

        $this->assertSame(
            PackageType::Theme,
            $packageTypeDetector->determine(),
        );
    }

    public function testReturnsLibraryForLibraryType(): void
    {
        $packageComposerJsonReader = $this->createMock(PackageComposerJsonReader::class);
        $packageComposerJsonReader
            ->expects($this->once())
            ->method('read')
            ->willReturn('{"type":"library"}');

        $packageTypeDetector = new PackageTypeDetector($packageComposerJsonReader);

        $this->assertSame(
            PackageType::Library,
            $packageTypeDetector->determine(),
        );
    }
}
