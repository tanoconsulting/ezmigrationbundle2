<?php

include_once(__DIR__.'/MigrationExecutingTest.php');

use Kaliop\eZMigrationBundle\API\Value\Migration;

/**
 * Tests transaction handling
 */
class TransactionsTest extends MigrationExecutingTest
{
    /**
     * Test executing the migration without the `-u` option: wrap it in a db transaction.
     * This is known to cause issues with php >= 8.0 and mysql, when the migration contains ddl statements, which
     * make the wrapping transaction be committed directly by the db. We check that the code which handles
     * the wrapping transaction in the MigrationService can cope with that
     *
     * @todo skip when db is not mysql
     */
    public function testMysqlAutocommit()
    {
        $this->runMigration($this->dslDir.'/transactions/UnitTestOK1011_mysql_create_table.sql');

        $this->runMigration($this->dslDir.'/transactions/UnitTestOK1012_mysql_drop_table.sql');
    }

    /**
     * Test the migration rollback: a failed migration should not leave partial data in the db
     */
    public function testRollback()
    {
        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $this->getBootedContainer()->get('ezpublish.persistence.connection');

        $this->runMigration($this->dslDir.'/transactions/UnitTestOK1021_create_table.yml');

        $this->runMigration($this->dslDir.'/transactions/UnitTestOK1023_check_data.yml');

        $this->runMigration($this->dslDir.'/transactions/UnitTestOK1024_drop_table.yml');
    }
}
