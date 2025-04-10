<?php

namespace App\Domain\Event;

use App\Domain\Entity\BankAccount;
use App\Domain\ValueObject\Payment;

readonly class MoneyTransferredEvent implements EventInterface
{
    public function __construct(
        public BankAccount $bankAccount,
        public Payment     $payment
    )
    {
    }
}