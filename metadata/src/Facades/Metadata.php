<?php

namespace Kalephan\Metadata\Facades;

use Illuminate\Support\Facades\Facade;

class Metadata extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'Metadata';
    }
}
