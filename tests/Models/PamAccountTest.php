<?php namespace Poppy\System\Tests\Models;

use Poppy\System\Models\PamAccount;
use Poppy\System\Tests\Base\SystemTestCase;

class PamAccountTest extends SystemTestCase
{
    public function testPermissions()
    {
        $user        = PamAccount::passport(env('TEST_USER'));
        $permissions = PamAccount::permissions($user);
        $this->assertNotNull($permissions, 'User has no permission');
        $names = $permissions->pluck('name');
        $this->assertNotNull($names, 'User has no permission');
    }

    public function testJwtToken()
    {
        echo auth('jwt_web')->tokenById(10);
    }
}