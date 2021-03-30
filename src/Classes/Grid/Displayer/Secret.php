<?php

namespace Poppy\System\Classes\Grid\Displayer;

class Secret extends AbstractDisplayer
{
    public function display($dotCount = 6)
    {

        $dots = str_repeat('*', $dotCount);

        return <<<HTML
<span class="secret-wrapper">
    <i class="fa fa-eye" style="cursor: pointer;"></i>
    &nbsp;
    <span class="secret-placeholder" style="vertical-align: middle;">{$dots}</span>
    <span class="secret-content" style="display: none;">{$this->getValue()}</span>
</span>
HTML;
    }

}
