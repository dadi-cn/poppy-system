<?php

namespace Poppy\System\Classes\Misc;

use Illuminate\Support\Str;
use Poppy\System\Classes\PySystemDef;
use Poppy\System\Models\SysConfig;

class MiscArea
{

    /**
     * 城市的KV
     * @return array
     */
    public static function kvCity(): array
    {
        return sys_cache('py-system')->remember(PySystemDef::ckArea('kv'), SysConfig::MIN_ONE_MONTH, function () {
            $area    = self::area();
            $collect = [];
            collect($area)->each(function ($item) use (&$collect) {
                if (Str::endsWith($item['id'], '00') && ((string) substr($item['id'], 2, 2)) !== '00') {
                    $collect[substr($item['id'], 0, 4)] = $item['title'];
                }
            });
            return $collect;
        });
    }

    /**
     * 国家KV
     * @return array
     */
    public static function kvCountry(): array
    {
        return sys_cache('py-system')->remember(PySystemDef::ckCountry('kv'), SysConfig::MIN_ONE_MONTH, function () {
            $area    = self::country();
            $collect = [];
            collect($area)->each(function ($country) use (&$collect) {
                $collect[$country['iso']] = $country['zh'];
            });
            return $collect;
        });
    }

    /**
     * 国别缓存
     * @return array|mixed
     */
    public static function country()
    {
        return sys_cache('py-system')->remember(PySystemDef::ckCountry(), SysConfig::MIN_ONE_MONTH, function () {
            return include poppy_path('poppy.system', 'resources/def/country.php');
        });
    }

    /**
     * 获取区域数据
     * @return array|mixed
     */
    public static function area()
    {
        return sys_cache('py-system')->remember(PySystemDef::ckArea(), SysConfig::MIN_ONE_MONTH, function () {
            $path    = poppy_path('poppy.system', 'resources/def/area.php');
            $area    = include $path;
            $collect = [];
            collect($area)->each(function ($title, $code) use (&$collect) {
                if (Str::endsWith($code, '0000')) {
                    $collect[] = [
                        'id'        => $code,
                        'title'     => $title,
                        'parent_id' => 0,
                    ];
                }
                elseif (Str::endsWith($code, '00')) {
                    $parentKey = substr($code, 0, 2) . '0000';
                    $collect[] = [
                        'id'        => $code,
                        'title'     => $title,
                        'parent_id' => $parentKey,
                    ];
                }
                else {
                    $parentKey = substr($code, 0, 4) . '00';
                    $collect[] = [
                        'id'        => $code,
                        'title'     => $title,
                        'parent_id' => $parentKey,
                    ];
                }
            });
            return $collect;
        });
    }

}