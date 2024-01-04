<?php

namespace Hiimlamxung\EupEncryptApi\Exceptions;

use Exception;

class InvalidBits extends Exception
{
    public static function make()
    {
        return new self("Generating an RSA key with the given number of bits failed");
    }
}
