<?php

namespace Kaliop\eZMigrationBundle\Command;

use Kaliop\eZMigrationBundle\Core\MigrationService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * Base command class that all migration commands extend from.
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var MigrationService
     */
    private $migrationService;

    /** @var OutputInterface $output */
    protected $output;
    /** @var OutputInterface $output */
    protected $errOutput;
    protected $verbosity = OutputInterface::VERBOSITY_NORMAL;

    public function __construct(MigrationService $migrationService)
    {
        $this->migrationService = $migrationService;

        parent::__construct();
    }

    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return MigrationService
     */
    public function getMigrationService()
    {
        return $this->migrationService;
    }

    protected function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        $this->errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
    }

    protected function setVerbosity($verbosity)
    {
        $this->verbosity = $verbosity;
    }

    /**
     * Small trick to allow us to:
     * - lower verbosity between NORMAL and QUIET
     * - have a decent writeln API, even with old SF versions
     * @param string|array $message The message as an array of lines or a single string
     * @param int $verbosity
     * @param int $type
     */
    protected function writeln($message, $verbosity = OutputInterface::VERBOSITY_NORMAL, $type = OutputInterface::OUTPUT_NORMAL)
    {
        if ($this->verbosity >= $verbosity) {
            $this->output->writeln($message, $type);
        }
    }

    /**
     * @param string|array $message The message as an array of lines or a single string
     * @param int $verbosity
     * @param int $type
     */
    protected function writeErrorln($message, $verbosity = OutputInterface::VERBOSITY_QUIET, $type = OutputInterface::OUTPUT_NORMAL)
    {
        if ($this->verbosity >= $verbosity) {

            // When verbosity is set to quiet, SF swallows the error message in the writeln call
            // (unlike for other verbosity levels, which are left for us to handle...)
            // We resort to a hackish workaround to _always_ print errors to stdout, even in quiet mode.
            // If the end user does not want any error echoed, he can just 2>/dev/null
            if ($this->errOutput->getVerbosity() == OutputInterface::VERBOSITY_QUIET) {
                $this->errOutput->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
                $this->errOutput->writeln($message, $type);
                $this->errOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
            }
            else
            {
                $this->errOutput->writeln($message, $type);
            }
        }
    }
}
