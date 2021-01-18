<?php namespace Poppy\System\Classes\Form\Field;

use Poppy\System\Classes\Form\Field;

class Code extends Field
{
    protected $attributes = [
        'class' => 'layui-textarea layui-textarea-code',
        'style' => 'font-family: monospace;',
        'rows'  => 6,
    ];
}
