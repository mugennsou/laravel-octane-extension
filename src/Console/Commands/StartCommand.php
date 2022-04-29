<?php

declare(strict_types=1);

namespace Mugennsou\LaravelOctaneExtension\Console\Commands;

use Laravel\Octane\Commands\Command;
use Laravel\Octane\Commands\Concerns as OctaneConcerns;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class StartCommand extends Command implements SignalableCommandInterface
{
    use Concerns\InvalidServer;
    use OctaneConcerns\InteractsWithServers;

    /**
     * The command's signature.
     *
     * @var string
     */
    public $signature = 'octane-extension:start
                    {--server= : The server that should be used to serve the application}
                    {--host=127.0.0.1 : The IP address the server should bind to}
                    {--port=8000 : The port the server should be available on}
                    {--workers=auto : The number of workers that should be available to handle requests}
                    {--max-requests=500 : The number of requests to process before reloading the server}
                    {--watch : Automatically reload the server when the application is modified}';

    /**
     * The command's description.
     *
     * @var string
     */
    public $description = 'Start the Octane extension server';

    /**
     * Handle the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $server = $this->option('server') ?: config('octane.server');

        try {
            return $this->call(
                sprintf('octane-extension:start-%s', $server),
                [
                    '--host' => $this->option('host'),
                    '--port' => $this->option('port'),
                    '--workers' => $this->option('workers'),
                    '--max-requests' => $this->option('max-requests'),
                    '--watch' => $this->option('watch'),
                ]
            );
        } catch (CommandNotFoundException $e) {
            return $this->invalidServer($server);
        }
    }

    /**
     * Stop the server.
     *
     * @return void
     */
    protected function stopServer(): void
    {
        $server = $this->option('server') ?: config('octane.server');

        $this->callSilent('octane-extension:stop', ['--server' => $server]);
    }
}
