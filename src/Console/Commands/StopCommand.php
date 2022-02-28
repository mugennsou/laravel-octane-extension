<?php

declare(strict_types=1);

namespace Mugennsou\LaravelOctaneExtension\Console\Commands;

use Laravel\Octane\Commands\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class StopCommand extends Command
{
    use Concerns\InvalidServer;

    /**
     * The command's signature.
     *
     * @var string
     */
    public $signature = 'octane-extension:stop {--server= : The server that is running the application}';

    /**
     * The command's description.
     *
     * @var string
     */
    public $description = 'Stop the Octane extension server';

    /**
     * Handle the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $server = $this->option('server') ?: config('octane-extension.server');

        try {
            return $this->call(sprintf('octane-extension:stop-%s', $server));
        } catch (CommandNotFoundException $e) {
            return $this->invalidServer($server);
        }
    }
}
