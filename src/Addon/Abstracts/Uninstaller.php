<?php namespace Poppy\System\Addon\Abstracts;

/**
 * Class Uninstaller.
 */
abstract class Uninstaller
{
	/**
	 * @return true
	 */
	abstract public function handle();
}