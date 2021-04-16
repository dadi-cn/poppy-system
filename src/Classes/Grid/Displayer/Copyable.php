<?php

namespace Poppy\System\Classes\Grid\Displayer;


/**
 * Class Copyable.
 *
 * @see https://codepen.io/shaikmaqsood/pen/XmydxJ
 */
class Copyable extends AbstractDisplayer
{
    public function display()
    {
        return <<<HTML
<span data-clipboard-text="{$this->getValue()}">
    <i class="fa fa-copy"></i> {$this->getValue()}
</span>&nbsp;
HTML;
    }
}
