<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Prozorov\DataVerification\Models\Code;
use RuntimeException;

class Converter
{
    /**
     * Created Code from Eloquent adapter
     *
     * @access public static
     * @param OtpCode $otpCode
     * @return Code
     */
    public static function fromEloquent(OtpCode $otpCode): Code
    {
        return new Code([
            'ID' => $otpCode->id,
            'CREATED_AT' => $otpCode->created_at ?? new DateTime(),
            'VERIFICATION_CODE' => $otpCode->verification_code,
            'PASS' => $otpCode->pass,
            'ATTEMPTS' => (int) $otpCode->attempts,
            'VALIDATED' => $otpCode->is_validated,
            'DATA' => $otpCode->data,
            'ADDRESS_TYPE' => $otpCode->address_type,
            'ADDRESS' => $otpCode->address,
        ]);
    }

    /**
     * Creates Eloquent model adapter from Code
     *
     * @access protected
     * @return OtpCode
     */
    public static function toEloquent(Code $code): OtpCode
    {
        $otpCode = static::getModel($code);

        $otpCode->fill([
            'created_at' => $code->getCreatedAt()->format('d.m.Y H:i:s'),
            'verification_code' => $code->getVerificationCode(),
            'pass' => $code->getOneTimePass(),
            'attempts' => $code->getAttempts(),
            'data' => $code->getVerificationData(),
            'address_type' => (string) $code->getAddressType(),
            'address' => (string) $code->getAddress(),
            'is_validated' => $code->isValidated(),
        ]);

        return $otpCode;
    }

    /**
     * getModel.
     *
     * @access protected static
     * @param Code $code
     * @return OtpCode
     */
    protected static function getModel(Code $code): OtpCode
    {
        if ($code->isNew()) {
            return new OtpCode();
        }

        $otpCode = OtpCode::where(['id' => $code->getId()])->first();

        if ($otpCode === null) {
            throw new RuntimeException('No code with such ID');
        }

        return $otpCode;
    }
}