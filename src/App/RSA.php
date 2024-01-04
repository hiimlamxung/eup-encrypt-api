<?php

namespace Hiimlamxung\EupEncryptApi\App;

use Hiimlamxung\EupEncryptApi\Exceptions\InvalidBits;

class RSA {
    public static function createKey() {
        $bits = config('eup_encrypt_api.decrypt_req.bits');
        if (!in_array($bits, [1024, 2048, 3072, 4096])) {
            throw InvalidBits::make();
        }

        $config = [
            "digest_alg" => "sha512",
            "private_key_bits" => $bits,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $publicKey = openssl_pkey_get_details($res)["key"];
        return [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey
        ];
    }

    public static function getPublicKey() {
        return file_get_contents(base_path(config('eup_encrypt_api.decrypt_req.path_key.public_key')));
    }

    public static function getPrivateKey() {
        return file_get_contents(base_path(config('eup_encrypt_api.decrypt_req.path_key.private_key')));
    }

    public static function encryptFromPublicKey (string $data) {
        $publicKey = self::getPublicKey();
        openssl_public_encrypt($data, $encryptedData, $publicKey);
        return $encryptedData;
    }

    public static function decryptByPrivateKey (string $encryptedData) {
        $privateKey = self::getPrivateKey();
        openssl_private_decrypt($encryptedData, $decryptedData, $privateKey);
        return $decryptedData;
    }
}
