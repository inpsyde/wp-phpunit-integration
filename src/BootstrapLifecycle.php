<?php

declare(strict_types=1);

namespace Syde\WpPhpUnitIntegration;

readonly class BootstrapLifecycle
{
    public function __construct(
        private ?\Closure $setup = null,
        private ?\Closure $load = null,
        private ?\Closure $cleanup = null,
        private ?\Closure $prepare = null,
    ) {
    }

    public function setup(): void
    {
        ($this->setup ?? static function (): void {
            WpTestEnv::setup();
        })();
    }

    public function prepare(): void
    {
        ($this->prepare ?? static function (): void {
            WpTestEnv::prepare();
        })();
    }

    public function load(): void
    {
        ($this->load ?? static function (): void {
            WpTestEnv::load();
        })();
    }

    public function cleanup(): void
    {
        ($this->cleanup ?? static function (): void {
            WpTestEnv::cleanup();
        })();
    }
}
