<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Carbon\Carbon;

class MoneyAdded extends ShouldBeStored
{
    public $amount;
    public $added_at;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
        $this->added_at = Carbon::now()->format('Y-m-d H:i:s');
    }


}
