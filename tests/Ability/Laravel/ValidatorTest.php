<?php namespace Poppy\System\Tests\Ability\Laravel;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Validation\Rule;
use Poppy\System\Tests\Base\SystemTestCase;
use Validator;

class ValidatorTest extends SystemTestCase
{
	public function testUrl(): void
	{
		$validator = Validator::make([
			'url' => '',
		], [
			'url' => [
				Rule::string(),
				Rule::url(),
			],
		]);
		if ($validator->fails()) {
			$this->assertTrue(false, 'Url Not Right');
		}
		else {
			$this->assertTrue(true);
		}
	}


	public function testCharacter(): void
	{
		$validator = Validator::make([
			'chars' => '中国人1我是啥2',
		], [
			'chars' => [
				Rule::string(),
				Rule::min(4),
				Rule::max(8),
			],
		]);
		if ($validator->fails()) {
			$this->assertTrue(false, $validator->errors());
		}
		else {
			$this->assertTrue(true);
		}
	}
}