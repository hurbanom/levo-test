<?php

namespace App\Projectors;

use App\Models\Account;
use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;
use App\Events\AccountCreated;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AccountSaldoProjector extends Projector
{
    public function onAccountCreated(AccountCreated $event)
    {

        Account::create([
            'uuid' => $event->aggregateRootUuid(),
            'nombre' => $event->nombre,
            'user_id' => $event->userId,
        ]);
    }

    public function onMoneyAdded(MoneyAdded $event)
    {
        $account = Account::uuid($event->aggregateRootUuid());
        $account->saldo += $event->amount;
        $account->save();
    }

    public function onMoneySubtracted(MoneySubtracted $event)
    {
        $account = Account::uuid($event->aggregateRootUuid());
        $account->saldo -= $event->amount;
        $account->save();
    }

}
