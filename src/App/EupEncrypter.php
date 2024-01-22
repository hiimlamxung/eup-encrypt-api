<?php

namespace Hiimlamxung\EupEncryptApi\App;

use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\DecryptException;

class EupEncrypter extends Encrypter
{
    protected $key;
    protected $cipher;

    public function encrypt($value, $serialize = false, $randomIv = false) {
        $iv = ($randomIv) ? random_bytes(openssl_cipher_iv_length(strtolower($this->cipher)))
        : base64_decode(config('eup_encrypt_api.encrypt_res.iv'));

        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            strtolower($this->cipher), $this->key, 0, $iv
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $iv = base64_encode($iv);

        $mac = $this->hash($iv, $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    public function simpleEncrypt($value, $serialize = false) {
        $iv = base64_decode(config('eup_encrypt_api.encrypt_res.iv'));

        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            strtolower($this->cipher), $this->key, 0, $iv
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return $value;
    }

    public function decrypt($payload, $unserialize = false) {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
            $payload['value'], strtolower($this->cipher), $this->key, 0, $iv
        );

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    public function simpleDecrypt($encrypted, $unserialize = false) {

        $iv = base64_decode(config('eup_encrypt_api.encrypt_res.iv'));

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
            $encrypted, strtolower($this->cipher), $this->key, 0, $iv
        );

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }
}
