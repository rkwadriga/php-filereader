<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;
use rkwadriga\filereader\FRException;

class CsvReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/CsvReaderTest

    protected string $testFileContent = "Test attr 1;Test attr 2;Test attr 3;Test attr 4;Test attr 5\nTest val 11;Test val 12;Test val 13;Test val 14;Test val 15\nTest val 21;Test val 22;Test val 23;Test val 24;Test val 25\nTest val 31;Test val 32;Test val 33;Test val 34;Test val 35";

    public function testReading() : void
    {
        // Read file and check the reading result
        $this->assertEquals($this->testData, $this->getReaderForFile('test_read.' . Factory::EXT_CSV)->readFile());
    }

    public function testWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile('test_write.' . Factory::EXT_CSV);
        // Clear test file
        file_put_contents($writer->getFile()->path, '');
        // Write data and check how it's wrote
        $writer->writeData($this->testData);
        $this->assertEquals($this->testFileContent, $writer->getFile()->raw());
    }

    public function testInvalidReading() : void
    {
        // Get invalid file reader
        $reader = $this->getReaderForFile('invalid_test_read.' . Factory::EXT_CSV);
        $this->expectException(FRException::class);
        $this->expectExceptionCode(FRException::CODE_VALIDATION_ERROR);
        $reader->readFile();
    }
}