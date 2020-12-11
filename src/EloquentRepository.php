<?php

declare(strict_types=1);

namespace Zebrains\LaravelDataLocker;

use Datetime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Prozorov\DataVerification\Contracts\CodeRepositoryInterface;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;

class EloquentRepository implements CodeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function save(Code $code): Code
    {
        $otpCode = Converter::toEloquent($code);

        if (! $otpCode->save()) {
            throw new \RuntimeException('Unale to save code');
        }

        $code->setId($otpCode->id);

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Code $code): void
    {
        $otpCode = Converter::toEloquent($code);

        if (! $otpCode->delete()) {
            throw new \RuntimeException('Unale to delete code');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOneUnvalidatedByCode(string $code, ?Datetime $createdAfter = null): ?Code
    {
        $query = $this->getBaseQuery(['verification_code' => $code], $createdAfter);

        $otpCode = $query->first();

        if ($otpCode === null) {
            return null;
        }

        return Converter::fromEloquent($otpCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastCodeForAddress(Address $address, ?Datetime $createdAfter = null): ?Code
    {
        $query = $this->getBaseQuery(['address' => $address], $createdAfter);

        $otpCode = $query->first();

        if ($otpCode === null) {
            return null;
        }

        return Converter::fromEloquent($otpCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodesCountForAddress(Address $address, ?Datetime $createdAfter = null): ?int
    {
        $query = $this->getBaseQuery(['address' => $address], $createdAfter);

        return $query->count();
    }

    /**
     * Sets and returns query builder
     *
     * @access protected
     * @param array $filter
     * @param Datetime $createdAfter Default: null
     * @return Builder
     */
    protected function getBaseQuery(array $filter, ?Datetime $createdAfter = null): Builder
    {
        $filter['is_validated'] = 0;

        $query = OtpCode::where($filter)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($createdAfter !== null) {
            $query->whereTime('created_at', '>', $createdAfter);
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function openTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function commitTransaction(): void
    {
        DB::commit();
    }

    /**
     * @inheritDoc
     */
    public function rollbackTransaction(): void
    {
        DB::rollBack();
    }
}