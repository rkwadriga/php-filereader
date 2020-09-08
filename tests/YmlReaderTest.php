<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;

class YmlReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/YmlReaderTest

    protected array $testData = [
        'Component1' => [
            'Component1Param1' => 'Component1Value1',
            'Component1Param2' => true,
            'Component1Param3' => false,
            'Component11' => [
                'Component11Param1' => 'Component11Value1',
                'Component11Param2' => 'Component11Value2',
                'Component11Param3' => null
            ],
        ],
        'Component2' => [
            'Component2Param1' => 'Component2Value1',
            'Component2Param2' => 'Component2Value2'
        ],
    ];
    protected string $testFileContent = "Component1:\n  Component1Param1: Component1Value1\n  Component1Param2: true\n  Component1Param3: false\n  Component11:\n    Component11Param1: Component11Value1\n    Component11Param2: Component11Value2\n    Component11Param3: null\nComponent2:\n  Component2Param1: Component2Value1\n  Component2Param2: Component2Value2";

    public function testReading() : void
    {
        // Read file and check the reading result
        $this->assertEquals($this->testData, $this->getReaderForFile('test_read.' . Factory::EXT_YML)->readFile());
    }

    public function testWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile('test_write.' . Factory::EXT_YML);
        // Clear test file
        file_put_contents($writer->getFile()->path, '');
        // Write data and check how it's wrote
        $writer->writeData($this->testData);
        $this->assertEquals($this->testFileContent, $writer->getFile()->raw());
    }
}