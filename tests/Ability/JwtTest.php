<?php namespace Poppy\System\Tests\Ability;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class JwtTest extends SystemTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->initPam();
    }

    public function testGenToken()
    {
        $id = $this->pam->id;
        echo auth('jwt')->tokenById($id);
    }
}
