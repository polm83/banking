<?php

namespace App\Domain\Policy;

use App\Domain\Entity\BankAccount;
use App\Domain\ValueObject\TransactionDate;

interface TransferLimitTrackerInterface
{
    public function getDailyDebits(BankAccount $account, TransactionDate $date): int;

    public function increaseDailyDebits(BankAccount $account, TransactionDate $date): void;
}
