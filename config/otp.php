<?php

use Zebrains\LaravelDataLocker\EloquentRepository;
use Prozorov\DataVerification\Transport\DebugTransport;
use Prozorov\DataVerification\Messages\SmsMessage;

$debugCodePath = __DIR__ . '/../debug_otp';

return [
    'code_repository' => EloquentRepository::class,
    'passwords' => [
        'default' => [
            'pass_length' => 4,
            'creation_code_threshold' => 60,
            'limit_per_hour' => 10,
            'attempts' => 3,
            'password_validation_period' => 3600,
            'allowed_symbols' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0],
        ],
    ],
    'transport' => [
        'sms' => function () use ($debugCodePath) {
            return new DebugTransport($debugCodePath);
        },
    ],
    'messages' => [
        'sms' => SmsMessage::class,
    ],
];