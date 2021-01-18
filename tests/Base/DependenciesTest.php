<?php namespace Poppy\System\Tests\Base;

use EloquentFilter\ServiceProvider;
use Faker\Provider\Base;
use Poppy\Framework\Application\TestCase;

class DependenciesTest extends TestCase
{
    public function testVendor()
    {
        // 系统模块测试
        $this->poppyTestVendor([
            'fzaninotto/faker'           => Base::class,
            'tucker-eric/eloquentfilter' => ServiceProvider::class,
        ]);
    }
}