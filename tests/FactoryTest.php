<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

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

class FactoryTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/FactoryTest

    protected string $notExistedFile = 'not_existed_file.' . Factory::EXT_TXT;

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
        // Get file reader
        $reader = $this->getReaderForFile($this->notExistedFile, false);
        // Remove file if exist
        $file = $reader->getFile()->path;
        if (file_exists($file)) {
            unlink($file);
        }
        // Check not existed file
        $this->expectException(FRException::class);
        $this->expectExceptionCode(FRException::CODE_FILE_NOT_FOUND);
        $reader->read();
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
        // Get reader for not existed file
        $reader = $this->getReaderForFile($this->notExistedFile, false);
        $file = $reader->getFile()->path;
        // Check is file not exists
        $this->assertFileNotExists($file);
        // Check is file exists
        $this->getReaderForFile($this->notExistedFile, true);
        $this->assertFileExists($file);
        // Delete file
        unlink($file);
    }

    public function testExtensionsMapping() : void
    {
        $factory = new Factory($this->filesDir, ['xtxt' => Factory::EXT_TXT, 'xjson' => Factory::EXT_JSON, 'xcsv' => Factory::EXT_CSV]);
        $this->assertInstanceOf(TxtReader::class, $factory->getReader($this->getFile('xtxt'), false));
        $this->assertInstanceOf(JsonReader::class, $factory->getReader($this->getFile('xjson'), false));
        $this->assertInstanceOf(CsvReader::class, $factory->getReader($this->getFile('xcsv'), false));
    }

    protected function getReaderForFile(string $file, bool $autoCreate = true) : AbstractReader
    {
        return $this->getFactory(false)->getReader($file, $autoCreate);
    }
}