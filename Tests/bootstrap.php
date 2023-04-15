<?php

use Symfony\Component\Dotenv\Dotenv;

// try to load autoloader both when extension is top-level project and when it is installed as part of a working eZPlatform
if (!file_exists($file = __DIR__.'/../vendor/autoload.php') && !file_exists($file = __DIR__.'/../../../../vendor/autoload.php')) {
    throw new \RuntimeException('Install the dependencies to run the test suite.');
}

require $file;

if (!is_dir($configDir = __DIR__.'/../vendor/ezsystems/ezplatform/config') &&
    !is_dir($configDir = __DIR__.'/../vendor/ibexa/oss-skeleton/config') &&
    !is_dir($configDir = __DIR__.'/../../../../config')) {
    throw new \RuntimeException('Unsupported directory layout.');
}

if (file_exists($configDir.'/bootstrap.php')) {
    require $configDir.'/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname($configDir).'/.env');
}

/// @todo if the kernel is booted first with this variable set to false, by eg. running a cli command instead of phpunit,
///       then it will be cached with the test config not loaded, which will make tests fail. How to prevent that?
Kaliop\eZMigrationBundle\DependencyInjection\eZMigrationExtension::$loadTestConfig = true;
