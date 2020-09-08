<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class JsonReader extends AbstractReader
{
    public function read(?array $requiredAttributes = []) : array
    {
        if ($this->file->data !== null) {
            return $this->file->data;
        }
        if (!file_exists($this->file->path)) {
            throw new FRException(sprintf('File %s not found', $this->file->path), FRException::CODE_FILE_NOT_FOUND);
        }
        try {
            $data = file_get_contents($this->file->path);
        } catch (\Exception $e) {
            throw new FRException(sprintf('Can not read the file %s: %s', $this->file->path, $e->getMessage()), FRException::CODE_READING_ERROR, $e);
        }
        $this->file->data = json_decode($data, true);
        if ($this->file->data === null) {
            throw new FRException(sprintf('Invalid json in file %s: (%s) "%s"', $this->file->path, json_last_error(), json_last_error_msg()), FRException::CODE_VALIDATION_ERROR);
        }
        return $this->file->data;
    }

    public function convertData(array $data) : string
    {
        return json_encode($data);
    }
}