<?php

use Kaliop\eZMigrationBundle\API\MigrationInterface;

use Kaliop\eZMigrationBundle\API\Value\Migration;
use Kaliop\eZMigrationBundle\API\Value\MigrationDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;

class noclassname implements MigrationInterface
{
    public static function execute(ContainerInterface $container)
    {
    }
}
