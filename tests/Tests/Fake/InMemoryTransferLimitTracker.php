<?php

namespace Tests\Fake;

use App\Domain\Entity\BankAccount;
use App\Domain\Policy\TransferLimitTrackerInterface;
use App\Domain\ValueObject\TransactionDate;

class InMemoryTransferLimitTracker implements TransferLimitTrackerInterface
{
    private array $storage = [];

    public function getDailyDebits(BankAccount $account, TransactionDate $date): int
    {
        $key = spl_object_id($account) . '-' . $date->toString();
        return $this->storage[$key] ?? 0;
    }

    public function increaseDailyDebits(BankAccount $account, TransactionDate $date): void
    {
        $key = spl_object_id($account) . '-' . $date->toString();
        $this->storage[$key] = ($this->storage[$key] ?? 0) + 1;
    }
}
