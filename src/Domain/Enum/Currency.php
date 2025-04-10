<?php

namespace App\Domain\Enum;

use App\Domain\Config\BankingConfig;

enum Currency: string
{
    case PLN = 'PLN';
    case EUR = 'EUR';
    case USD = 'USD';

    public static function fromCode(string $code, BankingConfig $config): Currency
    {
        if (!self::isValid($code, $config)) {
            throw new \InvalidArgumentException("Unsupported currency: $code");
        }

        return Currency::from($code);
    }

    public static function isValid(string $code, BankingConfig $config): bool
    {
        return in_array($code, $config->getSupportedCurrencies(), true);
    }

    public function equals(Currency $other): bool
    {
        return $this === $other;
    }
}