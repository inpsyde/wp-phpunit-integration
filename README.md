# WP PHPUnit Integration

The WP PHPUnit Integration package helps you write PHPUnit tests for your WordPress packages where you prefer to have the actual implementation of WordPress functions and classes available, rather than mocking them with a library such as [Brain Monkey](https://github.com/Brain-WP/BrainMonkey).

Depending on your intent for your tests, it is ideal for functional or integration tests, where you can also take advantage of the database.

Once you require and configure this package, it will set up a fully functional WordPress environment before the tests are run, install WordPress, symlink and activate your plugin or theme, and reset everything once the tests have finished.

All you need is PHP. You don't need a test database service running such as MySQL or MariaDB, or to configure and start Docker containers, as this package also takes advantage of the [SQLite Database Integration](https://github.com/WordPress/sqlite-database-integration).

## Setup and usage

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

No fixed location is required, as the WP PHPUnit Integration will automatically detect the location.

### Setup

You can use WP PHPUnit Integration for all your PHPUnit tests.

However, if you already have unit tests where you use Brain Monkey, we recommend creating separate PHPUnit configuration and bootstrap files for your integration tests, as these files can quickly grow complex over time and become messy.

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

It's also recommended to run the integration tests as part of the CI/CD pipeline.

To do that, take advantage of the [Reusable Workflows](https://github.com/inpsyde/reusable-workflows/blob/main/docs/php.md#unit-tests-php) and create `/.github/workflows/quality-assurance-php-integration-testing.yml` file with the following content:

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
        setup: function () {
            // Run some setup tasks before the default ones.
            
            WpTestEnv::setup();
        },
        load: function () use ($packagePath) {
            // Omit the default WpTestEnv::load() to use your own custom logic.
            
            include "{$packagePath}/vendor/roots/wordpress/wp-load.php";
        },
        cleanup: function () {
            WpTestEnv::cleanup();

            // Run some extra cleanup tasks after the default ones.
        },
    ),
);
```

### Helpers

For convenience, `WpTestEnv` exposes some helper methods to cover most customization requirements.

#### WP-CLI commands

`runWpCliCommand` triggers a call to WP-CLI without returning any output from it:

```php
WpTestEnv::runWpCliCommand([
    'plugin',
    'activate',
    'woocommerce',
]);
```

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

To be able to quickly test against multiple WordPress versions, when the `WP_PHPUNIT_INTEGRATION_WP_CORE_UPGRADE_VERSION` environment variable is set, part of the setup process the installed WordPress is updated to the specific version.

```shell
WP_PHPUNIT_INTEGRATION_WP_CORE_UPGRADE_VERSION=6.8 composer run tests:integration:no-cov
```

This environment variable is optional and doesn't have to be passed. Without it, the already installed WordPress is used, for example, the one installed via Composer.

You can take advantage of this in GitHub Actions and modify your existing workflow as follows:

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
                [{"name":"WP_PHPUNIT_INTEGRATION_WP_CORE_UPGRADE_VERSION", "value":"${{ matrix.wp }}"}]
        with:
            PHPUNIT_ARGS: '-c phpunit-integration.xml.dist --coverage-text'
            PHP_VERSION: '8.2'
```

## Copyright and License

This package is [open-source software](https://opensource.org/license/MIT) distributed under
the terms of the MIT License. For the full license, see [LICENSE](./LICENSE).

## Contributing

All feedback, bug reports and pull requests are welcome.
