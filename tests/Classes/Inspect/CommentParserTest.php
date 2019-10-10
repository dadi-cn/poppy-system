<?php namespace Poppy\System\Tests\Classes\Inspect;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Classes\Inspect\CommentParser;
use Poppy\System\Tests\Base\SystemTestCase;

class CommentParserTest extends SystemTestCase
{
	public function testParser()
	{
		$data = '<?php
	class FooBar
	{
		
		/**
		 * Short description about class
		 * 
		 * Foo Foo Foo
		 *
		 * @param PamAccount $value 描述
		 * @param \DomNode   $node  Node 描述
		 * @param value2
		 * @param Complex Value !@#$%^&*()_+-= Foo!
		 * @throws ApplicationException
		 */
		public function foo() 
		{
			// Bogus
		}		
	}';

		$p = new CommentParser();
		$x = $p->parseContent($data);

		$this->assertTrue(array_key_exists('foo', $x));
		$this->assertEquals($x['foo']['params'][0]['var_type'], 'PamAccount');
		$this->assertEquals($x['foo']['params'][0]['var_desc'], '描述');
		$this->assertEquals($x['foo']['params'][0]['var_name'], '$value');
		$this->assertEquals($x['foo']['params'][0]['type'], 'param');
		$this->assertEquals($x['foo']['throws'], 'ApplicationException');
	}
}
