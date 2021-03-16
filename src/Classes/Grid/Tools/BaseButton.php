<?php namespace Poppy\System\Classes\Grid\Tools;

/**
 * 创建按钮
 */
class BaseButton
{

    protected $title;


    protected $url;


    protected $pageBtn;


    protected $type;


    protected $pageClass;


    public function __construct($type, $title, $url, $page_btn, $page_class)
    {
        $this->type      = $type;
        $this->title     = $title;
        $this->url       = $url;
        $this->pageBtn   = $page_btn;
        $this->pageClass = $page_class;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render(): string
    {
        return <<<EOT
    <a title="{$this->title}" class="{$this->pageClass}"     href="{$this->url}">
       {$this->pageBtn}
    </a>
EOT;
    }


    public function data(): array
    {
        return [
            'title'  => $this->title,
            'url'    => $this->url,
            'action' => $this->type,
        ];
    }
}
