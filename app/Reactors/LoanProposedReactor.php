<?php

namespace App\Reactors;

use App\Models\Account;
use App\Models\User;
use App\Events\LoanProposed;
use App\Mail\LoanProposalMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class LoanProposedReactor extends Reactor implements ShouldQueue
{
    public function __invoke(LoanProposed $event)
    {
        $account = Account::where('uuid', $event->aggregateRootUuid())->first();
        $user = User::where('id', $account->user_id)->first();
        Mail::to($user->email)->send(new LoanProposalMail($user->name));
    }

}
