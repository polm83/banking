<?php

namespace App\Domain\Config;

use App\Domain\ValueObject\Decimal;

class BankingConfig
{
    public function __construct(
        private readonly Decimal $feePercentage = new Decimal(0),
        private readonly int     $maxDailyDebits = 0,
        private readonly array   $supportedCurrencies = []
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