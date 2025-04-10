<?php

namespace Tests;

use App\Domain\Config\ConfigLoader;
use App\Domain\Enum\Currency;
use App\Domain\ValueObject\Decimal;
use PHPUnit\Framework\TestCase;

class ConfigLoaderTest extends TestCase
{
    public function testLoadsValidConfig()
    {
        $path = realpath(__DIR__ . '/../../config/banking_config.json');
        $config = ConfigLoader::fromJsonFile($path);
        $this->assertEquals(new Decimal(0.005, 3), $config->getFeePercentage());
        $this->assertEquals(3, $config->getMaxDailyDebits());
        $this->assertContains(Currency::PLN, $config->getSupportedCurrencies());
    }
}