<?php

namespace App\Exceptions;

use Exception;

class CouldNotSubtractMoney extends Exception
{
    public static function notEnoughFunds(int $amount): self
    {
        return new static("No puedes retirar la cantidad de $ {$amount} por que tu cuenta no tiene los fondos suficientes.");
    }

    public static function limitTransactionsPerDayReached(int $amount): self
    {
        return new static("No puedes retirar la cantidad de $ {$amount} por que superas la cantidad de $10,000 por dia.");
    }
}
