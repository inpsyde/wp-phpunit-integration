# WP PHPUnit Integration

This package helps you write PHPUnit tests for your WordPress packages where you prefer to have the actual implementation of WordPress functions and classes available, rather than mocking them with a library such as [Brain Monkey](https://github.com/Brain-WP/BrainMonkey).

Depending on your intent, it is ideal for functional or integration tests, where you can also take advantage of the database.

Once you require and configure WP PHPUnit Integration, it will set up a fully functional WordPress environment before the tests are run (installing WordPress, symlinking and activating your plugin or theme, etc.), and reset everything once the tests have finished.

All you need is PHP. You don't need a database service running, such as MySQL or MariaDB, for your tests, or to configure and start Docker containers, as this package also takes advantage of the [SQLite Database Integration](https://github.com/WordPress/sqlite-database-integration).

## Setup and usage

> [!WARNING]
> This package is currently in a pre-stable release. Breaking changes may occur between versions until a stable `1.0` release is published.

### Installation

You can require this package with Composer:

```shell
composer require --dev syde/wp-phpunit-integration
```

You will also need WordPress, though it is not a hard requirement to install it via Composer. In most cases, it's the most straightforward approach:

```shell
composer require --dev roots/wordpress
```

You can also specify where you want to install WordPress by adding the following to your `composer.json`:

```json
{
    "extra": {
        "wordpress-install-dir": "vendor/wordpress/wordpress",
        "installer-paths": {
            "vendor/wordpress/wordpress/wp-content/plugins/{$name}": [
                "type:wordpress-plugin"
            ]
        }
    }
}
```

No fixed location is required, as WP PHPUnit Integration will automatically try to detect the location.

### Setup

You can use WP PHPUnit Integration for all your PHPUnit tests.

However, if you already have unit tests where you use, for example, Brain Monkey, we recommend creating separate PHPUnit configuration and bootstrap files for your integration tests, as these files can quickly grow complex over time and become messy.

The following steps describe this scenario.

#### Bootstrap file

Create a `/tests/phpunit/bootstrap-integration.php` file with the following content:

```php
<?php

declare(strict_types=1);

use Syde\WpPhpUnitIntegration\Bootstrap;

$packagePath = dirname(__DIR__, 2);
$vendorPath = "{$packagePath}/vendor";

if (!realpath($vendorPath)) {
    die('Please install via Composer before running tests.');
}

require_once "{$vendorPath}/autoload.php";

Bootstrap::init($packagePath);

unset($packagePath, $vendorPath);
```

#### PHPUnit configuration

Create a `/phpunit-integration.xml.dist` file with the following content:

```xml
<phpunit
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
        bootstrap="tests/phpunit/bootstrap-integration.php">
    <testsuites>
        <testsuite name="integration">
            <directory>tests/phpunit/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

#### Composer scripts

Then, in your `composer.json`, add dedicated integration test scripts alongside your existing ones:

```json
{
    "scripts": {
        "tests": "@php ./vendor/bin/phpunit --coverage-text",
        "tests:no-cov": "@php ./vendor/bin/phpunit --no-coverage",
        "tests:integration": "@php ./vendor/bin/phpunit -c phpunit-integration.xml.dist --coverage-text",
        "tests:integration:no-cov": "@php ./vendor/bin/phpunit -c phpunit-integration.xml.dist --no-coverage"
    }
}
```

If you have followed these steps, you should now be able to run your integration tests with:

```shell
composer run tests:integration:no-cov
```

For most cases, this is all you have to do.

#### GitHub Actions

It's also recommended to run the integration tests as part of the CI/CD pipeline. When using GitHub Actions, you can do this by taking advantage of the [Reusable Workflows](https://github.com/inpsyde/reusable-workflows/blob/main/docs/php.md#unit-tests-php). 

Create a `/.github/workflows/quality-assurance-php-integration-testing.yml` file with the following content:

```yaml
name: PHP Integration Testing
on:
    pull_request:
        paths:
            - '**.php'
            - 'composer.*'
            - 'phpunit.*'
jobs:
    tests-unit-php:
        uses: inpsyde/reusable-workflows/.github/workflows/tests-unit-php.yml@main
        secrets:
            COMPOSER_AUTH_JSON: '${{secrets.PACKAGIST_AUTH_JSON}}'
        with:
            PHPUNIT_ARGS: '-c phpunit-integration.xml.dist --coverage-text'
            PHP_VERSION: '8.2'
```

## Customization

### Lifecycle phases

WP PHPUnit Integration has three distinct phases that you can customize.

The `setup` and `cleanup` phases run only once, before and after all tests are executed. As their names suggest, they trigger steps such as creating the `wp-config.php`, setting up required constants, or undoing these actions.

The `load` phase is called before both the main test process and any child processes. When the test is run in isolation, `load` is called multiple times, either before the [test class](https://docs.phpunit.de/en/10.5/attributes.html#runtestsinseparateprocesses) or [test method](https://docs.phpunit.de/en/10.5/attributes.html#runinseparateprocess), depending on your PHPUnit [configuration](https://docs.phpunit.de/en/10.5/configuration.html#the-processisolation-attribute).

Each phase can be customized by running additional logic before or after the defaults, or by replacing it entirely.

```php
use Syde\WpPhpUnitIntegration\Bootstrap;
use Syde\WpPhpUnitIntegration\BootstrapLifecycle;
use Syde\WpPhpUnitIntegration\WpTestEnv;

Bootstrap::init(
    $packagePath,
    new BootstrapLifecycle(
        setup: function (): void {
            // Run some setup tasks before the default ones.
            
            WpTestEnv::setup();
        },
        load: function () use ($packagePath): void {
            // Omit the default WpTestEnv::load() to use your own custom logic.
            
            include "{$packagePath}/vendor/roots/wordpress/wp-load.php";
        },
        cleanup: function (): void {
            WpTestEnv::cleanup();

            // Run some extra cleanup tasks after the default ones.
        },
    ),
);
```

### Helpers

For convenience, `WpTestEnv` exposes some helper methods to cover most customization requirements.

#### WP-CLI commands

With `runWpCliCommand`, you can run arbitrary WP-CLI commands:

```php
WpTestEnv::runWpCliCommand([
    'plugin',
    'activate',
    'woocommerce',
]);
```

`runWpCliCommand` returns the `stdout` output, and if an error occurs, an exception is thrown with the error message returned by WP-CLI.

You can use any WP-CLI commands, but keep in mind that they are called with the `--skip-plugins,` `--skip-themes,` and an explicit `--path` option.

#### Early hooks

The other two static methods exposed are `addEarlyFilter` and `addEarlyAction`.

These can be called before WordPress is even loaded, and their signatures match those of the WordPress hooks:

```php
WpTestEnv::addEarlyFilter(
    'wonolog.buffer-handler',
    function (): bool {
        return false;
    },
);
```

### WordPress version

To quickly test against multiple WordPress versions, you can set the `WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION` (or simply `WP_CORE_VERSION`) environment variable. When provided, the setup process will automatically update WordPress to that specific version.

```shell
WP_PHPUNIT_INTEGRATION_WP_CORE_VERSION=6.8 composer run tests:integration:no-cov
```

This environment variable is optional. Without it, the already-installed WordPress version is used without attempting to update it using WP-CLI.

#### Multiple WordPress versions in GitHub Actions

You can take advantage of this environment variable in GitHub Actions to run tests against multiple WordPress versions.

To do this, amend the [previously mentioned workflow](#github-actions) as follows:

```yaml
jobs:
    tests-unit-php:
        uses: inpsyde/reusable-workflows/.github/workflows/tests-unit-php.yml@main
        strategy:
            fail-fast: false
            matrix:
                wp:
                    - '6.8'
                    - '6.9'
                    - '7.0'
        secrets:
            COMPOSER_AUTH_JSON: '${{secrets.PACKAGIST_AUTH_JSON}}'
            ENV_VARS: >-
                [{"name":"WP_CORE_VERSION", "value":"${{ matrix.wp }}"}]
        with:
            PHPUNIT_ARGS: '-c phpunit-integration.xml.dist --coverage-text'
            PHP_VERSION: '8.2'
```

## Copyright and License

This package is [open-source software](https://opensource.org/license/MIT) distributed under the terms of the MIT License. For the full license, see [LICENSE](./LICENSE).

## Contributing

All feedback, bug reports and pull requests are welcome.
