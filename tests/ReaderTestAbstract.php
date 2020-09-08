<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

use PHPUnit\Framework\TestCase;
use rkwadriga\filereader\AbstractReader;
use rkwadriga\filereader\Factory;

abstract class ReaderTestAbstract extends TestCase
{
    protected string $filesDir = __DIR__ . DIRECTORY_SEPARATOR . 'files';
    protected Factory $factory;

    protected array $testData = [
        [
            'Test attr 1' => 'Test val 11',
            'Test attr 2' => 'Test val 12',
            'Test attr 3' => 'Test val 13',
            'Test attr 4' => 'Test val 14',
            'Test attr 5' => 'Test val 15',
        ],
        [
            'Test attr 1' => 'Test val 21',
            'Test attr 2' => 'Test val 22',
            'Test attr 3' => 'Test val 23',
            'Test attr 4' => 'Test val 24',
            'Test attr 5' => 'Test val 25',
        ],
        [
            'Test attr 1' => 'Test val 31',
            'Test attr 2' => 'Test val 32',
            'Test attr 3' => 'Test val 33',
            'Test attr 4' => 'Test val 34',
            'Test attr 5' => 'Test val 35',
        ],
    ];

    protected string $testFileContent = '';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->factory = new Factory($this->filesDir);
    }

    protected function getFactory(bool $singleton = true) : Factory
    {
        return $singleton ? $this->factory : new Factory($this->filesDir);
    }

    protected function getReaderForFile(string $file, bool $autoCreate = true) : AbstractReader
    {
        return $this->factory->getReader($file, $autoCreate);
    }

    protected function getReaderForExt(string $ext, bool $autoCreate = true) : AbstractReader
    {
        return $this->getReaderForFile($this->getFile($ext), $autoCreate);
    }

    protected function getReaderForNotExistedFile(string $ext, $autoCreate = false) : AbstractReader
    {
        return $this->getReaderForFile('not_existed_file.' . $ext, $autoCreate);
    }

    protected function getFile(string $ext) : string
    {
        return 'test.' . $ext;
    }
}