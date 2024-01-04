<?php
namespace Hiimlamxung\EupEncryptApi\App;

class Helper {
    public static function objectToArray(object $obj)
    {
        return json_decode(json_encode($obj), true);
    }

    public static function arrayToObject(array $array)
    {
        return json_decode(json_encode($array));
    }
}
