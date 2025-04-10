<?php

namespace App\Domain\Enum;


enum Direction: string
{
    case CREDIT = 'CREDIT';
    case DEBIT = 'DEBIT';
}