<?php

namespace App\Domain\Config;

use App\Domain\ValueObject\Decimal;

readonly class BankingConfig
{
    public function __construct(
        private Decimal $feePercentage = new Decimal(0),
        private int     $maxDailyDebits = 0,
        private array   $supportedCurrencies = []
    )
    {
    }

    public function getFeePercentage(): Decimal
    {
        return $this->feePercentage;
    }

    public function getMaxDailyDebits(): int
    {
        return $this->maxDailyDebits;
    }

    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }
}