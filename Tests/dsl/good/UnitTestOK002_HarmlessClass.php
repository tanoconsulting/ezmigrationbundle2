<?php

use Kaliop\eZMigrationBundle\API\MigrationInterface;

use Kaliop\eZMigrationBundle\API\Value\Migration;
use Kaliop\eZMigrationBundle\API\Value\MigrationDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HarmlessClass implements MigrationInterface
{
    public static function execute(ContainerInterface $container)
    {
        // return non-null to make sure that the listener tests pass
        return 1;
    }
}
