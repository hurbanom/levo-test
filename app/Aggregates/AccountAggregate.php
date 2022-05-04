<?php

namespace App\Aggregates;

use Spatie\EventSourcing\AggregateRoots\AggregateRoot;
use App\Events\AccountCreated;
use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;
use App\Events\AccountLimitHit;
use App\Events\AccountLimitTransactionsPerDayReached;
use App\Events\LoanProposed;
use App\Events\LimitReached;
use App\Exceptions\CouldNotSubtractMoney;
use Carbon\Carbon;

class AccountAggregate extends AggregateRoot
{
    protected int $saldo = 0;
    protected int $accountLimit = 0;
    private $accountLimitHitCount = 0;
    private $amountLimitPerDay = 10000;
    private $amountDay = 0;


    public function createAccount(string $name, string $userId)
    {
        $this->recordThat(new AccountCreated($name, $userId));
        return $this;
    }

    public function addMoney(int $amount)
    {
        $added = $this->recordThat(new MoneyAdded($amount));
        if ($this->limitPerDay($amount)) {
            $events = collect($this->getAppliedEvents());
            $depositos = $events->where('added_at', '>=', Carbon::now()->subHours(48));
            $retiros = $events->where('subtracted_at', '>=', Carbon::now()->subHours(48));

            $transacciones['depositos'] = $depositos;
            $transacciones['retiros'] = $retiros;

            $this->recordThat(new LimitReached($this->uuid(), $transacciones));
        }
        return $this;
    }

    public function subtractAmount(int $amount)
    {
        if (!$this->hasSufficientFundsToSubtractAmount($amount)) {
            $this->recordThat(new AccountLimitHit());
            if ($this->accountLimitHitCount >= 3) {
                $this->recordThat(new LoanProposed());
            }
            $this->persist();
            throw CouldNotSubtractMoney::notEnoughFunds($amount);
        } else if ($this->limitPerDay($amount)) {

            $events = collect($this->getAppliedEvents());
            $depositos = $events->where('added_at', '>=', Carbon::now()->subHours(48));
            $retiros = $events->where('subtracted_at', '>=', Carbon::now()->subHours(48));

            $transacciones['depositos'] = $depositos;
            $transacciones['retiros'] = $retiros;

            $this->recordThat(new AccountLimitTransactionsPerDayReached($amount, 'No puedes retirar la cantidad de: $'.$amount.' ya que superas el limite de transacciones de $10,000 por día'));
            $this->recordThat(new LimitReached($this->uuid(), $transacciones));
            $this->persist();

            throw CouldNotSubtractMoney::limitTransactionsPerDayReached($amount);

        } else {
            $accountLimitHitCount = 0;
            $this->recordThat(new MoneySubtracted($amount));
        }
        return $this;

    }

    public function applyMoneyAdded(MoneyAdded $event)
    {
        $this->saldo += $event->amount;
        if ($event->added_at) {
            if (Carbon::parse($event->added_at)->format('Y-m-d H:i:s') >= Carbon::today()->format('Y-m-d 00:00:00')) {
                $this->amountDay += $event->amount;
            }
        }

    }

    public function applyMoneySubtracted(MoneySubtracted $event)
    {
        $this->saldo -= $event->amount;
        if ($event->subtracted_at) {
            if (Carbon::parse($event->subtracted_at)->format('Y-m-d H:i:s') >= Carbon::today()->format('Y-m-d 00:00:00')) {
                $this->amountDay += $event->amount;
            }
        }
    }

    private function hasSufficientFundsToSubtractAmount(int $amount): bool
    {
        return $this->saldo - $amount >= $this->accountLimit;
    }

    private function limitPerDay(int $amount): bool
    {
        return $this->amountDay + $amount >= $this->amountLimitPerDay;
    }

    public function applyAccountLimitHit()
    {
        $this->accountLimitHitCount++;
    }
}
