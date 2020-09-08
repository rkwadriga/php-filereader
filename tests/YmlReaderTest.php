<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;

class YmlReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/YmlReaderTest

    protected array $testData = [
        'Component1' => [
            'Component1Param1' => 'Component1Value1',
            'Component1Param2' => 'Component1Value2',
            'Component11' => [
                'Component11Param1' => 'Component11Value1',
                'Component11Param2' => 'Component11Value2'
            ],
        ],
        'Component2' => [
            'Component2Param1' => 'Component2Value1',
            'Component2Param2' => 'Component2Value2'
        ],
    ];
    protected string $testFileContent = "Component1:\n  Component1Param1: Component1Value1\n  Component1Param2: Component1Value2\n  Component11:\n    Component11Param1: Component11Value1\n    Component11Param2: Component11Value2\nComponent2:\n  Component2Param1: Component2Value1\n  Component2Param2: Component2Value2";

    public function testReading() : void
    {
        // Read file and check the reading result
        $this->assertEquals($this->testData, $this->getReaderForFile('test_read.' . Factory::EXT_YML)->readFile());
    }
}