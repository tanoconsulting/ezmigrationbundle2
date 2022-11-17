<?php

namespace Kaliop\eZMigrationBundle\Core\StorageHandler\Database;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Schema\Schema;
use Kaliop\eZMigrationBundle\API\ContextStorageHandlerInterface;

class Context extends TableStorage implements ContextStorageHandlerInterface
{
    protected $fieldList = 'migration, context, insertion_date';

    public function loadMigrationContext($migrationName)
    {
        $this->createTableIfNeeded();

        $qb = $this->connection->createQueryBuilder();
        $qb->select($this->fieldList)
            ->from($this->tableName)
            ->where($qb->expr()->eq('migration', $qb->createPositionalParameter($migrationName)));
        $result = $qb->execute()->fetchAssociative();

        if (is_array($result) && !empty($result)) {
            return $this->stringToContext($result['context']);
        }

        return null;
    }

    /**
     * Stores a migration context
     *
     * @param string $migrationName
     * @param array $context
     */
    public function storeMigrationContext($migrationName, array $context)
    {
        $this->createTableIfNeeded();

        // select for update

        // annoyingly enough, neither Doctrine nor EZP provide built in support for 'FOR UPDATE' in their query builders...
        // at least the doctrine one allows us to still use parameter binding when we add our sql particle
        $conn = $this->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb->select('*')
            ->from($this->tableName, 'm')
            ->where('migration = ?');
        $sql = $qb->getSQL() . ' FOR UPDATE';

        $conn->beginTransaction();

        $stmt = $conn->executeQuery($sql, array($migrationName));
        $existingMigrationData = $stmt->fetchAssociative();

        if (is_array($existingMigrationData)) {
            // context exists

            $conn->update(
                $this->tableName,
                array(
                    'context' => $this->contextToString($context),
                    'insertion_date' => time(),
                ),
                array('migration' => $migrationName)
            );
            $conn->commit();

        } else {
            // context did not exist. Create it!

            // commit immediately, to release the lock and avoid deadlocks
            $conn->commit();

            $conn->insert($this->tableName, array(
                'migration' => $migrationName,
                'context' => $this->contextToString($context),
                'insertion_date' => time(),
            ));
        }
    }

    /**
     * Removes a migration context from storage
     *
     * @param string $migrationName
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteMigrationContext($migrationName)
    {
        $this->createTableIfNeeded();
        $conn = $this->getConnection();
        $conn->delete($this->tableName, array('migration' => $migrationName));
    }

    /**
     * Removes all migration contexts from storage (regardless of the migration status/existence)
     */
    public function deleteMigrationContexts()
    {
        $this->truncate();
    }

    /**
     * @throws DriverException
     */
    public function createTable()
    {
        /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager $sm */
        $sm = $this->connection->getSchemaManager();
        $dbPlatform = $sm->getDatabasePlatform();

        $schema = new Schema();

        $t = $schema->createTable($this->tableName);
        $t->addColumn('migration', 'string', array('length' => 255));
        $t->addColumn('context', 'text');
        $t->addColumn('insertion_date', 'integer');
        $t->setPrimaryKey(array('migration'));

        $this->injectTableCreationOptions($t);

        foreach ($schema->toSql($dbPlatform) as $sql) {
            try {
                $this->connection->executeStatement($sql);
            } catch (DriverException $e) {
                // work around limitations in both Mysql and Doctrine
                // @see https://github.com/kaliop-uk/ezmigrationbundle/issues/176
                if (strpos($e->getMessage(), '1071 Specified key was too long; max key length is 767 bytes') !== false &&
                    strpos($sql, 'PRIMARY KEY(migration)') !== false) {
                    $this->connection->executeStatement(str_replace('PRIMARY KEY(migration)', 'PRIMARY KEY(migration(191))', $sql));
                } else {
                    throw $e;
                }
            }
        }
    }

    protected function stringToContext($data)
    {
        return json_decode($data, true);
    }

    protected function contextToString(array $context)
    {
        return json_encode($context);
    }
}
