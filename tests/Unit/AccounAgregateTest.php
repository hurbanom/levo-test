<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Aggregates\AccountAggregate;
use App\Events\AccountCreated;
use App\Events\AccountLimitHit;
use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;
use App\Events\LoanProposed;
use App\Events\LimitReached;
use App\Events\AccountLimitTransactionsPerDayReached;
use App\Exceptions\CouldNotSubtractMoney;
use App\Models\User;
use Carbon\Carbon;
use App;

class AccounAgregateTest extends TestCase
{
    private const ACCOUNT_UUID = '944c86b4-d0e2-4e62-a86c-43fdef8d1d1e';
    private const ACCOUNT_NAME = 'Fake Account';


    public function test_crear_cuenta()
    {
        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([])
            ->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->createAccount(self::ACCOUNT_NAME, $this->user->id);
            })
            ->assertRecorded([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id)
            ]);
    }

    public function test_depositar()
    {

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([new AccountCreated(self::ACCOUNT_NAME, $this->user->id)])
            ->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->addMoney(10);
            })
            ->assertRecorded([
                new MoneyAdded(10)
            ]);
    }

    public function test_retirar()
    {
        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneyAdded(10)
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->subtractAmount(10);
            })
            ->assertRecorded([
                new MoneySubtracted(10),
            ]);

    }


    public function test_no_permitir_retiro_cuando_la_cuenta_se_queda_en_ceros()
    {
        AccountAggregate::fake(self::ACCOUNT_UUID)
           ->given([
               new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
               new MoneySubtracted(5000)
           ])
           ->when(function (AccountAggregate $accountAggregate): void {
               $this->assertExceptionThrown(function () use ($accountAggregate) {
                   $accountAggregate->subtractAmount(1);

               }, CouldNotSubtractMoney::class);
           })
           ->assertApplied([
               new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
               new MoneySubtracted(5000),
               new AccountLimitHit()
           ])
           ->assertNotRecorded(MoneySubtracted::class);

    }

    public function test_ofrecer_prestamo_cuando_intenta_3_retiros_con_saldo_deudor()
    {
        $a = AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneySubtracted(5000),
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $this->assertExceptionThrown(function () use ($accountAggregate) {
                    $accountAggregate->subtractAmount(1);
                }, CouldNotSubtractMoney::class);

                $this->assertExceptionThrown(function () use ($accountAggregate) {
                    $accountAggregate->subtractAmount(1);
                }, CouldNotSubtractMoney::class);

                $this->assertExceptionThrown(function () use ($accountAggregate) {
                    $accountAggregate->subtractAmount(1);
                }, CouldNotSubtractMoney::class);

            })->assertApplied([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneySubtracted(5000),
                new AccountLimitHit(),
                new AccountLimitHit(),
                new AccountLimitHit(),
                new LoanProposed(),
            ]);
    }

    public function test_limite_permitido_por_dia()
    {
        $transaccionesA = [
            'depositos' => collect([
                1 => new MoneyAdded(5000),
                3 => new MoneyAdded(5000),
            ]),
            'retiros' => collect([
                2 => new MoneySubtracted(2000),
            ]),
        ];

        $transaccionesB = [
            'depositos' => collect([
                1 => new MoneyAdded(5000),
                3 => new MoneyAdded(5000),
                6 => new MoneyAdded(1000),
            ]),
            'retiros' => collect([
                2 => new MoneySubtracted(2000),
            ]),
        ];

        $a = AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneyAdded(5000),
                new MoneySubtracted(2000),
                new MoneyAdded(5000),
            ])
            ->when(function (AccountAggregate $accountAggregate): void {
                $this->assertExceptionThrown(function () use ($accountAggregate) {
                    $accountAggregate->subtractAmount(1000);
                }, CouldNotSubtractMoney::class);

            })->when(function (AccountAggregate $accountAggregate): void {
                $accountAggregate->addMoney(1000);
            })->assertApplied([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneyAdded(5000),
                new MoneySubtracted(2000),
                new MoneyAdded(5000),
                new AccountLimitTransactionsPerDayReached(1000, 'No puedes retirar la cantidad de: $1000 ya que superas el limite de transacciones de $10,000 por día'),
                new LimitReached(self::ACCOUNT_UUID, $transaccionesA),
                new MoneyAdded(1000),
                new LimitReached(self::ACCOUNT_UUID, $transaccionesB),
            ]);
    }

}
