<?php namespace Poppy\System\Tests\Classes;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\System\Classes\Atomic;

class AtomicTest extends TestCase
{
	/**
	 * @
	 */
	public function testInLock(): bool
	{
		for ($start = 1; $start <= 20; $start++) {
			Atomic::inLock('testing_atomic_lock', 1);
			// 100 ms
			usleep(100000);
			if ($start % 10 === 0) {
				$this->assertEquals(Atomic::inLock('testing_atomic_lock', 1), false, 'Lock');
			}
			else {
				$this->assertEquals(Atomic::inLock('testing_atomic_lock', 1), true, 'No Lock');
			}
		}
	}
}
