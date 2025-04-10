<?php

namespace App\Domain\Entity;

use App\Domain\Enum\Currency;
use App\Domain\ValueObject\Decimal;
use App\Domain\ValueObject\TransactionDate;

class BankAccount
{
    private readonly string $number;
    private Decimal $balance;
    private readonly Currency $currency;
    private int $dailyDebits;
    private ?TransactionDate $lastDebitDate = null;

    public function __construct(
        Currency $currency,
        string   $number,
        Decimal  $balance = new Decimal(0),
        int      $dailyDebits = 0,
    )
    {
        $this->balance = $balance;
        $this->currency = $currency;
        $this->number = $number;
        $this->dailyDebits = $dailyDebits;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getBalance(): Decimal
    {
        return $this->balance;
    }

    public function getDailyDebits(): int
    {
        return $this->dailyDebits;
    }

    public function getLastDebitDate(): ?TransactionDate
    {
        return $this->lastDebitDate;
    }

    public function increaseDailyDebits(): void
    {
        $this->dailyDebits++;
    }

    public function resetDailyDebits(TransactionDate $newDate): void
    {
        $this->dailyDebits = 0;
        $this->lastDebitDate = $newDate;
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