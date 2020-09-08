<?php declare(strict_types=1);

namespace rkwadriga\filereader;

class SqlReader extends TxtReader
{
    protected string $sep = ";";
}