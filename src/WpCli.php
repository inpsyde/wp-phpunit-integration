<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

use Exception;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Symfony\Component\Process\Process;

readonly class WpCli
{
    public function __construct(
        private SymfonyProcessFactory $symfonyProcessFactory,
        private LocalDependencyPath $wordPressPath,
        private LocalDependencyPath $wpCliPath,
    ) {
    }

    /**
     * @param string[] $args
     */
    public function run(array $args): string
    {
        $extendedArgs = [
            $this->wpCliPath->path(),
            ...$args,
            '--skip-plugins',
            '--skip-themes',
            '--path=' . $this->wordPressPath->path(),
        ];
        $output = '';

        $this->symfonyProcessFactory->create($extendedArgs)->run(
            static function (string $type, string $data) use (&$output): void {
                if ($type === Process::ERR) {
                    // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
                    throw new Exception($data);
                }

                $output .= $data;
            }
        );

        return $output;
    }
}
