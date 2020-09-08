<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;

class LogReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/LogReaderTest

    protected string $testFileContent = "Test val 11\nTest val 12\nTest val 13\nTest val 14\nTest val 15\nTest val 21\nTest val 22\nTest val 23\nTest val 24\nTest val 25\nTest val 31\nTest val 32\nTest val 33\nTest val 34\nTest val 35";

    public function testReading() : void
    {
        // Get file reader
        $reader = $this->getReaderForFile('test_read.' . Factory::EXT_LOG);
        // Convert 2-levels data-array to 1-level array
        $testData = [];
        foreach ($this->testData as $dataArr) {
            $testData = array_merge($testData, array_values($dataArr));
        }
        // Read file acn check reading result
        $this->assertEquals($testData, $reader->read());
    }

    public function testWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile('test_write.' . Factory::EXT_LOG);
        // Clear test file
        file_put_contents($writer->getFile()->path, '');
        // Write data and check how it's wrote
        $writer->write($this->testData);
        //print_r($writer->getFile()->raw()); echo "\n"; die;
        $this->assertEquals($this->testFileContent, $writer->getFile()->raw());
    }
}