<?php namespace Poppy\System\Classes\Form\Field;

class Time extends Date
{

    protected $options = [
        'type' => 'time',
    ];

    public function render()
    {
        $this->prepend('<i class="fa fa-clock-o fa-fw"></i>');
        return parent::render();
    }
}
