<?php namespace Poppy\System\Module;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonSerializable;
use Poppy\Framework\Classes\Traits\HasAttributesTrait;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * Class Module.
 */
class Module implements Arrayable, ArrayAccess, JsonSerializable
{
	use HasAttributesTrait;

	/**
	 * Module constructor.
	 * @param $slug
	 * @throws ModuleNotFoundException
	 */
	public function __construct($slug)
	{
		if (!Str::contains($slug, '.')) {
			$this->attributes = [
				'directory' => poppy_path($slug),
				'namespace' => poppy_class($slug),
				'slug'      => $slug,
				'enabled'   => app('poppy')->isEnabled($slug),
			];
		}
		$poppyModule = str_after($slug, '.');
		$poppyPath   = app('path.poppy');

		$this->attributes = [
			'directory' => $poppyPath . '/' . $poppyModule,
			'namespace' => 'Poppy\\' . Str::studly($poppyModule),
			'slug'      => $slug,
			'enabled'   => app('poppy')->isEnabled($slug),
		];

	}

	/**
	 * @return string
	 */
	public function directory()
	{
		return $this->attributes['directory'];
	}

	/**
	 * @return string
	 */
	public function namespace()
	{
		return $this->attributes['namespace'];
	}

	/**
	 * @return string
	 */
	public function slug(): string
	{
		return $this->attributes['slug'];
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return (bool) $this->attributes['enabled'];
	}

	/**
	 * @return Collection
	 */
	public function pages(): Collection
	{
		return collect($this->get('pages', []))->map(function ($definition, $identification) {
			$definition['initialization']['identification'] = $identification;
			unset($definition['initialization']['tabs']);

			return $definition['initialization'];
		})->groupBy('target');
	}

	/**
	 * @return bool
	 */
	public function validate(): bool
	{
		return $this->offsetExists('name')
			&& $this->offsetExists('identification')
			&& $this->offsetExists('description')
			&& $this->offsetExists('authors');
	}
}
