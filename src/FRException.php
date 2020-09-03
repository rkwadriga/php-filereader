<?php declare(strict_types=1);

namespace rkwadriga\filereader;

use \Exception;

class FRException extends Exception
{
    const CODE_CONFIG_ERROR = 2001;
    const CODE_PARAMS_ERROR = 2002;
    const CODE_INVALID_EXTENSION = 2003;
    const CODE_FILE_NOT_FOUND = 2004;
    const CODE_CREATING_ERROR = 2005;
    const CODE_INVALID_PATH = 2006;
    const CODE_VALIDATION_ERROR = 2007;
    const CODE_WRITING_ERROR = 2008;
    const CODE_READING_ERROR = 2009;
    const CODE_UNKNOWN_ERROR = 2015;

    public string $name = 'File reader Exception';
}