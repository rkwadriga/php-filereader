<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class FileEntity
{
    public string $path;

    public string $ext;

    public ?array $data;

    public function __construct(string $path, string $ext)
    {
        $this->path = $path;
        $this->ext = $ext;
        $this->data = null;
    }
}