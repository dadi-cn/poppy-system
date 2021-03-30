<?php

namespace Poppy\System\Classes\Grid\Displayer;

use Illuminate\Support\Arr;
use Poppy\System\Classes\Facades\Admin;

class SwitchGroup extends AbstractDisplayer
{
    protected $states = [
        'on'  => ['value' => 1, 'text' => 'ON', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => 'OFF', 'color' => 'default'],
    ];

    protected function updateStates($states)
    {
        foreach (Arr::dot($states) as $key => $state) {
            Arr::set($this->states, $key, $state);
        }
    }

    public function display($columns = [], $states = [])
    {
        $this->updateStates($states);

        if (!Arr::isAssoc($columns)) {
            $labels = array_map('ucfirst', $columns);

            $columns = array_combine($columns, $labels);
        }

        $html = [];

        foreach ($columns as $column => $label) {
            $html[] = $this->buildSwitch($column, $label);
        }

        return '<table>' . implode('', $html) . '</table>';
    }

    protected function buildSwitch($name, $label = '')
    {
        $class = 'grid-switch-' . str_replace('.', '-', $name);

        $keys = collect(explode('.', $name));
        if ($keys->isEmpty()) {
            $key = $name;
        }
        else {
            $key = $keys->shift() . $keys->reduce(function ($carry, $val) {
                    return $carry . "[$val]";
                });
        }


        $key = $this->row->{$this->grid->getKeyName()};

        $checked = $this->states['on']['value'] == $this->row->$name ? 'checked' : '';

        return <<<EOT
<tr style="height: 28px;">
    <td><strong><small>$label:</small></strong>&nbsp;&nbsp;&nbsp;</td>
    <td><input type="checkbox" class="$class" $checked data-key="$key" /></td>
</tr>
EOT;
    }
}
