<?php

declare(strict_types=1);

namespace Mugennsou\LaravelOctaneExtension\Console\Commands\Concerns;

trait InvalidServer
{
    /**
     * Inform the user that the server type is invalid.
     *
     * @param string $server
     * @return int
     */
    protected function invalidServer(string $server): int
    {
        $this->error(sprintf('Invalid server: %s', $server));

        return 1;
    }
}
