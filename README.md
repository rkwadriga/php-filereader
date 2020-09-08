## PHP files reader

## REQUIREMENTS

The minimum requirement by this application template that your Web server supports PHP 7.4.0.

## Install
```bash
$ composer require rkwadriga/php-filereader:dev-master
```

### Supported formats: csv, sql, txt, log, json, yml, yaml

## Usage
```php
use rkwadriga\filereader\Factory;

class MyApp
{
    public function myFunction()
    {
        // Create a Factory instance
        $factory = new Factory();
        // Create file reader (in this case .yml file reader)
        $fileReader = $factory->getReader('./config/main.yml');

        // Read file (method "readFile" returns an associative array)
        $data = $fileReader->read();

        // Write file
        $fileReader->write([
            'var1' => 'Value 1',
            'var2' => 'Value 2',
        ]);
    }
}
```
If you use the same dir for all files you work with, you can put this dir in Factory constructor and use relative files paths:
```php
$factory = new Factory('./files_dir_path');
$fileReader = $factory->getReader('main.yml');
```
The file readers automatically crete file if it's not exist. If you don't want to do this, set second argument of "getReader" method to false:
```php
$fileReader = $factory->getReader('main.yml', false);
```
In this case you will get the "File not found" exception trying to read not existed file.

If you have some specific file extension but that can be read like one of allowed extensions, you can set the "extensions map" of the readers factory:
```php
$factory = new Factory(null, [
    'xjson' => 'json',
    'xtxt' => 'txt',
    ...
]);
```