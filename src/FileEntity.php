<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class FileEntity
{
    public string $path;

    public string $ext;

    public ?array $data;

    private ?string $_rawData;

    public function __construct(string $path, string $ext)
    {
        $this->path = $path;
        $this->ext = $ext;
        $this->data = null;
        $this->_rawData = null;
    }

    public function raw() : string
    {
        if ($this->_rawData !== null) {
            return $this->_rawData;
        }
        if ($this->path === null || $this->data === null || !file_exists($this->path)) {
            return '';
        }
        return $this->_rawData = file_get_contents($this->path);
    }
}