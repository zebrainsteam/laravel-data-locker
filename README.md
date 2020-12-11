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

### Обработка ошибок

В системе есть ряд исключений. Часть из них выбрасываются в случае нарушения лимитов запроса одноразового пароля или в случе некорректной валидации пароля. Такие исключения не должны приводить к 500 ошибкам. Для того, чтобы Laravel корректно обрабатывал такие исключения, нужно дать инструкции в ErrorHandler. В данной библиотеке есть специальный трайт `Zebrains\LaravelDataLocker\HandlesOtpExceptions`, который загружает такие инструкции. Подключите этот трайт в класс `App\Exceptions\Handler` и вызовите метод `registerOtpExceptionHandlers` внутри метода `register`.

Пример:
```
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Zebrains\LaravelDataLocker\HandlesOtpExceptions;
use Throwable;

class Handler extends ExceptionHandler
{
    use HandlesOtpExceptions;

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->registerOtpExceptionHandlers();
    }
}
```

## Система событий

Вы можете изменить поведение библиотеки с помощью системы событий. На данный момент есть событие, вызываемое перед генерацией одноразового пароля. Особенно полезно это может быть для тестирования чтобы менять поведение системы только для определенных адресов.

В качестве примера указана подмена генерируемого кода, если указан определенный номер телефона:

```
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Prozorov\DataVerification\Events\OtpGenerationEvent;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(function (OtpGenerationEvent $event) {
            if ((string) $event->getAddress() === '79181234567') {
                $event->setOtp('1234');
            }
        });
    }
}

```
