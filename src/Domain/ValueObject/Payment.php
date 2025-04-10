<?php

namespace App\Domain\ValueObject;

use App\Domain\Enum\Currency;
use App\Domain\Enum\Direction;
use DateTimeImmutable;
use InvalidArgumentException;

class Payment
{
    private Decimal $amount;
    private Currency $currency;
    private Direction $direction;
    private DateTimeImmutable $date;
    private Decimal $fee;

    public function __construct(
        Decimal           $amount,
        Currency          $currency,
        Direction         $direction,
        DateTimeImmutable $date = new DateTimeImmutable()
    )
    {
        if ($amount->toFloat() <= 0) {
            throw new InvalidArgumentException("Amount must be greater than 0.");
        }

        $this->amount = $amount;
        $this->currency = $currency;
        $this->direction = $direction;
        $this->date = $date;
    }

    public function addFee(Decimal $fee): Decimal
    {
        $this->fee = $fee;
        return $this->amount = $this->amount->add($fee);
    }

    public function getAmount(): Decimal
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getFee(): Decimal
    {
        return $this->fee;
    }
}