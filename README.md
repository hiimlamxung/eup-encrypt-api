# eup-encrypt-api
### 1. Mô tả:
- Sử dụng mã hoá AES để encrypt data trả dữ liệu cho API
- Decrypt requesrt từ phía client đã được encrypt mã hoá RSA
### 2. Yêu cầu:
Sử dụng trên PHP Laravel framework version 5.6 trở lên.
### 3. Cài đặt package:
Run: `composer require hiimlamxung/eup-encrypt-api`

Thêm provider vào trong config:


    $providers = [
            ...
          Hiimlamxung\EupEncryptApi\EupEncryptApiServiceProvider::class
    ],
    
Publish file config:
run `php artisan vendor:publish --provider="Hiimlamxung\EupEncryptApi\EupEncryptApiServiceProvider"`

### 4. Sử dụng:
#### 4.1. Sử dụng mã hoá AES để encrypt response API
##### Thiết lập đầu tiên:
Trước khi sử dụng cần thiết lập theo các bước sau:
+ Sử dụng 1 mã secret key riêng, không dùng đến biến APP_KEY mặc định của Laravel:
    Tạo 1 key mới: run `php artisan key:generate --show`
+ Thay thế key mới tạo vào config eup_encrypt_api.php
    
        <?php
        return [
            'encrypt_res' => [
                'key' => env('EEA_KEY_FOR_ENCRYPT'), // Thay thế key mới tạo vào đây
                'cipher' => 'AES-256-CBC'
            ],
+ run `php artisan config:cache`

##### Sử dụng:
Encypt data cần mã hoã (dạng chuỗi) trả về response API.
Sử dụng EupCrypt Facede:
`use \Hiimlamxung\EupEncryptApi\App\Facades\EupCrypt;`

    $users = User::get();
    $jsonData = json_encode($users); //Chuyển data sang dạng chuỗi json
    $encryptedData = EupCrypt::encrypt($jsonData); //Mã hoá data
    
    return response()->json[
        'code' => 200,
        'data' => $encryptedData
    ]
    
    
#### 4.2 Sử dụng mã hoá RSA để decrypt request từ phía client
##### Thiết lập đầu tiên:
Lưu privateKey và publicKey vào 2 file khác nhau. Nếu bạn chưa có sẵn key, chạy  đoạn  code sau để tự tạo và lấy nội dung key:

    \Hiimlamxung\EupEncryptApi\App\RSA::createKey(); // tạo mới và trả về nội dung của publicKey, privateKey
    
Bổ sung đường dẫn key file vào trong config:

    'decrypt_req' => [
        /**
         * ----------------------------------------------------------------
         * Đường dẫn file key tính từ thư mục gốc
         * ----------------------------------------------------------------
         */
        'path_key' => [
            'public_key' => '/env/publicKey.txt',
            'private_key' => '/env/privateKey.txt',
        ]
    ]
##### Sử dụng:
Tạo 1 middleware mới (Tên theo ý muốn) để áp dụng cho các route cần decrypt. Middleware này cần kế thừa lại  middleware của package  `\Hiimlamxung\EupEncryptApi\App\Http\Middleware\CanDecryptRSA`

run `php artisan make:middleware CanDecryptRSA`

Middleware mới tạo sẽ như sau:

    <?php

    namespace App\Http\Middleware;
    
    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Hiimlamxung\EupEncryptApi\App\Http\Middleware\CanDecryptRSA as EupCanDecryptRSA;

    class CanDecryptRSA extends EupCanDecryptRSA
    {
    
        /**
        * Xoá đi method handle, hoặc comment lại, không dùng nữa (Bắt buộc).
        */ 
        // public function handle(Request $request, Closure $next): Response
    
        // }
    
        public function failedResponse(Request $request, Closure $next)
        {
            // Logic xử lý của bạn khi không thể decrypt được data ở đây.
            return response()->json[
                'code' => 422,
                'message' => 'Could not decrypt the data'
            ]
        }
    }
Middleware này sẽ check và giải mã request có param name là 'encrypted_data' từ phía client gửi lên. Nếu muốn sử dụng param name khác có thể  định nhĩa lại trong config

        'decrypt_req' => [
        /**
         * ----------------------------------------------------------------
         * Tên trường dữ liệu đã được mã hoá do client gửi lên
         * ----------------------------------------------------------------
         */
        'encrypted_data_name' => 'encrypted_data',
        
Done! Việc còn lại là đăng ký middleware vào Kernel.php và áp dụng cho route muốn dùng

        protected $middlewareAliases = [
        ...
        'can.decrypt.rsa' => \App\Http\Middleware\CanDecryptRSA::class
    ];
Sử dụng trong route:

    Route::post('/update', 'UserController@update')->middleware('can.decrypt.rsa');
    
    // Có thể tự định nghĩa param name riêng biệt muốn check ,thay vì là 'encrypted_data' được định nghĩa ở config. Ở đây t lấy theo param name là encryt_param
    Route::post('/store', 'UserController@store')->middleware('can.decrypt.rsa:encryt_param');
    
DONE. Nếu thấy bug thì contact ngay lamhv@eupgroup.net =))
Chúc bạn may mắn và làm thành công =))
