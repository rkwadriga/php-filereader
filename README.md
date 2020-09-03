## PHP files reader

## REQUIREMENTS

The minimum requirement by this application template that your Web server supports PHP 7.4.0.

## Install
```bash
$ composer require rkwadriga/php-filereader:dev-master
```

### Supported formats: csv, sql, txt, log, json, yml, yaml
* For now  for formats "yml" and "yaml" only in read mode available

## Usage
```php
use rkwadriga\filereader\Factory;

class MyApp
{
    public function __construct()
    {
        // Create files reader (in this case .yml files reader)
        $fileReader = (new Factory())->getReader('./config/main.yml');
        ...
        // Read file
        $data = $fileReader->readFile();
        ...
        // Write file
        $fileReader->writeData([
            'var1' => 'Value 1',
            'var2' => 'Value 2',
        ]);
        ...
    }
}
```
If you use the same dir for all files you work with, you can put this dir in Factory constructor and use relative files paths:
```php
use rkwadriga\filereader\Factory;

class MyApp
{
    private Factory $factory;

    public function __construct()
    {
        $this->factory = new Factory('./files_dir');
        $fileReader = $this->factory->getReader('main.yml');
        ...
    }
}
```
The file readers automatically crete file if it's not exist. If you don't want to do this, set second argument of "getReader" method to false:
```php
$fileReader = $this->factory->getReader('main.yml', false);
```
In this case you will get the "File not found" exception trying to read not existed file.