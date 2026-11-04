<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/inc',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        phpunitNarrowAsserts: true,
        phpunitMockToStub: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true
    )
    ->withSkip([
        ClosureToArrowFunctionRector::class => [
            __DIR__ . '/inc/container.php',
        ],
    ]);
