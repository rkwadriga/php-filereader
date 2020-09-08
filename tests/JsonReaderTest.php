<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;
use rkwadriga\filereader\FRException;

class JsonReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/JsonReaderTest

    protected string $testFileContent = '[{"Test attr 1":"Test val 11","Test attr 2":"Test val 12","Test attr 3":"Test val 13","Test attr 4":"Test val 14","Test attr 5":"Test val 15"},{"Test attr 1":"Test val 21","Test attr 2":"Test val 22","Test attr 3":"Test val 23","Test attr 4":"Test val 24","Test attr 5":"Test val 25"},{"Test attr 1":"Test val 31","Test attr 2":"Test val 32","Test attr 3":"Test val 33","Test attr 4":"Test val 34","Test attr 5":"Test val 35"}]';

    public function testReading() : void
    {
        // Read file and check the reading result
        $this->assertEquals($this->testData, $this->getReaderForFile('test_read.' . Factory::EXT_JSON)->readFile());
    }

    public function testWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile('test_write.' . Factory::EXT_JSON);
        // Clear test file
        file_put_contents($writer->getFile()->path, '');
        // Write data and check how it's wrote
        $writer->writeData($this->testData);
        $this->assertEquals($this->testFileContent, $writer->getFile()->raw());
    }

    public function testInvalidReading() : void
    {
        // Get invalid file reader
        $reader = $this->getReaderForFile('invalid_test_read.' . Factory::EXT_JSON);
        $this->expectException(FRException::class);
        $this->expectExceptionCode(FRException::CODE_VALIDATION_ERROR);
        $reader->readFile();
    }
}