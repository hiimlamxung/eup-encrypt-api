<?php

namespace Hiimlamxung\EupEncryptApi\Exceptions;

use Exception;

class CouldNotDecryptData extends Exception
{
    public static function make()
    {
        return new self("Could not decrypt the data.");
    }
}
