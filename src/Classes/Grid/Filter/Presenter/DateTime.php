<?php

namespace Poppy\System\Classes\Grid\Filter\Presenter;

class DateTime extends Presenter
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * DateTime constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $this->getOptions($options);
    }

    public function variables(): array
    {
        return [
            'group'   => $this->filter->group,
            'options' => $this->options,
        ];
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    protected function getOptions(array $options): array
    {
        return $options;
    }
}
