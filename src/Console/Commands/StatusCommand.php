<?php

declare(strict_types=1);

namespace Mugennsou\LaravelOctaneExtension\Console\Commands;

use Laravel\Octane\Commands\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class StatusCommand extends Command
{
    use Concerns\InvalidServer;

    /**
     * The command's signature.
     *
     * @var string
     */
    public $signature = 'octane-extension:status {--server= : The server that is running the application}';

    /**
     * The command's description.
     *
     * @var string
     */
    public $description = 'Get the current status of the Octane extension server';

    /**
     * Handle the command.
     *
     * @return int
     */
    public function handle(): int
    {
        $server = $this->option('server') ?: config('octane.server');

        try {
            return $this->call(sprintf('octane-extension:status-%s', $server));
        } catch (CommandNotFoundException $e) {
            return $this->invalidServer($server);
        }
    }
}
