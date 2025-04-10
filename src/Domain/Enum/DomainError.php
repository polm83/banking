<?php

namespace App\Domain\Enum;

enum DomainError: int
{
    case CURRENCY_MISMATCH = 1001;
    case DAILY_LIMIT_EXCEEDED = 1002;
    case INSUFFICIENT_BALANCE = 1003;
}