<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Jobs\NotifyJob;
use Poppy\System\Tests\Base\SystemTestCase;

class JobTest extends SystemTestCase
{
    /**
     * 测试 oss 上传
     */
    public function testCallback()
    {
        dispatch(new NotifyJob('http://www2.baidu3.com', 'get', []));
    }
}