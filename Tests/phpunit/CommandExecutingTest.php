<?php

use Symfony\Component\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

abstract class CommandExecutingTest extends KernelTestCase
{
    protected $leftovers = array();

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
    private $_container;
    /** @var \Symfony\Component\Console\Application $app */
    protected $app;
    /** @var StreamOutput $output */
    protected $output;

    // tell to phpunit not to mess with ezpublish legacy global vars...
    protected $backupGlobalsBlacklist = array('eZCurrentAccess');

    protected function setUp(): void
    {
        $this->_container = $this->bootContainer();

        $this->app = new Application(static::$kernel);
        $this->app->setAutoExit(false);
        $fp = fopen('php://temp', 'r+');
        $this->output = new StreamOutput($fp);
        $this->leftovers = array();
    }

    /**
     * Fetches the data from the output buffer, resetting it.
     * It would be nice to use BufferedOutput, but that is not available in Sf 2.3...
     * @return null|string
     */
    protected function fetchOutput()
    {
        if (!$this->output) {
            return null;
        }

        $fp = $this->output->getStream();
        rewind($fp);
        $out = stream_get_contents($fp);

        fclose($fp);
        $fp = fopen('php://temp', 'r+');
        $this->output = new StreamOutput($fp);

        return $out;
    }

    protected function tearDown(): void
    {
        foreach ($this->leftovers as $file) {
            unlink($file);
        }

        // clean buffer, just in case...
        if ($this->output) {
            $fp = $this->output->getStream();
            fclose($fp);
            $this->output = null;
        }

        // shuts down the kernel etc...
        parent::tearDown();
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     * @throws Exception
     */
    protected function bootContainer()
    {
        static::ensureKernelShutdown();

        if (!isset($_SERVER['APP_ENV'])) {
            throw new \Exception("Please define the environment variable APP_ENV to specify the environment to use for the tests");
        }
        // Run in our own test environment. Sf by default uses the 'test' one. We let phpunit.xml set it...
        // We also allow to disable debug mode
        $options = array(
            'environment' => $_SERVER['APP_ENV']
        );
        if (isset($_SERVER['APP_DEBUG'])) {
            $options['debug'] = $_SERVER['APP_DEBUG'];
        }
        try {
            static::bootKernel($options);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage() . " Did you forget to define the environment variable KERNEL_CLASS?", $e->getCode(), $e->getPrevious());
        }

        return static::$container;
    }

    protected function getContainer()
    {
        return $this->_container;
    }

    /**
     * @param string $commandName
     * @param array $params
     * @param bool $checkExitCode
     * @return string|null
     * @throws Exception
     */
    protected function runCommand($commandName, array $params = array(), $checkExitCode = true)
    {
        $exitCode = $this->app->run($this->buildInput($commandName, $params), $this->output);
        $output = $this->fetchOutput();
        if ($checkExitCode) {
            $this->assertSame(0, $exitCode, 'CLI Command failed. Output: ' . $output);
        }
        return $output;
    }

    /**
     * @param $commandName
     * @param array $params
     * @return ArrayInput
     */
    protected function buildInput($commandName, array $params = array())
    {
        $params = array_merge(['command' => $commandName], $params);
        return new ArrayInput($params);
    }
}
