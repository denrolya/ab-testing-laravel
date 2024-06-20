<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AB extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ab-test';
    }
}
