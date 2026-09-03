<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Syde\WpPhpUnitIntegration\BuiltInServer;
use Syde\WpPhpUnitIntegration\EnvVar;
use Syde\WpPhpUnitIntegration\Package\PackageComposerJsonReader;
use Syde\WpPhpUnitIntegration\Package\PackageType;
use Syde\WpPhpUnitIntegration\Package\PackageTypeDetector;
use Syde\WpPhpUnitIntegration\Path\EnvPath;
use Syde\WpPhpUnitIntegration\Path\Finder\CachedLocalDependencyPathFinder;
use Syde\WpPhpUnitIntegration\Path\Finder\CachedLocalDependencyPathFinderKey;
use Syde\WpPhpUnitIntegration\Path\Finder\EnvPathFinder;
use Syde\WpPhpUnitIntegration\Path\Finder\LocalDependencyPathFinder;
use Syde\WpPhpUnitIntegration\Path\Finder\SqliteDatabaseIntegrationPluginFinder;
use Syde\WpPhpUnitIntegration\Path\Finder\WordPressPathFinder;
use Syde\WpPhpUnitIntegration\Path\Finder\WpCliPathFinder;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPath;
use Syde\WpPhpUnitIntegration\Path\LocalDependencyPathNormalizer;
use Syde\WpPhpUnitIntegration\Path\PackageRootPath;
use Syde\WpPhpUnitIntegration\Path\SqliteDatabaseIntegrationPluginPath;
use Syde\WpPhpUnitIntegration\Path\WordPressPath;
use Syde\WpPhpUnitIntegration\Path\WpCliPath;
use Syde\WpPhpUnitIntegration\PhpProcessIdProvider;
use Syde\WpPhpUnitIntegration\ShutdownFunctionRegisterer;
use Syde\WpPhpUnitIntegration\SymfonyProcessFactory;
use Syde\WpPhpUnitIntegration\Task\ActivateTestedPlugin;
use Syde\WpPhpUnitIntegration\Task\ActivateTestedPluginDependencies;
use Syde\WpPhpUnitIntegration\Task\ActivateTestedTheme;
use Syde\WpPhpUnitIntegration\Task\Bundle\Cleanup;
use Syde\WpPhpUnitIntegration\Task\Bundle\Load;
use Syde\WpPhpUnitIntegration\Task\Bundle\Setup;
use Syde\WpPhpUnitIntegration\Task\CreateEmptyWpThemesDir;
use Syde\WpPhpUnitIntegration\Task\CreateSqliteDbDropIn;
use Syde\WpPhpUnitIntegration\Task\CreateWpConfig;
use Syde\WpPhpUnitIntegration\Task\DefineRequiredWpConstants;
use Syde\WpPhpUnitIntegration\Task\DeleteDbDropIn;
use Syde\WpPhpUnitIntegration\Task\DeleteSqliteDatabaseDir;
use Syde\WpPhpUnitIntegration\Task\DeleteWpConfig;
use Syde\WpPhpUnitIntegration\Task\DeleteWpDebugLog;
use Syde\WpPhpUnitIntegration\Task\DeleteWpUploadsDir;
use Syde\WpPhpUnitIntegration\Task\EnableWpDebug;
use Syde\WpPhpUnitIntegration\Task\IncludeWp;
use Syde\WpPhpUnitIntegration\Task\InstallMultisiteWp;
use Syde\WpPhpUnitIntegration\Task\LoadEnvVariables;
use Syde\WpPhpUnitIntegration\Task\MaybeUpgradeCoreWp;
use Syde\WpPhpUnitIntegration\Task\Noop;
use Syde\WpPhpUnitIntegration\Task\RefreshSqliteDb;
use Syde\WpPhpUnitIntegration\Task\SymlinkTestedPlugin;
use Syde\WpPhpUnitIntegration\Task\SymlinkTestedTheme;
use Syde\WpPhpUnitIntegration\Task\UnlinkTestedPlugin;
use Syde\WpPhpUnitIntegration\Task\UnlinkTestedTheme;
use Syde\WpPhpUnitIntegration\TestRunnerProcessTracker;
use Syde\WpPhpUnitIntegration\WpCli;

return static function (string $packageRootPath): array {
    return [
        // /src/Package/
        PackageComposerJsonReader::class => static fn (ContainerInterface $container): PackageComposerJsonReader => new PackageComposerJsonReader(
            $container->get(PackageRootPath::class),
        ),
        PackageTypeDetector::class => static fn (ContainerInterface $container): PackageTypeDetector => new PackageTypeDetector(
            $container->get(PackageComposerJsonReader::class),
        ),
        // /src/Path/Finder/
        CachedLocalDependencyPathFinderKey::class => static fn (): CachedLocalDependencyPathFinderKey => new CachedLocalDependencyPathFinderKey(),
        EnvPathFinder::class => static fn (ContainerInterface $container): LocalDependencyPathFinder => new CachedLocalDependencyPathFinder(
            new EnvPathFinder(
                $container->get(PackageRootPath::class),
            ),
            $container->get(CachedLocalDependencyPathFinderKey::class),
            $container->get(EnvVar::class),
        ),
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
        // /src/Path/
        EnvPath::class => static fn (ContainerInterface $container): LocalDependencyPath => new EnvPath(
            $container->get(EnvPathFinder::class),
            $container->get(LocalDependencyPathNormalizer::class),
        ),
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
        // /src/Task/Bundle/
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
        Setup::class => static function (ContainerInterface $container): Setup {
            $contextual = (match ($container->get(PackageTypeDetector::class)->determine()) {
                PackageType::Plugin => [
                    $container->get(SymlinkTestedPlugin::class),
                    $container->get(ActivateTestedPluginDependencies::class),
                    $container->get(ActivateTestedPlugin::class),
                ],
                PackageType::Theme => [
                    $container->get(SymlinkTestedTheme::class),
                    $container->get(ActivateTestedTheme::class),
                ],
                default => []
            });

            return new Setup(
                $container->get(LoadEnvVariables::class),
                $container->get(CreateSqliteDbDropIn::class),
                $container->get(CreateEmptyWpThemesDir::class),
                $container->get(CreateWpConfig::class),
                $container->get(DefineRequiredWpConstants::class),
                $container->get(EnableWpDebug::class),
                $container->get(InstallMultisiteWp::class),
                $container->get(MaybeUpgradeCoreWp::class),
                ...$contextual,
            );
        },
        // /src/Task/
        ActivateTestedPlugin::class => static fn (ContainerInterface $container): ActivateTestedPlugin => new ActivateTestedPlugin(
            $container->get(PackageRootPath::class),
            $container->get(WpCli::class),
        ),
        ActivateTestedPluginDependencies::class => static fn (ContainerInterface $container): ActivateTestedPluginDependencies => new ActivateTestedPluginDependencies(
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
        LoadEnvVariables::class => static fn (ContainerInterface $container): LoadEnvVariables => new LoadEnvVariables(
            $container->get(EnvPath::class),
        ),
        MaybeUpgradeCoreWp::class => static fn (ContainerInterface $container): MaybeUpgradeCoreWp => new MaybeUpgradeCoreWp(
            $container->get(EnvVar::class),
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
        // /src/
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
    ];
};
