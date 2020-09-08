<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;

class YamlReaderTest extends YmlReaderTest
{
    // Run test: vendor/bin/phpunit tests/YamlReaderTest

    public function testReading() : void
    {
        // Read file and check the reading result
        $this->assertEquals($this->testData, $this->getReaderForFile('test_read.' . Factory::EXT_YAML)->readFile());
    }

    public function testWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile('test_write.' . Factory::EXT_YAML);
        // Clear test file
        file_put_contents($writer->getFile()->path, '');
        // Write data and check how it's wrote
        $writer->writeData($this->testData);
        $this->assertEquals($this->testFileContent, $writer->getFile()->raw());
    }
}