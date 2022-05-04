<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Response;
use App\Events\AccountCreated;
use App\Events\MoneyAdded;
use App\Aggregates\AccountAggregate;

class AccounControllerTest extends TestCase
{
    private const ACCOUNT_UUID = '944c86b4-d0e2-4e62-a86c-43fdef8d1d1e';
    private const ACCOUNT_NAME = 'Fake Account';

    public function test_api_crear_cuenta_ok()
    {
        $payload = [
            "nombre" => 'Cosme Fuluanito',
            "email" => 'cosme@larabank.com'
        ];
        $this->json('post', 'api/crearCuenta', $payload)
            ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_api_crear_cuenta_duplicada()
    {
        $payload = [
            "nombre" => 'Cosme Fuluanito',
            "email" => 'cosme@larabank.com'
        ];
        $this->json('post', 'api/crearCuenta', $payload)
            ->assertStatus(Response::HTTP_CREATED);
        $this->json('post', 'api/crearCuenta', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }

    public function test_api_crear_cuenta_nombre_numeros()
    {
        $payload = [
            "nombre" => 'Cosme 123',
            "email" => 'cosme@larabank.com'
        ];
        $this->json('post', 'api/crearCuenta', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'errores' => [
                    'nombre' => [
                        "El campo solo puede contener caracteres válidos: [A-Z] y espacios"
                    ]
                ],
                'message' => "La petición contiene datos enviados incorrectamente"
            ]);

    }

    public function test_api_crear_cuenta_nombre_caracteres_especiales()
    {
        $payload = [
            "nombre" => 'Cosme <>',
            "email" => 'cosme@larabank.com'
        ];
        $this->json('post', 'api/crearCuenta', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'errores' => [
                    'nombre' => [
                        "El campo solo puede contener caracteres válidos: [A-Z] y espacios"
                    ]
                ],
                'message' => "La petición contiene datos enviados incorrectamente"
            ]);

    }

    public function test_api_depositar()
    {

        $payload = [
            "uuid" => $this->account->uuid,
            "monto" => 500
        ];

        $this->json('post', 'api/depositar', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'success' => true,
                'message' => 'Depósito exitoso'
            ]);

    }

    public function test_api_depositar_uuid_incorrecto()
    {

        $payload = [
            "uuid" => '-',
            "monto" => 500
        ];

        $this->json('post', 'api/depositar', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'errores' => [
                    'uuid' => [
                        "El valor uuid es incorrecto."
                    ]
                ],
                'message' => "La petición contiene datos enviados incorrectamente"
            ]);

    }

    public function test_api_depositar_cuenta_no_existente()
    {

        $payload = [
            "uuid" => '844c86b4-d0e2-4e62-a86c-43fdef8d1d1e',
            "monto" => 500
        ];

        $this->json('post', 'api/depositar', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'message' => "No query results for model [App\\Models\\Account]."
            ]);

    }

    public function test_api_retirar()
    {

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneyAdded(1000)
            ]);

        $payload = [
            "uuid" => self::ACCOUNT_UUID,
            "monto" => 500
        ];

        $this->json('post', 'api/retirar', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'success' => true,
                'message' => 'Retiro exitoso'
            ]);

    }

    public function test_api_retirar_fondos_insuficientes()
    {

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
            ]);

        $payload = [
            "uuid" => self::ACCOUNT_UUID,
            "monto" => 500
        ];

        $this->json('post', 'api/retirar', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'message' => 'No puedes retirar la cantidad de $ 500 por que tu cuenta no tiene los fondos suficientes.'
            ]);

    }

    public function test_api_retirar_limite_superado()
    {

        AccountAggregate::fake(self::ACCOUNT_UUID)
            ->given([
                new AccountCreated(self::ACCOUNT_NAME, $this->user->id),
                new MoneyAdded(10000)
            ]);

        $payload = [
            "uuid" => self::ACCOUNT_UUID,
            "monto" => 500
        ];

        $this->json('post', 'api/retirar', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'message' => 'No puedes retirar la cantidad de $ 500 por que superas la cantidad de $10,000 por dia.'
            ]);

    }

    public function test_api_retirar_uuid_incorrecto()
    {

        $payload = [
            "uuid" => '-',
            "monto" => 500
        ];

        $this->json('post', 'api/retirar', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'errores' => [
                    'uuid' => [
                        "El valor uuid es incorrecto."
                    ]
                ],
                'message' => "La petición contiene datos enviados incorrectamente"
            ]);

    }

    public function test_api_retirar_cuenta_no_existente()
    {

        $payload = [
            "uuid" => '844c86b4-d0e2-4e62-a86c-43fdef8d1d1e',
            "monto" => 500
        ];

        $this->json('post', 'api/retirar', $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'success' => false,
                'message' => "No query results for model [App\\Models\\Account]."
            ]);

    }


}
