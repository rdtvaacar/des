<?php

namespace Acr\Destek\Facedes;

use Illuminate\Support\Facades\Facade;

class destek_facedes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'acr-destek';
    }
}