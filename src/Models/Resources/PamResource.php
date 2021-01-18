<?php namespace Poppy\System\Models\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

/**
 * 用户resource
 */
class PamResource extends Resource
{
    /**
     * 将资源转换成数组。
     * @param Request $request request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'username'       => $this->username,
            'mobile'         => $this->mobile,
            'email'          => $this->email,
            'type'           => $this->type,
            'is_enable'      => $this->is_enable,
            'disable_reason' => $this->disable_reason,
            'created_at'     => $this->created_at->toDatetimeString(),
            'updated_at'     => $this->updated_at->toDatetimeString(),
        ];
    }
}