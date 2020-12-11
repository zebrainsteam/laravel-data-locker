<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Illuminate\Console\Command;
use Carbon\Carbon;

class RemoveExpiredCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes expired one-time-passwords from the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time = Carbon::now();

        $time->subSeconds(config('otp.passwords.default.password_validation_period'));

        OtpCode::where('created_at', '<', $time)->delete();

        return 0;
    }
}
