<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use App\Aggregates\AccountAggregate;
use Illuminate\Support\Str;

use Egulias\EmailValidator\EmailValidator;
use Validator;

class AccountController extends Controller
{
    function crearCuenta(Request $request) {

        $validaciones = $this->validaciones($request->toArray(), 'crear-cuenta');
        if ($validaciones->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => 'La petición contiene datos enviados incorrectamente',
                'errores'   => $validaciones->errors()
            ], 403);
        }

        $user = User::create([
            'email'     => $request->email,
            'name'      => $request->nombre,
            'password'  => bcrypt(Str::random(8))
        ]);

        $newUuid = Str::uuid()->toString();

        AccountAggregate::retrieve($newUuid)
           ->createAccount($request->nombre, $user->id)
           ->persist();

        return response()->json([
            'success' => true,
            'uuid'    => $newUuid,
            'message' => 'Cuenta creada exitosamente'
        ], 201);

    }


    function depositar(Request $request) {

        try {

            $validaciones = $this->validaciones($request->toArray(), 'deposito');
            if ($validaciones->fails()) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'La petición contiene datos enviados incorrectamente',
                    'errores'   => $validaciones->errors()
                ], 403);
            }

            $account = Account::where(['uuid' => $request->uuid])->firstOrFail();

            $deposito = AccountAggregate::retrieve($account->uuid)
                ->addMoney($request->monto)
                ->persist();

                return response()->json([
                    'success' => true,
                    'message' => 'Depósito exitoso'
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 403);
        }

    }

    function retirar(Request $request) {

        try {

            $validaciones = $this->validaciones($request->toArray(), 'retiro');
            if ($validaciones->fails()) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'La petición contiene datos enviados incorrectamente',
                    'errores'   => $validaciones->errors()
                ], 403);
            }

            $account = Account::where(['uuid' => $request->uuid])->firstOrFail();

            $retiro = AccountAggregate::retrieve($account->uuid)
                ->subtractAmount($request->monto)
                ->persist();

                return response()->json([
                    'success' => true,
                    'message' => 'Retiro exitoso'
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 403);
        }

    }

    function validaciones($datos, $tipo) {

        $mensajes = [
            'required'                  => 'El campo es obligatorio.',
            'email'                     => 'El email no es válido.',
            'unique'                    => 'El email ya esta registrado',
            'numeric'                   => 'El campo debe contener solo números.',
            'uuid'                      => 'El valor uuid es incorrecto.',
            'alpha_spaces_not_html'     => 'El campo solo puede contener caracteres válidos: [A-Z] y espacios',
        ];

        $reglas = [];
        switch ($tipo) {
            case 'crear-cuenta':
                $reglas = [
                    'nombre'    => 'required|alpha_spaces_not_html',
                    'email'     => 'required|email|unique:App\Models\User,email',
                ];
            break;
            case 'deposito':
            case 'retiro':
                $reglas = [
                    'uuid'  => 'uuid|required',
                    'monto' => 'numeric|required',
                ];
            break;
        }

        $validaciones = Validator::make($datos, $reglas, $mensajes);
        return $validaciones;

    }

}
