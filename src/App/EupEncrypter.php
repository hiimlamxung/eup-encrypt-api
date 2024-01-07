<?php

namespace Hiimlamxung\EupEncryptApi\App;

use Illuminate\Encryption\Encrypter;

class EupEncrypter extends Encrypter
{
    public function encrypt($value, $serialize = false) {
        return parent::encrypt($value, $serialize);
    }

    public function decrypt($payload, $unserialize = false) {
        return parent::decrypt($payload, $unserialize);
    }
}
