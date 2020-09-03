<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class TextReader extends AbstractReader
{
    protected string $sep = "\n";

    public function readFile(?array $requiredAttributes = []) : array
    {
        if (!file_exists($this->file->path)) {
            throw new FRException(sprintf('File %s not found', $this->file->path), FRException::CODE_FILE_NOT_FOUND);
        }
        try {
            $data = file_get_contents($this->file->path);
        } catch (\Exception $e) {
            throw new FRException(sprintf('Can not read the file %s: %s', $this->file->path, $e->getMessage()), FRException::CODE_READING_ERROR, $e);
        }

        $this->file->data = [];
        $strings = explode($this->sep, $data);
        return $this->file->data = array_filter($strings);
    }

    public function convertData(array $data) : string
    {
        $delimiter = $this->sep === "\n" ? $this->sep : $this->sep . "\n";
        return implode($delimiter, $data);
    }
}