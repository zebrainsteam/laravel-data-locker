# Laravel Data Locker

## Installation

```
composer require zebrains/laravel-data-verificator
```

После того, как пакет установлен, он публикуется автоматически. Теперь нужно применить миграции:
```
php artisan migrate
```
и опубликовать конфигурацию

```
php artisan vendor:publish --provider="Zebrains\LaravelDataLocker\OtpServiceProvider" --tag="config"
```

## Примеры реализации

### Контроллер для получения запроса

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prozorov\DataVerification\Types\Address;

class OtpController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $manager = app('otp');

        $address = new Address('79034106060');

        $otp = $manager->generateAndSend($address, 'sms');

        return response()->json(['code' => $otp->getVerificationCode()]);
    }
}
```

### Контроллер для проверки запроса

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $manager = app('otp');

        $pass = $request->get('pass');
        $code = $request->get('code');

        $otp = $manager->verifyOrFail($code, $pass);
    }
}
```
