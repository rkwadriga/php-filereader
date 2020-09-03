<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class YmlReader extends AbstractReader
{
    public function readFile(?array $requiredAttributes = []): array
    {
        if ($this->file->data !== null) {
            return $this->file->data;
        }
        if (!file_exists($this->file->path)) {
            throw new FRException(sprintf('File %s not found', $this->file->path), FRException::CODE_FILE_NOT_FOUND);
        }

        $this->file->data = [];
        $lineNumber = 0;
        $levelsNames = [];
        $fo = fopen($this->file->path, 'r');
        if ($fo === false) {
            throw new FRException(sprintf('Can not read the file %s: %s', $this->file->path, error_get_last()), FRException::CODE_READING_ERROR);
        }

        while ($string = fgets($fo)) {
            $lineNumber++;
            $extracted = explode(':', $string);
            $extractedCount = count($extracted);
            $name = $value = null;
            if ($extractedCount == 1) {
                if ($name === null) {
                    $name = trim($extracted[0]);
                } else {
                    $value = trim($extracted[0]);
                }
            } elseif ($extractedCount == 2) {
                $name = trim($extracted[0]);
                $value = trim($extracted[1]);
            } else {
                fclose($fo);
                throw new FRException(sprintf('Can not parse file %s: line %s contains more than 1 ":" char', $this->file->path, $lineNumber), FRException::CODE_VALIDATION_ERROR);
            }

            $level = $this->getSpacesCount($string) / 2;
            if ($value === '') {
                $levelsNames[$level] = $name;
                continue;
            }
            if (count($levelsNames) > $level) {
                array_splice($levelsNames, $level);
            }

            $this->addValueRecursive($this->file->data, $levelsNames, $name, $value);
        }
        fclose($fo);

        return $this->file->data;
    }

    public function convertData(array $data): string
    {
        // TODO: Implement convertData() method.
        return '';
    }

    private function getSpacesCount(string $string): int
    {
        $count = 0;
        if (strlen(trim($string)) === 0) {
            return $count;
        }
        while (trim(substr($string, $count, 1)) === '') {
            $count++;
        }
        return $count;
    }

    private function addValueRecursive(array &$array, array $names, string $name, string $value) : array
    {
        if (empty($names)) {
            return array_merge($array, [$name => $value]);
        }
        $firstName = array_shift($names);
        if (!isset($array[$firstName])) {
            $array[$firstName] = [];
        }
        $array[$firstName] = $this->addValueRecursive($array[$firstName], $names, $name, $value);

        return $array;
    }
}