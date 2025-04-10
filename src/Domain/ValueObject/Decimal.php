<?php

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class Decimal
{
    private int $precision = 2;
    private string $value;

    public function __construct(string|float|int $value, int $precision = 2)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException("Value must be numeric.");
        }

        $this->precision = $precision;

        $this->value = number_format((float)$value, $this->precision, '.', '');
    }

    public static function getPrecision(string|float $number): int
    {
        $string = (string)$number;

        if (!str_contains($string, '.')) {
            return 0;
        }

        $parts = explode('.', rtrim($string, '0'));
        return strlen($parts[1] ?? '');
    }

    public function add(Decimal $other): Decimal
    {
        return new Decimal(bcadd($this->value, $other->value, $this->precision));
    }

    public function sub(Decimal $other): Decimal
    {
        return new Decimal(bcsub($this->value, $other->value, $this->precision));
    }

    public function mul(Decimal|string|float $other): Decimal
    {
        $otherValue = $other instanceof Decimal ? $other->value : $other;
        return new Decimal(bcmul($this->value, $otherValue, $this->precision));
    }

    public function div(Decimal $other): Decimal
    {
        return new Decimal(bcdiv($this->value, $other->value, $this->precision));
    }

    public function isGreaterThan(Decimal $other): bool
    {
        return bccomp($this->value, $other->value, $this->precision) === 1;
    }

    public function toFloat(): float
    {
        return (float)$this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}