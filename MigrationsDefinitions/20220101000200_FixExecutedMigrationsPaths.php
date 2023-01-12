<?php

use Doctrine\DBAL\Connection;
use Kaliop\eZMigrationBundle\API\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kaliop\eZMigrationBundle\API\Value\Migration;
use Kaliop\eZMigrationBundle\API\Value\MigrationDefinition;

/**
 * Make paths of all executed migrations relative, if possible
 */
class FixExecutedMigrationsPaths implements MigrationInterface
{
    private $container;
    /** @var Connection */
    private $connection;
    private $migrationsTableName;

    // The API says we have to have a static method, but we like better non-static... :-P
    public static function execute(ContainerInterface $container)
    {
        $migration = new self($container);
        $migration->goForIt();
    }

    private function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function goForIt()
    {
        $this->migrationsTableName = $this->container->getParameter('ez_migration_bundle.table_name');

        $this->connection = $this->container->get('ezpublish.persistence.connection');

        /** @var \Kaliop\eZMigrationBundle\Core\Helper\ConsoleIO $io */
        $io = $this->container->get('ez_migration_bundle.helper.console_io');
        // NB: in theory this could be null!
        $output = $io->getOutput();

        if (!$this->tableExist($this->migrationsTableName))
        {
            $output->writeln("Nothing to update: database table '{$this->migrationsTableName}' not found");
            return;
        }

        $toMigrate = $this->loadAllMigrations();
        $output->writeln("<info>Found " . count($toMigrate) . ' migrations in the database table</info>');

        $rootDir = realpath($this->container->get('kernel')->getProjectDir() . '/..') . '/';

        foreach ($toMigrate as $legacyMigration) {
            $name = $legacyMigration['migration'];
            $path = $legacyMigration['path'];
            // note: we handle the case of 'current dir', but path is expected to include a filename...
            if (strpos($path, './') === 0 && $path !== './') {
                $this->updateMigrationPath($name, substr($path, 2));
                $output->writeln("Updated path info for migration: {$name} ({$path})");
            } elseif (strpos($path, $rootDir) === 0) {
                if ($path === $rootDir) {
                    $this->updateMigrationPath($name, './');
                } else {
                    $this->updateMigrationPath($name, substr($path, strlen($rootDir)));
                }
                $output->writeln("Updated path info for migration: {$name} ({$path})");
            }
        }

        $output->writeln("<info>All known migrations have been modified to use a relative path</info>");
    }

    private function tableExist($tableName)
    {
        /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager $sm */
        $sm = $this->connection->getSchemaManager();
        foreach ($sm->listTables() as $table) {
            if ($table->getName() == $tableName) {
                return true;
            }
        }

        return false;
    }

    private function loadAllMigrations()
    {
        $q = $this->connection->createQueryBuilder();
        $q->select('migration, path')
            ->from($this->migrationsTableName)
            ->orderBy('migration', 'ASC');
        $result = $q->execute();

        return $result->fetchAllAssociative();
    }

    private function updateMigrationPath($migrationName, $path)
    {
        $this->connection->executeQuery(
            'UPDATE ' . $this->migrationsTableName . ' SET path = :newPath WHERE migration = :migrationName',
            ['newPath' => $path, 'migrationName' => $migrationName]
        );
    }
}
