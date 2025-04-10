<?php

namespace App\Domain\Config;

use App\Domain\Enum\Currency;
use App\Domain\ValueObject\Decimal;
use RuntimeException;

class ConfigLoader
{
    public static function fromJsonFile(string $path): BankingConfig
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Configuration file not found: $path");
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            throw new RuntimeException("Invalid configuration file format.");
        }

        return new BankingConfig(
            feePercentage: new Decimal($data['fee_percentage'], Decimal::getPrecision($data['fee_percentage'])) ?? 0.000,
            maxDailyDebits: $data['max_daily_debits'] ?? 0,
            supportedCurrencies: array_map(
                fn(string $code) => Currency::from($code),
                $data['supported_currencies'] ?? []
            )
        );
    }
}