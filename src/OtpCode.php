<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    /**
     * @var array<string> $fillable
     */
    protected $fillable = [
        'created_at',
        'verification_code',
        'pass',
        'attempts',
        'data',
        'address_type',
        'address',
        'is_validated',
    ];

    /**
     * @var array<string>
     */
    protected $casts = [
        'data' => 'array',
    ];
}
