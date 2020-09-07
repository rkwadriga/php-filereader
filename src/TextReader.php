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

        $sep = $this->convertSep();
        $this->file->data = [];
        $strings = explode($sep, $data);
        return $this->file->data = array_filter($strings);
    }

    public function convertData(array $data) : string
    {
        $sep = $this->convertSep();
        $result = '';
        foreach ($data as $string) {
            if (is_array($string)) {
                $result .= $this->convertData($string) . $sep;
            } else {
                $result .= $string . $sep;
            }
        }
        return substr($result, 0, -strlen($sep));
    }

    private function convertSep() : string
    {
        return $this->sep === "\n" ? $this->sep : $this->sep . "\n";
    }
}