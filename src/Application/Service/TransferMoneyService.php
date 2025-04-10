<?php

namespace App\Application\Service;

use App\Domain\Entity\BankAccount;
use App\Domain\Enum\Currency;
use App\Domain\Enum\Direction;
use App\Domain\Event\EventDispatcherInterface;
use App\Domain\Event\MoneyTransferredEvent;
use App\Domain\Service\BankingServiceInterface;
use App\Domain\ValueObject\Decimal;
use App\Domain\ValueObject\Payment;

readonly class TransferMoneyService
{
    public function __construct(
        private BankingServiceInterface  $bankingService,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function execute(BankAccount $from, BankAccount $to, Decimal $amount, Currency $currency): void
    {
        $debit = new Payment($amount, $currency, Direction::DEBIT);
        $credit = new Payment($amount, $currency, Direction::CREDIT);
        $this->bankingService->apply($from, $debit);
        $this->bankingService->apply($to, $credit);

        $event = new MoneyTransferredEvent($from, $debit);
        $this->dispatcher->dispatch($event);

        $event = new MoneyTransferredEvent($to, $credit);
        $this->dispatcher->dispatch($event);
    }
}