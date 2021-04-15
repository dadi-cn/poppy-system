<?php

namespace Poppy\System\Classes\Grid\Displayer;

class Select extends AbstractDisplayer
{
    public function display($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $name = $this->column->name;

        $class = "grid-select-{$name}";

        $key = $this->row->{$this->grid->getKeyName()};

        $optionsHtml = '';

        foreach ($options as $option => $text) {
            $selected    = $option == $this->value ? 'selected' : '';
            $optionsHtml .= "<option value=\"$option\" $selected>$text</option>";
        }

        return <<<EOT
<select style="width: 100%;" class="$class btn btn-mini" data-key="$key">
$optionsHtml
</select>

EOT;
    }
}
