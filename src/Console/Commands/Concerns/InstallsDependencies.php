<?php

declare(strict_types=1);

namespace Mugennsou\LaravelOctaneExtension\Console\Commands\Concerns;

use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

trait InstallsDependencies
{
    /**
     * Determine if package is installed.
     *
     * @param string $className
     * @return bool
     */
    protected function isPackageInstalled(string $className): bool
    {
        return class_exists($className);
    }

    /**
     * Install packages into the project.
     *
     * @param string ...$packages
     * @return bool
     */
    protected function requirePackages(string ...$packages): bool
    {
        $message = sprintf(
            'Octane extension requires "%s". Do you wish to install it as dependency?',
            implode(', ', $packages)
        );

        if (!$this->confirm($message)) {
            $this->error(sprintf('Octane extension requires "%s".', implode(', ', $packages)));

            return false;
        }

        $command = sprintf('%s require %s --with-all-dependencies', $this->findComposer(), implode(' ', $packages));

        $process = Process::fromShellCommandline($command, base_path(), null, null, null);

        if (DIRECTORY_SEPARATOR !== '\\' && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: ' . $e->getMessage());
            }
        }

        try {
            $process->run(fn ($type, $line) => $this->output->write($line));
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }

        return true;
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer(): string
    {
        $composerPath = getcwd() . '/composer.phar';

        $phpPath = (new PhpExecutableFinder())->find();

        if (!file_exists($composerPath)) {
            $composerPath = (new ExecutableFinder())->find('composer');
        }

        return sprintf('"%s" %s', $phpPath, $composerPath);
    }
}
