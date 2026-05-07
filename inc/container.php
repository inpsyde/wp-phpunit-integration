<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Syde\WpPhpunitIntegration\BuiltInServer;
use Syde\WpPhpunitIntegration\EnvVar;
use Syde\WpPhpunitIntegration\Package\PackageComposerJsonReader;
use Syde\WpPhpunitIntegration\Package\PackageType;
use Syde\WpPhpunitIntegration\Package\PackageTypeDetector;
use Syde\WpPhpunitIntegration\Path\Finder\CachedLocalDependencyPathFinder;
use Syde\WpPhpunitIntegration\Path\Finder\CachedLocalDependencyPathFinderKey;
use Syde\WpPhpunitIntegration\Path\Finder\LocalDependencyPathFinder;
use Syde\WpPhpunitIntegration\Path\Finder\SqliteDatabaseIntegrationPluginFinder;
use Syde\WpPhpunitIntegration\Path\Finder\WordPressPathFinder;
use Syde\WpPhpunitIntegration\Path\Finder\WpCliPathFinder;
use Syde\WpPhpunitIntegration\Path\LocalDependencyPath;
use Syde\WpPhpunitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpunitIntegration\Path\PackageRootPath;
use Syde\WpPhpunitIntegration\Path\SqliteDatabaseIntegrationPluginPath;
use Syde\WpPhpunitIntegration\Path\WordPressPath;
use Syde\WpPhpunitIntegration\Path\WpCliPath;
use Syde\WpPhpunitIntegration\PhpProcessIdProvider;
use Syde\WpPhpunitIntegration\ShutdownFunctionRegisterer;
use Syde\WpPhpunitIntegration\SymfonyProcessFactory;
use Syde\WpPhpunitIntegration\Task\ActivateTestedPlugin;
use Syde\WpPhpunitIntegration\Task\ActivateTestedTheme;
use Syde\WpPhpunitIntegration\Task\Bundle\Cleanup;
use Syde\WpPhpunitIntegration\Task\Bundle\Load;
use Syde\WpPhpunitIntegration\Task\Bundle\Setup;
use Syde\WpPhpunitIntegration\Task\CreateEmptyWpThemesDir;
use Syde\WpPhpunitIntegration\Task\CreateSqliteDbDropIn;
use Syde\WpPhpunitIntegration\Task\CreateWpConfig;
use Syde\WpPhpunitIntegration\Task\DefineRequiredWpConstants;
use Syde\WpPhpunitIntegration\Task\DeleteDbDropIn;
use Syde\WpPhpunitIntegration\Task\DeleteSqliteDatabaseDir;
use Syde\WpPhpunitIntegration\Task\DeleteWpConfig;
use Syde\WpPhpunitIntegration\Task\DeleteWpDebugLog;
use Syde\WpPhpunitIntegration\Task\DeleteWpUploadsDir;
use Syde\WpPhpunitIntegration\Task\EnableWpDebug;
use Syde\WpPhpunitIntegration\Task\IncludeWp;
use Syde\WpPhpunitIntegration\Task\InstallMultisiteWp;
use Syde\WpPhpunitIntegration\Task\Noop;
use Syde\WpPhpunitIntegration\Task\RefreshSqliteDb;
use Syde\WpPhpunitIntegration\Task\SymlinkTestedPlugin;
use Syde\WpPhpunitIntegration\Task\SymlinkTestedTheme;
use Syde\WpPhpunitIntegration\Task\UnlinkTestedPlugin;
use Syde\WpPhpunitIntegration\Task\UnlinkTestedTheme;
use Syde\WpPhpunitIntegration\TestRunnerProcessTracker;
use Syde\WpPhpunitIntegration\WpCli;

