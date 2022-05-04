<?php

namespace App\Models;

use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomStoredEvent extends EloquentStoredEvent
{
    public static function boot()
    {
        parent::boot();

        static::creating(function(CustomStoredEvent $storedEvent) {

            switch ($storedEvent->originalEvent) {
                case Str::contains($storedEvent, 'MoneySubtracted'):
                    $type_date = 'subtracted_at';
                    break;
                case Str::contains($storedEvent, 'MoneyAdded'):
                    $type_date = 'added_at';
                    break;
                default:
                    $type_date = 'created_at';
                    break;
            }

            $storedEvent->meta_data[$type_date] = Carbon::now()->format('Y-m-d H:i:s');
        });
    }
}
