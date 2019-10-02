<?php defined('_ARAKNY_') OR exit;

if ( ! function_exists( 'is_widepage' ) )
{
    /**
     * 와이드 페이지 여부를 반환합니다.
     *
     * @param bool $value
     * @return bool
     */
    function is_widepage($value = null)
    {
        static $wide = true;
        if (!is_null($value)) $wide = $value;
        return $wide;
    }
}