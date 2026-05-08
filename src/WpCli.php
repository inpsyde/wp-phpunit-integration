<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;

class WpCli
{
    public function __construct(
        private readonly SymfonyProcessFactory $symfonyProcessFactory,
        private readonly LocalDependencyPath $wordPressPath,
        private readonly LocalDependencyPath $wpCliPath,
    ) {
    }

    /**
     * @param string[] $args
     */
    public function run(array $args): void
    {
        $extendedArgs = [
            $this->wpCliPath->path(),
            ...$args,
            '--skip-plugins',
            '--skip-themes',
            '--path=' . $this->wordPressPath->path(),
        ];

        // TODO: We might need to handle displaying if anything failed and surfacing the errors even from child PHP processes.
        $this->symfonyProcessFactory->create($extendedArgs)->run();
    }
}
