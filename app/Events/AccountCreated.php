<?php

namespace App\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AccountCreated extends ShouldBeStored
{
    public $nombre;
    public $userId;

    public function __construct(string $nombre, int $userId) {
        $this->nombre = $nombre;
        $this->userId = $userId;

    }

}
