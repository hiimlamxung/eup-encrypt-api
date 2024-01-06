<?php

namespace Hiimlamxung\EupEncryptApi\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Hiimlamxung\EupEncryptApi\App\RSA;
use Hiimlamxung\EupEncryptApi\Exceptions\CouldNotDecryptData;
use Hiimlamxung\EupEncryptApi\Exceptions\InvalidEncryptedParamName;
use Hiimlamxung\EupEncryptApi\App\Helper;

class CanDecryptRSA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $customEncyptedName = null)
    {
        $encryptedName = $customEncyptedName
        ? $customEncyptedName
        : config('eup_encrypt_api.decrypt_req.encrypted_data_name');

        if (empty($encryptedName)) {
            throw InvalidEncryptedParamName::make();
        }
        $encryptedData = $request->{$encryptedName};
        $encryptedData = base64_decode($encryptedData);

        $decryptData = RSA::decryptByPrivateKey($encryptedData);
        if (is_null($decryptData)) {
            $this->failedResponse($request, $next);
        }
        $request->merge([
            $encryptedName => Helper::objectToArray(json_decode($decryptData))
        ]);
        return $next($request);
    }

    public function failedResponse(Request $request, Closure $next)
    {
        throw CouldNotDecryptData::make();
    }
}
