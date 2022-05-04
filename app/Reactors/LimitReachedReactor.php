<?php

namespace App\Reactors;

use App\Events\LimitReached;
use App\Mail\AccountReachedLimitMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class LimitReachedReactor extends Reactor implements ShouldQueue
{
    public function __invoke(LimitReached $event)
    {
        Mail::to('director@larabank.com')->send(new AccountReachedLimitMail($event->account, $event->transacciones));
    }
}
