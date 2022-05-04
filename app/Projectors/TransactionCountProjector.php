<?php

namespace App\Projectors;

use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;
use App\Models\TransactionCount;

use Spatie\EventSourcing\Models\StoredEvent;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TransactionCountProjector extends Projector
{
    public function onMoneyAdded(MoneyAdded $event)
    {
        $transactionCounter = TransactionCount::firstOrCreate(['account_uuid' => $event->aggregateRootUuid()]);
        $transactionCounter->count += 1;
        $transactionCounter->save();

    }

    public function onMoneySubtracted(MoneySubtracted $event)
    {
        $transactionCounter = TransactionCount::firstOrCreate(['account_uuid' => $event->aggregateRootUuid()]);
        $transactionCounter->count += 1;
        $transactionCounter->save();

    }
}
