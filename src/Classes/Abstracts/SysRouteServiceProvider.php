<?php namespace Poppy\System\Classes\Abstracts;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

abstract class SysRouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     * @deprecated
     * @see $prefix
     */
    protected $backendPrefix;

    /**
     * 前缀
     * @var string
     */
    protected $prefix;


    public function __construct($app)
    {
        parent::__construct($app);
        $this->backendPrefix = config('poppy.system.prefix') ?: 'backend';
        $this->prefix        = config('poppy.system.prefix') ?: 'backend';
    }
}