return static function (string $packageRootPath): array {
    return [
        ...[
            PackageComposerJsonReader::class => static fn (ContainerInterface $container): PackageComposerJsonReader => new PackageComposerJsonReader(
                $container->get(PackageRootPath::class),
            ),
            PackageTypeDetector::class => static fn (ContainerInterface $container): PackageTypeDetector => new PackageTypeDetector(
                $container->get(PackageComposerJsonReader::class),
            ),
        ],
        ...[
            CachedLocalDependencyPathFinderKey::class => static fn (): CachedLocalDependencyPathFinderKey => new CachedLocalDependencyPathFinderKey(),
            SqliteDatabaseIntegrationPluginFinder::class => static fn (ContainerInterface $container): LocalDependencyPathFinder => new CachedLocalDependencyPathFinder(
                new SqliteDatabaseIntegrationPluginFinder(
                    $container->get(PackageRootPath::class),
                ),
                $container->get(CachedLocalDependencyPathFinderKey::class),
                $container->get(EnvVar::class),
            ),
            WordPressPathFinder::class => static fn (ContainerInterface $container): LocalDependencyPathFinder => new CachedLocalDependencyPathFinder(
                new WordPressPathFinder(
                    $container->get(PackageRootPath::class),
                ),
                $container->get(CachedLocalDependencyPathFinderKey::class),
                $container->get(EnvVar::class),
            ),
            WpCliPathFinder::class => static fn (ContainerInterface $container): LocalDependencyPathFinder => new CachedLocalDependencyPathFinder(
                new WpCliPathFinder(
                    $container->get(PackageRootPath::class),
                ),
                $container->get(CachedLocalDependencyPathFinderKey::class),
                $container->get(EnvVar::class),
            ),
        ],
        ...[
            LocalDependencyPathNormalizer::class => static fn (): LocalDependencyPathNormalizer => new LocalDependencyPathNormalizer(),
            PackageRootPath::class => static fn (ContainerInterface $container): LocalDependencyPath => new PackageRootPath(
                $packageRootPath,
                $container->get(LocalDependencyPathNormalizer::class),
            ),
            SqliteDatabaseIntegrationPluginPath::class => static fn (ContainerInterface $container): LocalDependencyPath => new SqliteDatabaseIntegrationPluginPath(
                $container->get(SqliteDatabaseIntegrationPluginFinder::class),
                $container->get(LocalDependencyPathNormalizer::class),
            ),
            WordPressPath::class => static fn (ContainerInterface $container): LocalDependencyPath => new WordPressPath(
                $container->get(WordPressPathFinder::class),
                $container->get(LocalDependencyPathNormalizer::class),
            ),
            WpCliPath::class => static fn (ContainerInterface $container): LocalDependencyPath => new WpCliPath(
                $container->get(WpCliPathFinder::class),
                $container->get(LocalDependencyPathNormalizer::class),
            ),
        ],
        ...[
            Setup::class => static function (ContainerInterface $container): Setup {
                $contextual = (match ($container->get(PackageTypeDetector::class)->determine()) {
                    PackageType::Plugin => [
                        $container->get(SymlinkTestedPlugin::class),
                        $container->get(ActivateTestedPlugin::class),
                    ],
                    PackageType::Theme => [
                        $container->get(SymlinkTestedTheme::class),
                        $container->get(ActivateTestedTheme::class),
                    ],
                    default => []
                });

                return new Setup(
                    $container->get(CreateSqliteDbDropIn::class),
                    $container->get(CreateWpConfig::class),
                    $container->get(DefineRequiredWpConstants::class),
                    $container->get(EnableWpDebug::class),
                    $container->get(InstallMultisiteWp::class),
                    $container->get(CreateEmptyWpThemesDir::class),
                    ...$contextual,
                );
            },
            Cleanup::class => static function (ContainerInterface $container): Cleanup {
                $contextual = (match ($container->get(PackageTypeDetector::class)->determine()) {
                    PackageType::Plugin => [
                        $container->get(UnlinkTestedPlugin::class),
                    ],
                    PackageType::Theme => [
                        $container->get(UnlinkTestedTheme::class),
                    ],
                    default => []
                });

                return new Cleanup(
                    $container->get(DeleteWpConfig::class),
                    $container->get(DeleteDbDropIn::class),
                    $container->get(DeleteSqliteDatabaseDir::class),
                    $container->get(DeleteWpUploadsDir::class),
                    $container->get(DeleteWpDebugLog::class),
                    ...$contextual,
                );
            },
            Load::class => static fn (ContainerInterface $container): Load => new Load(
                $container->get(DeleteWpUploadsDir::class),
                $container->get(RefreshSqliteDb::class),
                $container->get(IncludeWp::class),
            ),
        ],
        ...[
            ActivateTestedPlugin::class => static fn (ContainerInterface $container): ActivateTestedPlugin => new ActivateTestedPlugin(
                $container->get(PackageRootPath::class),
                $container->get(WpCli::class),
            ),
            ActivateTestedTheme::class => static fn (ContainerInterface $container): ActivateTestedTheme => new ActivateTestedTheme(
                $container->get(PackageRootPath::class),
                $container->get(WpCli::class),
            ),
            CreateEmptyWpThemesDir::class => static fn (ContainerInterface $container): CreateEmptyWpThemesDir => new CreateEmptyWpThemesDir(
                $container->get(WordPressPath::class),
            ),
            CreateSqliteDbDropIn::class => static fn (ContainerInterface $container): CreateSqliteDbDropIn => new CreateSqliteDbDropIn(
                $container->get(WordPressPath::class),
                $container->get(SqliteDatabaseIntegrationPluginPath::class),
            ),
            CreateWpConfig::class => static fn (ContainerInterface $container): CreateWpConfig => new CreateWpConfig(
                $container->get(WpCli::class),
            ),
            DefineRequiredWpConstants::class => static fn (ContainerInterface $container): DefineRequiredWpConstants => new DefineRequiredWpConstants(
                $container->get(WpCli::class),
            ),
            DeleteDbDropIn::class => static fn (ContainerInterface $container): DeleteDbDropIn => new DeleteDbDropIn(
                $container->get(WordPressPath::class),
            ),
            DeleteSqliteDatabaseDir::class => static fn (ContainerInterface $container): DeleteSqliteDatabaseDir => new DeleteSqliteDatabaseDir(
                $container->get(WordPressPath::class),
            ),
            DeleteWpConfig::class => static fn (ContainerInterface $container): DeleteWpConfig => new DeleteWpConfig(
                $container->get(WordPressPath::class),
            ),
            DeleteWpDebugLog::class => static fn (ContainerInterface $container): DeleteWpDebugLog => new DeleteWpDebugLog(
                $container->get(WordPressPath::class),
            ),
            DeleteWpUploadsDir::class => static fn (ContainerInterface $container): DeleteWpUploadsDir => new DeleteWpUploadsDir(
                $container->get(WordPressPath::class),
            ),
            EnableWpDebug::class => static fn (ContainerInterface $container): EnableWpDebug => new EnableWpDebug(
                $container->get(WpCli::class),
            ),
            IncludeWp::class => static fn (ContainerInterface $container): IncludeWp => new IncludeWp(
                $container->get(WordPressPath::class),
            ),
            InstallMultisiteWp::class => static fn (ContainerInterface $container): InstallMultisiteWp => new InstallMultisiteWp(
                $container->get(WpCli::class),
            ),
            Noop::class => static fn (): Noop => new Noop(),
            RefreshSqliteDb::class => static fn (ContainerInterface $container): RefreshSqliteDb => new RefreshSqliteDb(
                $container->get(WordPressPath::class),
                $container->get(PhpProcessIdProvider::class),
                $container->get(WpCli::class),
            ),
            SymlinkTestedPlugin::class => static fn (ContainerInterface $container): SymlinkTestedPlugin => new SymlinkTestedPlugin(
                $container->get(PackageRootPath::class),
                $container->get(WordPressPath::class),
            ),
            SymlinkTestedTheme::class => static fn (ContainerInterface $container): SymlinkTestedTheme => new SymlinkTestedTheme(
                $container->get(PackageRootPath::class),
                $container->get(WordPressPath::class),
            ),
            UnlinkTestedPlugin::class => static fn (ContainerInterface $container): UnlinkTestedPlugin => new UnlinkTestedPlugin(
                $container->get(PackageRootPath::class),
                $container->get(WordPressPath::class),
            ),
            UnlinkTestedTheme::class => static fn (ContainerInterface $container): UnlinkTestedTheme => new UnlinkTestedTheme(
                $container->get(PackageRootPath::class),
                $container->get(WordPressPath::class),
            ),
        ],
        ...[
            BuiltInServer::class => static fn (ContainerInterface $container): BuiltInServer => new BuiltInServer(
                $container->get(SymfonyProcessFactory::class),
                $container->get(WordPressPath::class),
            ),
            EnvVar::class => static fn (): EnvVar => new EnvVar(),
            PhpProcessIdProvider::class => static fn (): PhpProcessIdProvider => new PhpProcessIdProvider(),
            ShutdownFunctionRegisterer::class => static fn (): ShutdownFunctionRegisterer => new ShutdownFunctionRegisterer(),
            SymfonyProcessFactory::class => static fn (): SymfonyProcessFactory => new SymfonyProcessFactory(),
            TestRunnerProcessTracker::class => static fn (ContainerInterface $container): TestRunnerProcessTracker => new TestRunnerProcessTracker(
                $container->get(PhpProcessIdProvider::class),
                $container->get(EnvVar::class),
            ),
            WpCli::class => static fn (ContainerInterface $container): WpCli => new WpCli(
                $container->get(SymfonyProcessFactory::class),
                $container->get(WordPressPath::class),
                $container->get(WpCliPath::class),
            ),
        ],
    ];
};
