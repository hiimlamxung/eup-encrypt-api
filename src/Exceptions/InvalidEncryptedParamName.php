<?php

namespace Hiimlamxung\EupEncryptApi\Exceptions;

use Exception;

class InvalidEncryptedParamName extends Exception
{
    public static function make()
    {
        return new self("The encrypted parameter name is invalid");
    }
}
