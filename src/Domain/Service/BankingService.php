<?php

namespace App\Domain\Service;

use App\Domain\Config\BankingConfig;
use App\Domain\Entity\BankAccount;
use App\Domain\Enum\Direction;
use App\Domain\Enum\DomainError;
use App\Domain\Policy\TransferLimitTrackerInterface;
use App\Domain\ValueObject\Decimal;
use App\Domain\ValueObject\Payment;
use App\Domain\ValueObject\TransactionDate;
use DomainException;

readonly class BankingService implements BankingServiceInterface
{
    public function __construct(
        private BankingConfig                 $config,
        private TransferLimitTrackerInterface $limitTracker
    )
    {
    }

    public function apply(BankAccount $account, Payment $payment): void
    {
        $this->checkCurrency($account, $payment);

        if ($payment->getDirection() == Direction::CREDIT) {
            $this->credit($account, $payment);
        } else {
            $this->debit($account, $payment);
        }
    }

    private function checkCurrency(BankAccount $account, Payment $payment): void
    {
        if (!$account->getCurrency()->equals($payment->getCurrency())) {
            throw new DomainException("Currency mismatch.", DomainError::CURRENCY_MISMATCH->value);
        }
    }

    private function credit(BankAccount $account, Payment $payment): void
    {
        $account->increaseBalance($payment->getAmount());
    }

    private function debit(BankAccount $account, Payment $payment): void
    {
        $this->checkDailyDebits($account, $date = new TransactionDate($payment->getDate()));

        $fee = $this->calculateFee($payment->getAmount());
        $total = $payment->addFee($fee);

        if ($total->isGreaterThan($account->getBalance())) {
            throw new DomainException("Insufficient balance.", DomainError::INSUFFICIENT_BALANCE->value);
        }

        $account->decreaseBalance($total);
        $this->limitTracker->increaseDailyDebits($account, $date);
    }

    private function checkDailyDebits(BankAccount $account, TransactionDate $date): void
    {
        if ($this->limitTracker->getDailyDebits($account, $date) >= $this->config->getMaxDailyDebits()) {
            throw new DomainException("Daily debit limit exceeded.", DomainError::DAILY_LIMIT_EXCEEDED->value);
        }
    }

    private function calculateFee(Decimal $amount): Decimal
    {
        return $amount->mul($this->config->getFeePercentage());
    }
}