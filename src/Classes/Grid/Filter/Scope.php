<?php namespace Poppy\System\Classes\Grid\Filter;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Scope implements Renderable
{
    const QUERY_NAME = '_scope_';

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var Collection
     */
    protected $queries;

    /**
     * Scope constructor.
     *
     * @param        $key
     * @param string $label
     */
    public function __construct($key, $label = '')
    {
        $this->key   = $key;
        $this->label = $label ? $label : Str::studly($key);

        $this->queries = new Collection();
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get model query conditions.
     *
     * @return array
     */
    public function condition(): array
    {
        return $this->queries->map(function ($query) {
            return [$query['method'] => $query['arguments']];
        })->toArray();
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $url = request()->fullUrlWithQuery([static::QUERY_NAME => $this->key]);
        $className = input(static::QUERY_NAME) === $this->key ? 'class="layui-this"' : '';
        return "<li {$className}><a href=\"{$url}\">{$this->label}</a></li>";
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->queries->push(compact('method', 'arguments'));

        return $this;
    }
}
