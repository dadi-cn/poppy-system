<?php namespace Poppy\System\Tests\Setting;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\System\Tests\Testing\TestingPam;

class TestingPamTest extends TestCase
{

    public function testExclude()
    {
        $exclude = TestingPam::exclude();
        $this->assertNotNull($exclude);
    }
}
