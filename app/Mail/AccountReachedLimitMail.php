<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AccountReachedLimitMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cuenta;
    public $transacciones;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cuenta, $transacciones)
    {
        $this->cuenta = $cuenta;
        $this->transacciones = $transacciones;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.transacciones')->with([
            'cuenta' => $this->cuenta,
            'transacciones' => $this->transacciones
        ]);
    }
}
