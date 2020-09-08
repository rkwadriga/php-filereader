<?php declare(strict_types=1);

namespace rkwadriga\filereader;

abstract class AbstractReader
{
    /** @var FileEntity */
    protected FileEntity $file;
    protected string $sep;

    public function __construct(string $file, string $ext)
    {
        $this->file = new FileEntity($file, $ext);
    }

    public function getFile() : FileEntity
    {
        return $this->file;
    }

    public function write(array $data) : void
    {
        try {
            $this->file->data = $data;
            file_put_contents($this->file->path, $this->convertData($data));
        } catch (\Exception $e) {
            if ($e instanceof FRException) {
                throw $e;
            }
            throw new FRException(sprintf('Can not write the file %s: %s', $this->file->path, $e->getMessage()), FRException::CODE_WRITING_ERROR, $e);
        }
    }

    public function setSeparator(string $sep) : AbstractReader
    {
        $this->sep = $sep;
        return $this;
    }

    abstract public function read(?array $requiredAttributes = []) : array;

    abstract public function convertData(array $data) : string;
}