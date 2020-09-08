<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class CsvReader extends AbstractReader
{
    const SEP_COMMA = ',';
    const SEP_POINT_AND_COMMA = ';';

    protected string $sep = self::SEP_POINT_AND_COMMA;

    public function read(?array $requiredAttributes = []) : array
    {
        if ($this->file->data !== null) {
            return $this->file->data;
        }
        if (!file_exists($this->file->path)) {
            throw new FRException(sprintf('File %s not found', $this->file->path), FRException::CODE_FILE_NOT_FOUND);
        }

        $this->file->data = [];
        $lineNumber = 0;
        $fields = [];
        $fieldsCount = 0;
        $handle = fopen($this->file->path, 'r');

        while (($line = fgets($handle)) !== false) {
            if (empty($line = trim($line))) {
                continue;
            }
            $lineParts = explode($this->sep, $line);
            $lineNumber++;
            if (empty($lineParts)) {
                continue;
            }
            if (empty($fields)) {
                foreach ($requiredAttributes as $attr) {
                    if (!in_array($attr, $lineParts)) {
                        throw new FRException(sprintf('Invalid file %s format: the first string (attributes) must contains all of those attributes: %s',
                            $this->file->path, implode(', ', $requiredAttributes)), FRException::CODE_VALIDATION_ERROR);
                    }
                }
                $fields = $lineParts;
                $fieldsCount = count($fields);
                continue;
            }

            $linePartsCount = count($lineParts);
            if ($linePartsCount !== $fieldsCount) {
                throw new FRException(sprintf('Invalid file %s format: each string must have a %s parts separated by "%s". String %s has a %s parts',
                    $this->file->path, $fieldsCount, $this->sep, $lineNumber, $linePartsCount), FRException::CODE_VALIDATION_ERROR);
            }

            $tmpData = [];
            foreach ($lineParts as $index => $value) {
                $tmpData[$fields[$index]] = trim($value);
            }
            $this->file->data[] = $tmpData;
        }

        fclose($handle);

        return $this->file->data;
    }

    public function convertData(array $data) : string
    {
        if (empty($data)) {
            return '';
        }

        $keys = array_keys(current($data));
        $dataString = implode($this->sep, $keys);

        foreach ($data as $params) {
            foreach ($params as $index => $value) {
                $params[$index] = str_replace($this->sep, ' ', $value);
            }
            $dataString .= sprintf("\n%s", implode($this->sep, array_values($params)));
        }

        return $dataString;
    }
}