<?php

namespace Poppy\System\Services\System;

use Auth;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\SysConfig;

class ServiceAuthAppend implements ServiceArray
{
    use CoreTrait;

    public function key(): string
    {
        return 'user';
    }

    /**
     */
    public function data(): array
    {
        /** @var PamAccount $pam */
        $pam = Auth::user();
        return [
            'id'             => $pam->id,
            'username'       => $pam->username,
            'mobile'         => $pam->mobile,
            'email'          => $pam->email,
            'type'           => $pam->type,
            'is_enable'      => $pam->is_enable === SysConfig::YES ? 'Y' : 'N',
            'disable_reason' => $pam->disable_reason,
            'created_at'     => $pam->created_at->toDatetimeString(),
        ];
    }
}