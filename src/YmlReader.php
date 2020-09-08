<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class YmlReader extends AbstractReader
{
    public function read(?array $requiredAttributes = []): array
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
        $convertedData = $this->convertDataRecursive($data);
        if (substr($convertedData, -1) === "\n") {
            $convertedData = substr($convertedData, 0, -1);
        }
        return $convertedData;
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
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            } elseif ($value === 'null') {
                $value = null;
            }
            return array_merge($array, [$name => $value]);
        }
        $firstName = array_shift($names);
        if (!isset($array[$firstName])) {
            $array[$firstName] = [];
        }
        $array[$firstName] = $this->addValueRecursive($array[$firstName], $names, $name, $value);

        return $array;
    }

    private function convertDataRecursive(array $data, ?string $name = null, ?string $value = null, int $level = -1) : string
    {
        $converted = '';
        if ($name !== null) {
            $converted .= str_repeat(' ', $level * 2) . $name . ':';
        }
        if ($value !== null) {
            $converted .= " {$value}";
        }

        if ($level >= 0) {
            $converted .= "\n";
        }

        foreach ($data as $dataName => $dataValue) {
            if (is_array($dataValue)) {
                $newLevelData = $dataValue;
                $newLevelValue = null;
            } else {
                if ($dataValue === true) {
                    $dataValue = 'true';
                } elseif ($dataValue === false) {
                    $dataValue = 'false';
                } elseif ($dataValue === null) {
                    $dataValue = 'null';
                }
                $newLevelData = [];
                $newLevelValue = $dataValue;
            }
            $converted .= $this->convertDataRecursive($newLevelData, $dataName, $newLevelValue, $level + 1);
        }

        return $converted;
    }
}