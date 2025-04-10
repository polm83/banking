<?php

namespace App\Domain\Service;

use App\Domain\Entity\BankAccount;
use App\Domain\ValueObject\Payment;

interface BankingServiceInterface
{
    public function apply(BankAccount $account, Payment $payment): void;
}