<?php namespace Poppy\System\Models\Actions;

use Poppy\System\Models\PamAccount;

/**
 * Base Action
 */
abstract class BaseAction
{

    /**
     * 用户账户
     * @var PamAccount
     */
    protected $pam;

    /**
     * @var object
     */
    protected $item;


    public function __construct($pam)
    {
        $this->pam = $pam;
    }


    public function setItem($item): self
    {
        $this->item = $item;
        return $this;
    }
}