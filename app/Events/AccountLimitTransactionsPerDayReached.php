<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AccountLimitTransactionsPerDayReached extends ShouldBeStored
{
    public $amount;
    public $message;

    public function __construct(string $amount, string $message)
    {
        $this->amount = $amount;
        $this->message = $message;
    }

}
