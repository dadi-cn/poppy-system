<?php

namespace Poppy\System\Classes\Abstracts;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Class SysRouteServiceProvider
 * @package    Poppy\System\Classes\Abstracts
 * @deprecated 3.1 使用 Framework 的路由加载
 * @see        \Poppy\Framework\Application\RouteServiceProvider
 * @removed    4.0
 */
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