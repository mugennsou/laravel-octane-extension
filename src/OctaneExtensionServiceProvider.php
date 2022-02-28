<?php

declare(strict_types=1);

namespace Mugennsou\LaravelOctaneExtension;

use Mugennsou\LaravelOctaneExtension\Console\Commands;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OctaneExtensionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-octane-extension')
            ->hasCommands(
                [
                    Commands\InstallCommand::class,
                    Commands\StartCommand::class,
                    Commands\StatusCommand::class,
                    Commands\ReloadCommand::class,
                    Commands\StopCommand::class,
                ]
            );
    }
}
