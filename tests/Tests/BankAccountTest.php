<?php

namespace Tests;

use App\Domain\Config\BankingConfig;
use App\Domain\Entity\BankAccount;
use App\Domain\Enum\Currency;
use App\Domain\Enum\Direction;
use App\Domain\Enum\DomainError;
use App\Domain\Service\BankingService;
use App\Domain\ValueObject\Decimal;
use App\Domain\ValueObject\Payment;
use DateTimeImmutable;
use DomainException;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\Fake\InMemoryTransferLimitTracker;

class BankAccountTest extends TestCase
{
    const ACCOUNT_NUMBER = "PL79249024160805354346560231";

    private BankingService $service;

    public function testCreditAndDebit()
    {
        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $credit = new Payment(new Decimal(1000), Currency::PLN, Direction::CREDIT);
        $debit = new Payment(new Decimal(100), Currency::PLN, Direction::DEBIT);
        $this->service->apply($account, $credit);
        $this->service->apply($account, $debit);

        $expected = new Decimal(899.50); // 100 + 0.5 = 100.5
        $this->assertEquals($expected, $account->getBalance());
    }

    /**
     * @throws Exception
     */
    public function testCreditAndDebitFewDays()
    {
        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER, new Decimal(1000));

        foreach (['2025-10-03', '2025-10-04', '2025-10-05', '2025-10-06'] as $date) {
            $this->service->apply(
                $account,
                new Payment(
                    new Decimal(100),
                    Currency::PLN,
                    Direction::DEBIT,
                    new DateTimeImmutable($date)
                )
            );
        }

        $expected = new Decimal(598);
        $this->assertEquals($expected, $account->getBalance());
    }

    public function testCurrencyMismatchOnCredit()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::CURRENCY_MISMATCH->value);
        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $credit = new Payment(new Decimal(100), Currency::EUR, Direction::CREDIT);
        $this->service->apply($account, $credit);
    }

    public function testCurrencyMismatchOnDebit()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::CURRENCY_MISMATCH->value);

        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $credit = new Payment(new Decimal(200), Currency::PLN, Direction::CREDIT);
        $debit = new Payment(new Decimal(100), Currency::EUR, Direction::DEBIT);

        $this->service->apply($account, $credit);
        $this->service->apply($account, $debit);
    }

    public function testExceedsDailyLimitOneDayTransfers()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::DAILY_LIMIT_EXCEEDED->value);

        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $credit = new Payment(new Decimal(1000), Currency::PLN, Direction::CREDIT);
        $this->service->apply($account, $credit);

        for ($i = 0; $i < 4; $i++) {
            $debit = new Payment(new Decimal(10), Currency::PLN, Direction::DEBIT);
            $this->service->apply($account, $debit);
        }
    }

    public function testInsufficientBalance()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::INSUFFICIENT_BALANCE->value);
        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $credit = new Payment(new Decimal(50), Currency::PLN, Direction::CREDIT);
        $debit = new Payment(new Decimal(200), Currency::PLN, Direction::DEBIT);

        $this->service->apply($account, $credit);
        $this->service->apply($account, $debit);
    }

    protected function setUp(): void
    {
        $this->service = new BankingService(
            new BankingConfig(
                new Decimal(0.005, 3),
                3,
                [Currency::PLN, Currency::USD, Currency::EUR]
            ),
            new InMemoryTransferLimitTracker());
    }
}