<?php namespace Poppy\System\Tests\Ability\Laravel;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Tests\Base\SystemTestCase;

class CollectionTest extends SystemTestCase
{
	public function testFilter()
	{
		$accountIds = [1, 2, 3, 4, 5];
		$accountIds = collect($accountIds)->filter(function ($id) {
			return $id !== 4;
		});
		$this->assertCount(4, $accountIds);
	}

	public function testCollect()
	{
		$collect = collect([1, 2, 3, 4, 5]);
		$items   = collect($collect)->values()->toArray();
		$this->assertCount(5, $items);
	}
}