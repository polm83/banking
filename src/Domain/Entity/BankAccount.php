<?php

namespace App\Domain\Entity;

use App\Domain\Enum\Currency;
use App\Domain\ValueObject\Decimal;

class BankAccount
{
    private readonly string $number;
    private Decimal $balance;
    private readonly Currency $currency;

    public function __construct(
        Currency $currency,
        string   $number,
        Decimal  $balance = new Decimal(0)
    )
    {
        $this->balance = $balance;
        $this->currency = $currency;
        $this->number = $number;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getBalance(): Decimal
    {
        return $this->balance;
    }

    public function increaseBalance(Decimal $amount): void
    {
        $this->balance = $this->balance->add($amount);
    }

    public function decreaseBalance(Decimal $amount): void
    {
        $this->balance = $this->balance->sub($amount);
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}