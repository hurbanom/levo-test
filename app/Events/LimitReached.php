<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Carbon\Carbon;

class LimitReached extends ShouldBeStored
{

    public $account;
    public $transacciones;

    public function __construct(string $account, $transacciones = null)
    {
        $this->account = $account;
        $this->transacciones = $transacciones;

        //dd($transacciones);
    }

}
