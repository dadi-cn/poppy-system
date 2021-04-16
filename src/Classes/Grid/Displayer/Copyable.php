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
<a href="javascript:void(0);" class="grid-column-copyable text-muted" data-content="{$this->getValue()}" title="Copied!" data-placement="bottom">
    <i class="fa fa-copy"></i>
</a>&nbsp;{$this->getValue()}
HTML;
    }
}
