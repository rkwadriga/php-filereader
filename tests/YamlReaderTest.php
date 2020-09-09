<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;
use rkwadriga\filereader\FRException;

class YamlReaderTest extends ReaderTestAbstract
{
    // Run test: ./run tests/YamlReaderTest

    use YmlReaderTestTrait;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testData = $this->getTestData();
        $this->testFileContent = $this->getTestFileContent();
    }

    public function testDataConverting() : void
    {
        $reader = $this->getReaderForNotExistedFile(Factory::EXT_YAML);
        $this->assertEquals($this->testFileContent, $reader->convertData($this->testData));
    }

    public function testReading() : void
    {
        // Read file and check the reading result
        $this->assertEquals($this->testData, $this->getReaderForFile('test_read.' . Factory::EXT_YAML)->read());
    }

    public function testWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile('test_write.' . Factory::EXT_YAML);
        // Clear test file
        file_put_contents($writer->getFile()->path, '');
        // Write data and check how it's wrote
        $writer->write($this->testData);
        $this->assertEquals($this->testFileContent, $writer->getFile()->raw());
    }

    public function testInvalidReading() : void
    {
        // Get invalid file reader
        $reader = $this->getReaderForFile('invalid_test_read.' . Factory::EXT_YAML);
        $this->expectException(FRException::class);
        $this->expectExceptionCode(FRException::CODE_VALIDATION_ERROR);
        $reader->read();
    }
}