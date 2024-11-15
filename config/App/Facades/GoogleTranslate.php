<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleTranslate extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'google-translate';
    }
}
