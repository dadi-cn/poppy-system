<?php

namespace Poppy\System\Classes\Grid\Displayer;

use Illuminate\Support\Arr;

class SwitchDisplay extends AbstractDisplayer
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

    public function display($states = [])
    {
        $this->updateStates($states);

        $name = $this->column->name;

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

        $checked = $this->states['on']['value'] == $this->value ? 'checked' : '';

        return <<<EOT
        <input type="checkbox" class="$class" $checked data-key="$key" />
EOT;
    }
}
