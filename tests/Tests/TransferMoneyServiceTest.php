<?php

namespace Tests;

use App\Application\Service\TransferMoneyService;
use App\Domain\Config\BankingConfig;
use App\Domain\Entity\BankAccount;
use App\Domain\Enum\Currency;
use App\Domain\Enum\DomainError;
use App\Domain\Event\EventDispatcherInterface;
use App\Domain\Event\MoneyTransferredEvent;
use App\Domain\Service\BankingService;
use App\Domain\ValueObject\Decimal;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\Fake\InMemoryEventDispatcher;
use Tests\Fake\InMemoryTransferLimitTracker;


class TransferMoneyServiceTest extends TestCase
{
    private TransferMoneyService $service;
    private EventDispatcherInterface $dispatcher;

    public function testTransferDispatchesEventAndMovesMoney()
    {
        $from = new BankAccount(Currency::PLN, 'PL20249011879256325718064651', new Decimal(1000));
        $to = new BankAccount(Currency::PLN, 'PL49249029087481755236228677');

        $this->service->execute($from, $to, new Decimal(200), Currency::PLN);

        $this->assertEquals('799.00', (string)$from->getBalance());
        $this->assertEquals('200.00', (string)$to->getBalance());
        $this->assertCount(2, $this->dispatcher->events);

        $this->assertInstanceOf(MoneyTransferredEvent::class, $this->dispatcher->getEvent(0));
        $this->assertEquals($this->dispatcher->getEvent(0)->bankAccount, $from);
        $this->assertEquals(new Decimal(201), $this->dispatcher->getEvent(0)->payment->getAmount());
        $this->assertEquals(new Decimal(1), $this->dispatcher->getEvent(0)->payment->getFee());

        $this->assertInstanceOf(MoneyTransferredEvent::class, $this->dispatcher->getEvent(1));
        $this->assertEquals($this->dispatcher->getEvent(1)->bankAccount, $to);
        $this->assertEquals(new Decimal(200), $this->dispatcher->getEvent(1)->payment->getAmount());

    }

    public function testTransferNoFunds()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::INSUFFICIENT_BALANCE->value);

        $from = new BankAccount(Currency::PLN, 'PL20249011879256325718064651', new Decimal(100));
        $to = new BankAccount(Currency::PLN, 'PL49249029087481755236228677');

        $this->service->execute($from, $to, new Decimal(200), Currency::PLN);
    }

    public function testTransferWrongCurrency()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::CURRENCY_MISMATCH->value);

        $from = new BankAccount(Currency::PLN, 'PL20249011879256325718064651', new Decimal(100));
        $to = new BankAccount(Currency::PLN, 'PL49249029087481755236228677');

        $this->service->execute($from, $to, new Decimal(200), Currency::EUR);
    }

    public function testTransferLimitExceeded()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionCode(DomainError::DAILY_LIMIT_EXCEEDED->value);

        $from = new BankAccount(Currency::PLN, 'PL20249011879256325718064651', new Decimal(1000));
        $to = new BankAccount(Currency::PLN, 'PL49249029087481755236228677');

        for ($i = 0; $i < 4; $i++) {
            $this->service->execute($from, $to, new Decimal(200), Currency::PLN);
        }
    }

    protected function setUp(): void
    {
        $banking = new BankingService(
            new BankingConfig(
                new Decimal(0.005, 3),
                3,
                [Currency::PLN, Currency::USD, Currency::EUR]
            ),
            new InMemoryTransferLimitTracker()
        );
        $this->dispatcher = new InMemoryEventDispatcher();
        $this->service = new TransferMoneyService($banking, $this->dispatcher);
    }
}