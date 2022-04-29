<?php

namespace Mugennsou\LaravelOctaneExtension\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Octane\Commands\Command;
use Mugennsou\LaravelOctaneExtension\OctaneExtensionAmphpServiceProvider;
use UnhandledMatchError;

class InstallCommand extends Command
{
    use Concerns\InstallsDependencies;
    use Concerns\InvalidServer;

    /**
     * The command's signature.
     *
     * @var string
     */
    public $signature = 'octane-extension:install {--server= : The server that should be used to serve the application}';

    /**
     * The command's description.
     *
     * @var string
     */
    public $description = 'Install the Octane extension components and resources';

    /**
     * Handle the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $server = $this->option('server')
            ?: $this->choice('Which application server you would like to use?', ['amphp']);

        try {
            return (int)!tap(
                match ($server) {
                    'amphp' => $this->installAmphpServer(),
                },
                function (bool $installed) use ($server) {
                    if ($installed) {
                        $this->updateEnvironmentFile($server);

                        $this->info('Octane extension installed successfully.');
                    }
                }
            );
        } catch (UnhandledMatchError $e) {
            return $this->invalidServer($server);
        }
    }

    /**
     * Install the Amphp dependencies.
     *
     * @return bool
     */
    public function installAmphpServer(): bool
    {
        $amphpInstalled = $this->isPackageInstalled(OctaneExtensionAmphpServiceProvider::class);

        if ($amphpInstalled) {
            return true;
        }

        return $this->requirePackages('mugennsou/laravel-octane-extension-amphp:dev-master');
    }

    /**
     * Updates the environment file with the given server.
     *
     * @param string $server
     * @return void
     */
    public function updateEnvironmentFile(string $server)
    {
        $env = app()->environmentFile();

        if (File::exists($env)) {
            $contents = File::get($env);

            if (!Str::contains($contents, 'OCTANE_SERVER=')) {
                File::append($env, PHP_EOL . 'OCTANE_SERVER=' . $server . PHP_EOL);
            } else {
                $this->warn('Please adjust the `OCTANE_SERVER` environment variable.');
            }
        }
    }
}
