<?php

include_once(__DIR__.'/CommandExecutingTest.php');

use Kaliop\eZMigrationBundle\API\Collection\MigrationStepsCollection;
use Kaliop\eZMigrationBundle\API\Value\MigrationStep;

/**
 * Tests the 'filtering' features of the collections. Plain usage as array is already tested by the rest of the suite
 * @todo should we extend just KernelTestCase ?
 */
class CollectionsTest extends CommandExecutingTest
{
    /**
     * @doesNotPerformAssertions
     */
    public function testValidElements1()
    {
        $collection = new MigrationStepsCollection(array(new MigrationStep('test')));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidElements2()
    {
        $collection = new MigrationStepsCollection(array());
        $collection[] = new MigrationStep('test');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidElements3()
    {
        $collection = new MigrationStepsCollection(array());
        $collection['test'] = new MigrationStep('test');
    }

    public function testInvalidElements1()
    {
        $this->expectException('InvalidArgumentException');
        $collection = new MigrationStepsCollection(array(1));
    }

    public function testInvalidElements2()
    {
        $collection = new MigrationStepsCollection(array());

        $this->expectException('InvalidArgumentException');
        $collection[] = 'not a content';
    }

    public function testInvalidElements3()
    {
        $collection = new MigrationStepsCollection(array());

        $this->expectException('InvalidArgumentException');
        $collection['test'] = true;
    }

    public function testExchangeArray()
    {
        $collection = new MigrationStepsCollection(array(new MigrationStep('test1')));
        $collection->exchangeArray(array(new MigrationStep('test2')));
        $this->assertSame('test2', $collection[0]->type);

        $this->expectException('InvalidArgumentException');
        $collection->exchangeArray(array('hello'));
    }
}
