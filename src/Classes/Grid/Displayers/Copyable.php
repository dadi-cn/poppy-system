<?php namespace Poppy\System\Classes\Grid\Displayers;

use Poppy\System\Classes\Facades\Admin;

/**
 * Class Copyable.
 *
 * @see https://codepen.io/shaikmaqsood/pen/XmydxJ
 */
class Copyable extends AbstractDisplayer
{
    protected function addScript()
    {
        $script = <<<SCRIPT
$('#{$this->grid->tableId}').on('click','.grid-column-copyable',(function (e) {
    var content = $(this).data('content');
    
    var temp = $('<input>');
    
    $("body").append(temp);
    temp.val(content).select();
    document.execCommand("copy");
    temp.remove();
    
    $(this).tooltip('show');
}));
SCRIPT;

    }

    public function display()
    {
        $this->addScript();

        $content = $this->getColumn()->getOriginal();

        return <<<HTML
<a href="javascript:void(0);" class="grid-column-copyable text-muted" data-content="{$content}" title="Copied!" data-placement="bottom">
    <i class="fa fa-copy"></i>
</a>&nbsp;{$this->getValue()}
HTML;
    }
}
