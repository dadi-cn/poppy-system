<?php namespace Poppy\System\Classes\Validation;

use Illuminate\Validation\Rule as IlluminateRule;

/**
 * Class Rule.
 */
class SysRule extends IlluminateRule
{
    /**
     * @return string
     */
    public static function mobileCty(): string
    {
        return 'mobile_cty';
    }
}
