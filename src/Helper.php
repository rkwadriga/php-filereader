<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class Helper
{
    public static function getExt(string $file) : ?string
    {
        preg_match("/^.+\.(\w+)$/", $file, $matches);
        return is_array($matches) && isset($matches[1]) ? $matches[1] : null;
    }

    public static function getFastHash(string $file) : string
    {
        $blockSize = 4096;
        $fileSize = is_file($file) ? filesize($file) : strlen($file);
        if ($fileSize > 2 * $blockSize) {
            $hc = hash_init('md5');
            if (is_file($file)) {
                $fp = fopen($file, 'r');
                hash_update($hc, fread($fp, $blockSize));
                hash_update($hc, pack('V', $fileSize)); // uint32 LE
                fseek($fp, 0 - $blockSize, SEEK_END); // last 4096 bytes
                hash_update($hc, fread($fp, $blockSize));
            } else {
                hash_update($hc, substr($file, 0, $blockSize));
                hash_update($hc, substr($file, $fileSize - $blockSize));
            }
            return hash_final($hc);
        } else {
            return is_file($file) ? md5_file($file) : md5($file);
        }
    }

    public static function checkFilePath(string $file, bool $autoCreate = false, ?string $basePath = null) : string
    {
        $file = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
        if (file_exists($file)) {
            return $file;
        }
        if ($basePath !== null) {
            $file = str_replace(['//', '\\\\'], DIRECTORY_SEPARATOR, $basePath . DIRECTORY_SEPARATOR . $file);
        }
        if (!$autoCreate || file_exists($file)) {
            return $file;
        }

        // If file path is not exist - create it
        $filePath = dirname($file);
        if (!is_dir($filePath)) {
            // Get last existed directory permissions
            $directories = array_filter(explode(DIRECTORY_SEPARATOR, $filePath));
            $tmpDir = array_shift($directories);
            if (substr($filePath, 0, 1) === DIRECTORY_SEPARATOR) {
                $tmpDir = DIRECTORY_SEPARATOR . $tmpDir;
            }
            $permissions = null;
            foreach ($directories as $dir) {
                $tmpDir = $tmpDir . DIRECTORY_SEPARATOR . $dir;
                if (is_dir($tmpDir)) {
                    $permissions = fileperms($tmpDir);
                } else {
                    break;
                }
            }
            // Try to create directory recursively
            try {
                if (!mkdir($filePath, $permissions, true)) {
                    throw new \ErrorException('Unknown error');
                }
            } catch (\Exception $e) {
                throw new FRException(sprintf('Can not create the directory "%s": %s', $filePath, $e->getMessage()), FRException::CODE_CREATING_ERROR, $e);
            }
        }

        // Try to create file
        try {
            file_put_contents($file, '');
        } catch (\Exception $e) {
            throw new FRException(sprintf('Can not create the file "%s": %s', $file, $e->getMessage()), FRException::CODE_CREATING_ERROR, $e);
        }

        return $file;
    }

    public static function cropImage(FileEntity $file, int $newWidth, int $newHeight, ?string $newImageFile = null, ?int $currentWidth = null, ?int $currentHeight = null) : void
    {
        if (!file_exists($file->path) && $file->data === null) {
            throw new FRException(sprintf('File "%s" does not exists and file data not given', $file->path), FRException::CODE_PARAMS_ERROR);
        }
        if (!file_exists($file->path)) {
            file_put_contents($file->path, $file->data);
        }

        $imageFile = $file->path;
        $fileExt = $file->ext;
        if ($newImageFile === null) {
            $fileName = basename($imageFile);
            $extWithSizePart = '_' . $newWidth . 'x' . $newHeight . '.' . $fileExt;
            if (!strpos($fileName, $extWithSizePart)) {
                $newImageFile = str_replace($fileName, str_replace('.' . $fileExt, $extWithSizePart, $fileName), $imageFile);
            } else {
                $newImageFile = $imageFile;
            }
        }
        if ($currentWidth === null || $currentHeight === null) {
            list($currentWidth, $currentHeight) = getimagesize($imageFile);
        }
        if ($currentWidth == $newWidth && $currentHeight == $newHeight) {
            if (!file_exists($newImageFile)) {
                copy($imageFile, $newImageFile);
            }
            $file->path = $newImageFile;
            return;
        }

        // Create default params for crop and resize
        $x1 = 0;
        $y1 = 0;
        $x2 = 0;
        $y2 = 0;
        $w1 = $newWidth;
        $h1 = $newHeight;
        $w2 = $currentWidth;
        $h2 = $currentHeight;
        $jpgExtensions = ['jpg', 'jpeg'];
        $pngExtensions = ['png'];

        // Calculate position and size of old image on new (empty) image
        if ($newWidth / $newHeight > $currentWidth / $currentHeight) {
            $w1 = ($newHeight * $currentWidth) / $currentHeight;
            $x1 = ($newWidth - $w1) / 2;
        } else {
            $h1 = ($newWidth * $currentHeight) / $currentWidth;
            $y1 = ($newHeight - $h1) / 2;
        }

        // Create new cropped and resized image
        try {
            $im1 = imagecreatetruecolor($newWidth, $newHeight);
            if (in_array($fileExt, $jpgExtensions)) {
                $im2 = imagecreatefromjpeg($imageFile);
            } elseif (in_array($fileExt, $pngExtensions)) {
                $im2 = imagecreatefrompng($imageFile);
            } else {
                throw new \ErrorException(sprintf('Invalid image extension: %s', $fileExt));
            }

            imagealphablending($im2, true);
            imagecopyresized($im1, $im2, $x1, $y1, $x2, $y2, $w1, $h1, $w2, $h2);

            if (in_array($fileExt, $jpgExtensions)) {
                imagejpeg($im1, $newImageFile);
            } elseif (in_array($fileExt, $pngExtensions)) {
                imagepng($im1, $newImageFile);
            }

            imagedestroy($im2);
            imagedestroy($im1);

            $file->path = $newImageFile;
        } catch (\Exception $e) {
            throw new FRException(sprintf('Can not crop the image "%s" to size "%s": %s', $imageFile, $newWidth . 'x' . $newHeight, $e->getMessage()), FRException::CODE_CONFIG_ERROR);
        }
    }

    public static function trim(string $line, string $trimmers) : string
    {
        return trim($line, " \t\n\r\0\x0B" . $trimmers);
    }
}