<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use rkwadriga\filereader\Factory;

class ReaderTest extends ReaderTestAbstract
{
    // Run test: vendor/bin/phpunit tests/ReaderTest

    protected string $notExistedFile = 'not_existed_file.' . Factory::EXT_TXT;

    public function testNotExistedFileWriting() : void
    {
        // Get file writer
        $writer = $this->getReaderForFile($this->notExistedFile , false);
        $file = $writer->getFile()->path;
        // Remove file if exist
        if (file_exists($file)) {
            unlink($file);
        }
        // Write data and check the writing result
        $data = ['String 1', 'String 2'];
        $dataString = "String 1\nString 2";
        $writer->write($data);
        $this->assertFileExists($file);
        $this->assertEquals($dataString, file_get_contents($file));
        // Delete file
        unlink($file);
    }
}