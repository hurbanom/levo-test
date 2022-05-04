<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Carbon\Carbon;

class MoneySubtracted extends ShouldBeStored
{
    public $amount;
    public $subtracted_at;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
        $this->subtracted_at = Carbon::now()->format('Y-m-d H:i:s');
    }
}
