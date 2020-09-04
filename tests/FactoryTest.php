<?php declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use rkwadriga\filereader\AbstractReader;
use rkwadriga\filereader\Factory;
use rkwadriga\filereader\FRException;
use rkwadriga\filereader\CsvReader;
use rkwadriga\filereader\JsonReader;
use rkwadriga\filereader\LogReader;
use rkwadriga\filereader\SqlReader;
use rkwadriga\filereader\TxtReader;
use rkwadriga\filereader\YamlReader;
use rkwadriga\filereader\YmlReader;

class FactoryTest extends TestCase
{
    // Run test: vendor/bin/phpunit tests/FactoryTest

    private string $filesDir = __DIR__ . DIRECTORY_SEPARATOR . 'files';

    public function testReadersClasses() : void
    {
        $this->assertInstanceOf(CsvReader::class, $this->getReaderForExt(Factory::EXT_CSV));
        $this->assertInstanceOf(SqlReader::class, $this->getReaderForExt(Factory::EXT_SQL));
        $this->assertInstanceOf(TxtReader::class, $this->getReaderForExt(Factory::EXT_TXT));
        $this->assertInstanceOf(LogReader::class, $this->getReaderForExt(Factory::EXT_LOG));
        $this->assertInstanceOf(JsonReader::class, $this->getReaderForExt(Factory::EXT_JSON));
        $this->assertInstanceOf(YmlReader::class, $this->getReaderForExt(Factory::EXT_YML));
        $this->assertInstanceOf(YamlReader::class, $this->getReaderForExt(Factory::EXT_YAML));
    }

    public function testNotAutoCreatingFiles() : void
    {
        $reader = $this->getReaderForFile('not_existing_file.txt', false);

        // Check not existed file
        $this->expectException(FRException::class);
        $this->expectExceptionCode(FRException::CODE_FILE_NOT_FOUND);
        $reader->readFile();
    }

    public function testNotAllowedExtension() : void
    {
        // Check not allowed extension
        $this->expectException(FRException::class);
        $this->expectExceptionCode(FRException::CODE_INVALID_EXTENSION);
        $this->getReaderForExt('xtxt');
    }

    public function testAutoCreatingFiles() : void
    {
        // File not exists
        $reader = $this->getReaderForFile('not_existing_file.txt', false);
        $this->assertFileNotExists($reader->getFile()->path);
        // File exist
        $reader = $this->getReaderForFile('not_existing_file.txt', true);
        $this->assertFileExists($reader->getFile()->path);
        // Delete file
        unlink($reader->getFile()->path);
    }

    public function testExtensionsMapping() : void
    {
        $factory = new Factory($this->filesDir, ['xtxt' => Factory::EXT_TXT, 'xjson' => Factory::EXT_JSON, 'xcsv' => Factory::EXT_CSV]);
        $this->assertInstanceOf(TxtReader::class, $factory->getReader($this->getFile('xtxt'), false));
        $this->assertInstanceOf(JsonReader::class, $factory->getReader($this->getFile('xjson'), false));
        $this->assertInstanceOf(CsvReader::class, $factory->getReader($this->getFile('xcsv'), false));
    }

    private function getReaderForFile(string $file, bool $autoCreate = true) : AbstractReader
    {
        return (new Factory($this->filesDir))->getReader($file, $autoCreate);
    }

    private function getReaderForExt(string $ext, bool $autoCreate = true) : AbstractReader
    {
        return (new Factory($this->filesDir))->getReader($this->getFile($ext), $autoCreate);
    }

    private function getFile(string $ext) : string
    {
        return 'test.' . $ext;
    }
}