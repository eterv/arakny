<?php

use Arakny\Constants\Consts;
use Config\Services;

/**
 * Path & URL Address Helper
 * 경로 & 주소 도우미
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('adminUrl') )
{
	/**
	 * Return administration page directory url
	 * 관리페이지 디렉토리의 URL 주소 경로를 반환한다.
	 *
	 * @param string $path
	 * @return string
	 */
	function adminUrl(string $path = '')
	{
		return _url(Consts::FORDER_ADMIN . '/' . $path, true);
	}
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('adminThemePath') )
{
    /**
     * Return admin theme directory path
     * admin 테마 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return string
     */
    function adminThemePath(string $path = '')
    {
        return baseThemePath(Consts::FORDER_ADMIN . DIRECTORY_SEPARATOR . $path);
    }
}

if ( ! function_exists('adminThemeUrl') )
{
    /**
     * Return admin theme directory url
     * admin 테마 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return string
     */
    function adminThemeUrl(string $uri = '')
    {
        return baseThemeUrl(Consts::FORDER_ADMIN . '/' . $uri);
    }
}

if ( ! function_exists('baseThemePath') )
{
    /**
     * Return base theme directory path
     * base 테마 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return string
     */
    function baseThemePath(string $path = '')
    {
        return contentPath(Consts::FORDER_BASE_THEME . DIRECTORY_SEPARATOR . $path);
    }
}

if ( ! function_exists('baseThemeUrl') )
{
    /**
     * Return base theme directory url
     * base 테마 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return string
     */
    function baseThemeUrl(string $uri = '')
    {
        return contentUrl(Consts::FORDER_BASE_THEME . '/' . $uri);
    }
}

if ( ! function_exists('contentPath') )
{
    /**
     * Return content directory path
     * content 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return string
     */
    function contentPath(string $path = '')
    {
        return A_PATH . Consts::FORDER_CONTENT . DIRECTORY_SEPARATOR . $path;
    }
}

if ( ! function_exists('contentUrl') )
{
    /**
     * Return content directory url
     * content 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return string
     */
    function contentUrl(string $uri = '')
    {
        return BASEURL . Consts::FORDER_CONTENT . '/' . $uri;
    }
}

if ( ! function_exists('themesPath') )
{
    /**
     * Return themes directory path
     * themes 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return string
     */
    function themesPath(string $path = '')
    {
        return contentPath(Consts::FORDER_THEMES . DIRECTORY_SEPARATOR . $path);
    }
}

if ( ! function_exists('themesUrl') )
{
    /**
     * Return themes directory url
     * themes 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return string
     */
    function themesUrl(string $uri = '')
    {
        return contentUrl(Consts::FORDER_THEMES . '/' . $uri);
    }
}

if ( ! function_exists('uploadsPath') )
{
	/**
	 * Return uploads directory path
	 * uploads 디렉토리 경로를 반환한다.
	 *
	 * 외부 URL 접속이 가능한 노출된 경로인지, 노출되지 않은 내부 스토리지 경로인지를 선택할 수 있다.
	 *
	 * @param string $path
	 * @param bool $exposed
	 * @return string
	 */
	function uploadsPath(string $path = '', $exposed = true)
	{
		if ($exposed) {
			return contentPath(Consts::FORDER_UPLOADS . DIRECTORY_SEPARATOR . $path);
		} else {
			return WRITEPATH . Consts::FORDER_UPLOADS . DIRECTORY_SEPARATOR . $path;
		}
	}
}

if ( ! function_exists('uploadsUrl') )
{
	/**
	 * Return exposed uploads directory url
	 * 외부 URL 접속이 가능한 노출된 uploads 디렉토리의 Full URL 주소 경로를 반환한다.
	 *
	 * @param string $uri
	 * @return string
	 */
	function uploadsUrl(string $uri = '')
	{
		return contentUrl(Consts::FORDER_UPLOADS . '/' . $uri);
	}
}

if ( ! function_exists('uploadsUrlOnlyPath') )
{
	/**
	 * Return exposed uploads directory url with only path parts.
	 * 외부 URL 접속이 가능한 노출된 uploads 디렉토리의 도메인 부분을 제외한 URL 주소 경로를 반환한다.
	 *
	 * @param string $uri
	 * @return string
	 */
	function uploadsUrlOnlyPath(string $uri = '')
	{
		return _parseUrl(uploadsUrl($uri), PHP_URL_PATH);
	}
}

if ( ! function_exists('uploadsRelativeUrl') )
{
	/**
	 * Return exposed uploads directory relative url.
	 * 외부 URL 접속이 가능한 노출된 uploads 디렉토리의 상대 URL 주소 경로를 반환한다.
	 *
	 * @param string $uri Absolute URI
	 * @return string
	 */
	function uploadsRelativeUrl(string $uri = '')
	{
		if (_startsWith($uri, uploadsUrl())) {
			return _substr($uri, _length( uploadsUrl() ));
		} else if (_startsWith($uri, uploadsUrlOnlyPath())) {
			return _substr($uri, _length( uploadsUrlOnlyPath() ));
		} else {
			return $uri;
		}
	}
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('currentThemePath') )
{
    /**
     * Return current theme directory path
     * 현재 테마 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return mixed
     */
    function currentThemePath(string $path = '')
    {
        return Services::theme()->getCurrentThemePath($path);
    }
}

if ( ! function_exists('currentThemeUrl') )
{
    /**
     * Return current theme directory url
     * 현재 테마 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return mixed
     */
    function currentThemeUrl(string $uri = '')
    {
        return Services::theme()->getCurrentThemeUrl($uri);
    }
}

if ( ! function_exists('parentThemePath') )
{
    /**
     * Return parent theme directory path
     * 부모 테마 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return mixed
     */
    function parentThemePath(string $path = '')
    {
        return Services::theme()->getCurrentThemePath($path);
    }
}

if ( ! function_exists('parentThemeUrl') )
{
    /**
     * Return parent theme directory url
     * 부모 테마 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return mixed
     */
    function parentThemeUrl(string $uri = '')
    {
        return Services::theme()->getCurrentThemeUrl($uri);
    }
}