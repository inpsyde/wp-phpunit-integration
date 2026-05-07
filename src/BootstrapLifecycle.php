<?php

declare(strict_types=1);

namespace Syde\PocWpLiteIntegrationTestHelper;

class BootstrapLifecycle
{
    public function __construct(
        private readonly ?\Closure $setup = null,
        private readonly ?\Closure $load = null,
        private readonly ?\Closure $cleanup = null,
    ) {
    }

    public function setup(): void
    {
        ($this->setup ?? static function (): void {
            WpTestEnv::setup();
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
