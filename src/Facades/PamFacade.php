<?php

namespace Poppy\System\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class PamFacade extends IlluminateFacade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return 'poppy.system.pam';
    }
}