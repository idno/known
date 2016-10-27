<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\Aggregate;

class AggregateFunctionalTest extends FunctionalTestCase
{
    private static $wireVersionForCursor = 2;

    /**
     * @expectedException MongoDB\Driver\Exception\RuntimeException
     */
    public function testUnrecognizedPipelineState()
    {
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$foo' => 1]]);
        $operation->execute($this->getPrimaryServer());
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocument
     */
    public function testTypeMapOption(array $typeMap, array $expectedDocuments)
    {
        if ( ! \MongoDB\server_supports_feature($this->getPrimaryServer(), self::$wireVersionForCursor)) {
            $this->markTestSkipped('Command cursor is not supported');
        }

        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, ['typeMap' => $typeMap]);
        $cursor = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($expectedDocuments, $cursor->toArray());
    }

    public function provideTypeMapOptionsAndExpectedDocument()
    {
        return [
            [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['_id' => 1, 'x' => ['foo' => 'bar']],
                    ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                [
                    (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
                    (object) ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                    ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
                ],
            ],
        ];
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures($n)
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (object) ['foo' => 'bar'],
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
