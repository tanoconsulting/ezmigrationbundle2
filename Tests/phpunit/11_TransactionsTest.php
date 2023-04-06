<?php

include_once(__DIR__.'/MigrationExecutingTest.php');

use Kaliop\eZMigrationBundle\API\Value\Migration;
use Kaliop\eZMigrationBundle\Tests\helper\BeforeStepExecutionListener;
use Kaliop\eZMigrationBundle\Tests\helper\StepExecutedListener;

/**
 * Tests transaction handling
 */
class TransactionsTest extends MigrationExecutingTest
{
    public function testMysqlAutocommit()
    {
        /// @todo skip when db is not mysql

        $filePath = $this->dslDir.'/transactions/UnitTestOK1011_mysql_create_table.sql';

        $ms = $this->getBootedContainer()->get('ez_migration_bundle.migration_service');

        // Make sure migration is not in the db: delete it, ignoring errors
        $this->prepareMigration($filePath);

        $output = $this->runCommand('kaliop:migration:migrate', array('--path' => array($filePath), '-n' => true));

        $m = $ms->getMigration(basename($filePath));
        $this->assertEquals($m->status, Migration::STATUS_DONE, 'Migration supposed to be completed but in unexpected state');

        $this->deleteMigration($filePath);
    }
}
