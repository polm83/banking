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
use App\Domain\ValueObject\TransactionDate;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\Fake\InMemoryTransferLimitTracker;

class BankingServiceTest extends TestCase
{
    const ACCOUNT_NUMBER = "PL79249024160805354346560231";

    private BankingService $service;
    private InMemoryTransferLimitTracker $tracker;
    private BankingConfig $config;

    public function testCreditIncreasesBalance()
    {
        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $payment = new Payment(new Decimal(100), Currency::PLN, Direction::CREDIT, new DateTimeImmutable('2025-10-01'));

        $this->service->apply($account, $payment);

        $this->assertEquals(new Decimal('100.00'), $account->getBalance());
    }

    public function testDebitSubtractsBalanceAndAddsFee()
    {
        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER, new Decimal(200));
        $date = new DateTimeImmutable();

        $payment = new Payment(new Decimal(100), Currency::PLN, Direction::DEBIT, $date);
        $this->service->apply($account, $payment);

        $this->assertEquals(new Decimal('99.5'), $account->getBalance());
        $this->assertEquals(1, $this->tracker->getDailyDebits($account, new TransactionDate($date)));
    }

    public function testCurrencyMismatchThrows()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::CURRENCY_MISMATCH->value);

        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $payment = new Payment(new Decimal(100), Currency::EUR, Direction::CREDIT, new DateTimeImmutable());

        $this->service->apply($account, $payment);
    }

    public function testExceedsDailyLimitThrows()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::DAILY_LIMIT_EXCEEDED->value);

        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER);
        $account->increaseBalance(new Decimal(500));

        for ($i = 0; $i < 4; $i++) {
            $this->service->apply(
                $account,
                new Payment(new Decimal(10), Currency::PLN, Direction::DEBIT, new DateTimeImmutable('2025-10-01'))
            );
        }
    }

    public function testInsufficientBalanceThrows()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::INSUFFICIENT_BALANCE->value);

        $account = new BankAccount(Currency::PLN, self::ACCOUNT_NUMBER, new Decimal(50));
        $payment = new Payment(new Decimal(100), Currency::PLN, Direction::DEBIT, new DateTimeImmutable('2025-10-01'));

        $this->service->apply($account, $payment);
    }

    protected function setUp(): void
    {
        $this->tracker = new InMemoryTransferLimitTracker();
        $this->config = new BankingConfig(feePercentage: new Decimal(0.005, 3), maxDailyDebits: 3);
        $this->service = new BankingService($this->config, $this->tracker);
    }
}
