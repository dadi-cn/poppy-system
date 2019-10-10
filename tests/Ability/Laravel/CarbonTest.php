<?php namespace Poppy\System\Tests\Ability\Laravel;

/**
 * Copyright (C) Update For IDE
 */

use Carbon\Carbon;
use Poppy\System\Tests\Base\SystemTestCase;

class CarbonTest extends SystemTestCase
{
	/**
	 * Diff 测试
	 */
	public function testDiff(): void
	{
		$last30Min = Carbon::now()->subMinutes(30);
		$this->assertEquals(Carbon::now()->diffInMinutes($last30Min, false), -30);

		$feature30Min = Carbon::now()->addMinutes(31);
		$this->assertEquals(Carbon::now()->diffInMinutes($feature30Min, false), 30);
	}

	/**
	 * 解析日期
	 */
	public function testParse(): void
	{
		$date = '2017-05-08';
		$this->assertEquals(Carbon::parse($date)->format('Ymd'), '20170508', 'Carbon Parse Error');

		$date = '2017/05/08';
		$this->assertEquals(Carbon::parse($date)->format('Ymd'), '20170508', 'Carbon Parse Error');

		$date = '2017/5/8';
		$this->assertEquals(Carbon::parse($date)->format('Ymd'), '20170508', 'Carbon Parse Error');

		$datetime = '2017-05-08 02:05:22';
		$this->assertEquals(Carbon::parse($datetime)->format('Ymdhis'), '20170508020522', 'Carbon Parse Error');

		$datetime = '2017-05-08 02:05:22';
		$this->assertEquals(Carbon::make($datetime)->format('Ymdhis'), '20170508020522', 'Carbon Parse Error');

		$date = '';
		$this->assertEquals(Carbon::parse($date)->format('Ymd'), Carbon::now()->format('Ymd'), 'Carbon Parse Error');
	}

	public function testCompare(): void
	{
		$now     = Carbon::now();
		$nowCopy = Carbon::now();
		$result  = $now->addMinute(5)->greaterThan($nowCopy);
		$this->assertTrue($result);

		$this->assertFalse('2019-06-08 23:00:00' > '2019-06-09 02:00:00');
	}
}