<?php

namespace App\Domain\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

readonly class TransactionDate
{
    public DateTimeImmutable $date;

    public function __construct(string|DateTimeImmutable $value)
    {
        if (is_string($value)) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $value);
            if (!$parsed) {
                throw new InvalidArgumentException("Invalid date format. Use YYYY-MM-DD.");
            }
            $this->date = $parsed;
        } else {
            $this->date = $value;
        }
    }

    public function equals(TransactionDate $other): bool
    {
        return $this->date->format('Y-m-d') === $other->date->format('Y-m-d');
    }

    public function toString(): string
    {
        return $this->date->format('Y-m-d');
    }
}