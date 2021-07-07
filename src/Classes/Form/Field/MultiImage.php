<?php

namespace Poppy\System\Classes\Form\Field;

use Poppy\System\Classes\Form\Field;

final class MultiImage extends Field
{

    /**
     * @inheritDoc
     */
    protected $view = 'py-system::tpl.form.multi_image';

    /**
     * Token
     * @var string
     */
    private $token;

    /**
     * 上传数量
     * @var int
     */
    private $number;

    public function token($token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 最大上传数量
     * @param $number
     */
    public function number($number)
    {
        $this->number = $number;
    }


    public function render()
    {
        $this->attribute([
            'token'  => $this->token,
            'number' => $this->number,
        ]);
        return parent::render();
    }
}
