<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Prozorov\DataVerification\Exceptions\VerificationException;
use Prozorov\DataVerification\Exceptions\LimitException;

trait HandlesOtpExceptions
{
    /**
     * Register a renderable callback.
     *
     * @param  callable  $renderUsing
     * @return $this
     */
    abstract public function renderable(callable $renderUsing);

    /**
     * Registers errorhandlers for OTP exceptions
     *
     * @access	protected
     * @return	void
     */
    protected function registerOtpExceptionHandlers(): void
    {
        $this->renderable(function (VerificationException $exception, $request) {
            return response()->json([
                'code' => 400,
                'message' => $exception->getMessage(),
            ], 400);
        });

        $this->renderable(function (LimitException $exception, $request) {
            return response()->json([
                'code' => 400,
                'message' => $exception->getMessage(),
            ], 400);
        });
    }
}
