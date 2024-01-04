<?php

namespace Hiimlamxung\EupEncryptApi\App\Facades;

use Illuminate\Support\Facades\Facade;

class EupCrypt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eupencrypter';
    }
}
