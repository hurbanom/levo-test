<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Events\AccountCreated;
use App\Events\MoneyAdded;
use App\Events\MoneySubtracted;

use Ramsey\Uuid\Uuid;

class Account extends Model
{
    protected $guarded = [];

    public static function uuid(string $uuid): ?Account
    {
        return static::where('uuid', $uuid)->first();
    }

}
