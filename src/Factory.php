<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class Factory
{
    private ?string $filePath;
    /** @var AbstractReader[] */
    private array $readers = [];

    const EXT_CSV = 'csv';
    const EXT_SQL = 'sql';
    const EXT_TXT = 'txt';
    const EXT_LOG = 'log';
    const EXT_JSON = 'json';
    const EXT_YML = 'yml';
    const EXT_YAML = 'yaml';

    private array $classesMap = [
        self::EXT_CSV => 'rkwadriga\filereader\CsvReader',
        self::EXT_SQL => 'rkwadriga\filereader\SqlReader',
        self::EXT_TXT => 'rkwadriga\filereader\TxtReader',
        self::EXT_LOG => 'rkwadriga\filereader\LogReader',
        self::EXT_JSON => 'rkwadriga\filereader\JsonReader',
        self::EXT_YML => 'rkwadriga\filereader\YmlReader',
        self::EXT_YAML => 'rkwadriga\filereader\YamlReader',
    ];

    public function __construct(?string $filePath = null)
    {
        $this->filePath = $filePath !== null ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath) : null;
    }

    public function getReader(string $forFile, bool $autoCreate = true) : AbstractReader
    {
        if (!isset($this->readers[$forFile])) {
            $file = Helper::checkFilePath($forFile, $autoCreate, $this->filePath);
            $this->readers[$forFile] = $this->createFileReader($file);
        }
        return $this->readers[$forFile];
    }

    private function createFileReader(string $forFile) : AbstractReader
    {
        $ext = Helper::getExt($forFile);

        if ($ext === null || !isset($this->classesMap[$ext])) {
            throw new FRException(sprintf('Unsupported file format: "%s"', $ext), FRException::CODE_INVALID_EXTENSION);
        }

        $readerClass = $this->classesMap[$ext];
        if (!is_subclass_of($readerClass, AbstractReader::class)) {
            throw new FRException(sprintf('File reader must instance of %s abstract class, but it does not. Reader class: %s', AbstractReader::class, $readerClass), FRException::CODE_UNKNOWN_ERROR);
        }

        return new $readerClass($forFile, $ext);
    }
}