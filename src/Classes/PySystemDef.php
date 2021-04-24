<?php

namespace Poppy\System\Classes;


class PySystemDef
{
    /**
     * 模型注释
     * @return string
     */
    public static function ckModelComment(): string
    {
        return 'model-comment';
    }

    /**
     * 设置
     * @return string
     */
    public static function ckSetting(): string
    {
        return 'setting';
    }

    /**
     * 设置
     * @return string
     */
    public static function ckPamRelParent(): string
    {
        return 'pam-rel-parent';
    }

    /**
     * 一次验证码
     * @return string
     */
    public static function ckVerificationOnce(): string
    {
        return 'verification-once_code';
    }

    /**
     * 验证码
     * @return string
     */
    public static function ckVerificationCaptcha(): string
    {
        return 'verification-captcha';
    }

    /**
     * 地区缓存
     * @param string $suffix
     * @return string
     */
    public static function ckArea($suffix = ''): string
    {
        return 'area' . ($suffix ? '-' . $suffix : '');
    }

    /**
     * 国家缓存
     * @param string $suffix
     * @return string
     */
    public static function ckCountry($suffix = ''): string
    {
        return 'country' . ($suffix ? '-' . $suffix : '');
    }

}