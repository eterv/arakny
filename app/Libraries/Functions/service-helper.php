<?php

/**
 * Theme Helper Global Functions
 * 테마 도우미 전역 함수
 *
 * 테마에서 템플릿엔진을 사용하지 않는 대신, _(Underbar)로 시작하는 다양한 전역 함수를 제공한다.
 * 가능한 개발자/디자이너가 쉽게 기억하고 사용하도록 최대한 함수명을 간단명료하게 구성한다.
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('___f') )
{
    /**
     * Returns ?
     * ? 반환한다.
     *
     * @param string $value
     * @return string
     */
    function ___f(string $value)
    {
        // 내용 작성
        return $value;
    }
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('serviceAuth') )
{
    /**
     * Return the shared instance of 'auth' service object.
     * auth 서비스 객체의 공유 인스턴스를 반환한다.
     *
     * @return Arakny\Libraries\Authentication
     */
    function serviceAuth()
    {
        return Config\Services::auth();
    }
}

if ( ! function_exists('serviceL10n') )
{
    /**
     * Return the shared instance of 'l10n' service object.
     * l10n 서비스 객체의 공유 인스턴스를 반환한다.
     *
     * @return Arakny\Libraries\L10n
     */
    function serviceL10n()
    {
        return Config\Services::l10n();
    }
}

if ( ! function_exists('serviceSettings') )
{
    /**
     * Return the shared instance of 'settings' service object.
     * settings 서비스 객체의 공유 인스턴스를 반환한다.
     *
     * @return Arakny\Libraries\Settings
     */
    function serviceSettings()
    {
        return Config\Services::settings();
    }
}

if ( ! function_exists('serviceTheme') )
{
    /**
     * Return the shared instance of 'theme' service object.
     * theme 서비스 객체의 공유 인스턴스를 반환한다.
     *
     * @return Arakny\Libraries\Theme
     */
    function serviceTheme()
    {
        return Config\Services::theme();
    }
}

/* -------------------------------------------------------------------------------- */

// 정규 서비스에 포함되지 않았지만, 필요에 의해 추가.
if ( ! function_exists('service_useragent') )
{
    /**
     * Return the shared instance of 'UserAgent' class object.
     * UserAgent 클래스 객체의 공유 인스턴스를 반환한다.
     *
     * @return CodeIgniter\HTTP\UserAgent
     */
    function service_useragent()
    {
        return Config\Services::request()->getUserAgent();
    }
}

/* -------------------------------------------------------------------------------- */