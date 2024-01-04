<?php

namespace Hiimlamxung\EupEncryptApi;

use Illuminate\Support\ServiceProvider;
use Hiimlamxung\EupEncryptApi\App\EupEncrypter;
use Hiimlamxung\EupEncryptApi\Exceptions\MissingKeyForEncrypt;

class EupEncryptApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('eupencrypter', function () {
            return new EupEncrypter($this->parseKey(), config('eup_encrypt_api.encrypt_res.cipher'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // publish file config
        $this->publishes([
            __DIR__.'/Config/eup_encrypt_api.php' => config_path('eup_encrypt_api.php'),
        ]);
    }


    protected function parseKey()
    {
        $key = config('eup_encrypt_api.encrypt_res.key');
        if (empty($key)) {
            throw MissingKeyForEncrypt::make();
        }
        return base64_decode(str_replace('base64:', '', $key));
    }

    /**
     * Configure Serializable Closure signing for security.
     *
     * @return void
     */
    protected function registerSerializableClosureSecurityKey()
    {
        $key = config('eup_encrypt_api.encrypt_res.key');
        if (! class_exists(\Laravel\SerializableClosure\SerializableClosure::class) || empty($key)) {
            return;
        }

        \Laravel\SerializableClosure\SerializableClosure::setSecretKey($this->parseKey());
    }
}
