<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;

class YamlReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/YamlReaderTest

    protected array $testData = [];
    protected string $testFileContent = "";

    public function testReading() : void
    {
        $this->assertEquals('OK', 'OK');
    }
}