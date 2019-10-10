<?php namespace Poppy\System\Tests\Ability\Core;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;

class ArrayTest extends TestCase
{
	/**
	 * 向数组头部追加数据
	 */
	public function testUnshift()
	{
		$items = [
			'fun' => 'fun-v',
		];
		$items = array_merge([
			'order' => 'order-v',
		], $items);
		$this->assertCount(2, $items);
	}

	public function testPlus()
	{
		$arrayA = [
			'a' => -1,
			'b' => -2,
		];
		$arrayB = [
			'b' => 2,
			'c' => 3,
		];
		// question 面试题目, 数组相加的时候会出现的bug 问题
		$result = $arrayA + $arrayB;
		$this->assertEquals($result['b'], -2);
	}

	public function testDiff()
	{
		$total      = [1, 3, 5, 7, 9, 10];
		$append     = [1, 4, 5, 7, 11];
		$appendDiff = array_diff($append, $total);
		$this->assertCount(2, $appendDiff);
	}

	public function testIsset()
	{
		$af = $d['a']['f'] ?? 'none';
		$this->assertEquals('none', $af, 'Isset Check');
	}

	public function testInArray():void
	{
		$in = in_array('4', [2, 4], true);
		$this->assertFalse($in);
	}
}
