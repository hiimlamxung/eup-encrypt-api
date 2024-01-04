<?php

namespace Hiimlamxung\EupEncryptApi\Exceptions;

use Exception;

class MissingKeyForEncrypt extends Exception
{
    public static function make()
    {
        return new self("No encryption key has been specified");
    }
}
