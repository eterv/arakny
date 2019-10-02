<?php

//use Arakny\Constants\Consts;
use Arakny\Libraries\Settings;
use CodeIgniter\HTTP\UserAgent;
use Config\Services;

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

if ( ! function_exists('_getCookie') )
{
	/**
	 * Fetch an item from the COOKIE array
	 * 지정한 쿠키를 가져온다.
	 *
	 * @param string $name
	 * @param boolean $xssClean
	 * @return mixed
	 */
	function _getCookie($name, bool $xssClean = false)
	{
		$appCookiePrefix = config(Config\App::class)->cookiePrefix;
		$prefix = isset($_COOKIE[$name]) ? '' : $appCookiePrefix;

		$request = Services::request();
		$filter = ($xssClean === true) ? FILTER_SANITIZE_STRING : null;
		$cookie = $request->getCookie($prefix . $name, $filter);

		return $cookie;
	}
}

if ( ! function_exists('_setCookie') )
{
	/**
	 * Set cookie
	 * 쿠키를 설정한다.
	 *
	 * Accepts seven parameters, or you can submit an associative
	 * array in the first parameter containing all the values.
	 *
	 * @param string|array $name     Cookie name or array containing binds
	 * @param string       $value    The value of the cookie
	 * @param string       $expire   The number of seconds until expiration
	 * @param string       $domain   For site-wide cookie.
	 *                                 Usually: .yourdomain.com
	 * @param string       $path     The cookie path
	 * @param string       $prefix   The cookie prefix
	 * @param boolean      $secure   True makes the cookie secure
	 * @param boolean      $httpOnly True makes the cookie accessible via
	 *                                 http(s) only (no javascript)
	 */
	function _setCookie($name, string $value = '', string $expire = '', string $domain = '', string $path = '/', string $prefix = '', bool $secure = false, bool $httpOnly = false)
	{
		Services::response()->setCookie($name, $value, $expire, $domain, $path, $prefix, $secure, $httpOnly);
	}
}

if ( ! function_exists('_deleteCookie') )
{
	/**
	 * Delete a cookie
	 * 지정한 쿠키를 삭제한다.
	 *
	 * @param string $name     Cookie name or array containing binds
	 */
	function _deleteCookie(string $name)
	{
		Services::response()->setCookie($name);
	}
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_redirect') )
{
	/**
	 * Perform a redirect to a new URL
	 * 새로운 URI 로 페이지 리다이렉트 한다.
	 *
	 * @param string $uri
	 */
	function _redirect(string $uri)
	{
		Services::response()->redirect($uri);
	}
}


/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_contains') )
{
    /**
     * Determine if a given string contains a given substring.
     * 주어진 문자열이 주어진 서브 문자열을 포함하고 있는지 여부를 결정한다.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $case_insensitive
     * @return bool
     */
    function _contains(string $haystack, $needle, $case_insensitive = false)
    {
        if ($case_insensitive) {
            return ($needle !== '' && mb_stripos($haystack, $needle) !== false);
        } else {
            return ($needle !== '' && mb_strpos($haystack, $needle) !== false);
        }
    }
}

if ( ! function_exists('_dashToUnderbar') )
{
	/**
	 * In a given string, replace dash symbols to underbar symbols.
	 * 주어진 문자열에서, 대쉬(-) 기호를 언더바(_) 기호로 바꾸어 반환한다.
	 *
	 * @param string $str
	 * @return string
	 */
	function _dashToUnderbar(string $str)
	{
		return str_replace('-', '_', $str);
	}
}

if ( ! function_exists('_e') )
{
    /**
     * Performs simple auto-escaping of data for security reasons.
     *
     * data 문자열을 이스케이프하여 반환한다. 주로, 보안상의 이유로 사용한다.
     * 기본적으로는 html 컨텍스트로 이스케이프된다.
     * 자주 사용되는 만큼 _e 라는 짧은 네이밍을 가지고 있다.
     *
     * If $data is a string, then it simply escapes and returns it.
     * If $data is an array, then it loops over it, escaping each
     * 'value' of the key/value pairs.
     *
     * Valid context values: html, js, css, url, attr, raw, null
     *
     * @param string|array $data
     * @param string       $context
     * @param string       $encoding
     * @return string|array
     */
    function _e($data, $context = 'html', $encoding = null)
    {
    	if (isset($data)) {
    		return esc($data, $context, $encoding);
		} else {
    		return '';
		}
    }
}

if ( ! function_exists('_ea') )
{
	/**
	 * Performs simple auto-escaping of data for security reasons.
	 * data 문자열을 속성(attribute) 용도로 이스케이프하여 반환한다. 주로, 보안상의 이유로 사용한다.
	 *
	 * @param string|array $data
	 * @param string       $encoding
	 * @return string|array
	 */
	function _ea($data, $encoding = null)
	{
		if (isset($data)) {
			return esc($data, 'attr', $encoding);
		} else {
			return '';
		}
	}
}

if ( ! function_exists('_endsWith') )
{
    /**
     * Determine if a given string ends with a given substring.
     * 주어진 문자열이 주어진 서브 문자열로 끝나는지 여부를 결정한다.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $case_insensitive
     * @return bool
     */
    function _endsWith($haystack, $needle, $case_insensitive = false)
    {
        if ($case_insensitive) {
            return (strtolower( substr($haystack, -strlen($needle)) ) === strtolower( (string) $needle ));
        } else {
            return (substr($haystack, -strlen($needle)) === (string) $needle);
        }
    }
}

if ( ! function_exists('_is') )
{
    /**
     * Determine if a given value is valid. ($name is test pattern)
     * 주어진 name 형식에 맞는 테스트를 수행하여 참 또는 거짓을 반환한다.
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    function _is($name, $value)
    {
        if ( function_exists('is' . $name) ) {
            return call_user_func('is' . $name, $value);
        }
        throw new InvalidArgumentException(_g('e_invalid_argument'));
    }
}

if ( ! function_exists('_length') )
{
    /**
     * Return the length of the given string.
     * 주어진 문자열의 길이를 반환한다.
     *
     * @param string $value
     * @param string $encoding
     * @return string
     */
    function _length(string $value, $encoding = 'UTF-8')
    {
        if ($encoding) return mb_strlen($value, $encoding);
        return mb_strlen($value);
    }
}

if ( ! function_exists('_limit') )
{
    /**
     * Limit the number of characters in a string.
     * 문자열을 지정된 길이 만큼으로 제한하고, 끝에 end 문자열을 붙인다.
     *
     * 문자열의 길이(length)가 아니라, 폭(width)이다.
     * 한국어, 중국어 등의 문자는 하나의 문자가 2의 값으로 계산된다.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function _limit(string $value, int $limit = 100, string $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) return $value;
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }
}

if ( ! function_exists('_lower') )
{
    /**
     * Convert the given string to lower-case.
     * 주어진 문자열을 소문자로 변환한다.
     *
     * @param string $value
     * @return string
     */
    function _lower(string $value)
    {
        return mb_strtolower($value, 'UTF-8');
    }
}

if ( ! function_exists('_nl2br') )
{
    /**
     * Returns a string with all instances of newline character (\n) converted to an HTML <br/> tag.
     * 개행 문자 (\n) 를 HTML <br/>태그로 변환하여 반환한다. 단, <Pre> 태그 내부는 변환하지 않는다.
     *
     * @param string $value
     * @return string
     */
    function _nl2br(string $value)
    {
        $newstr = '';
        for ($ex = explode('pre>', $value), $ct = count($ex), $i = 0; $i < $ct; $i ++ ) {
            $newstr .= (($i % 2) === 0) ? nl2br($ex[$i]) : $ex[$i];
            if ($ct - 1 !== $i) {
                $newstr .= 'pre>';
            }
        }
        return $newstr;
    }
}

if ( ! function_exists('_parseUrl'))
{
	/**
	 * parse_url() function for multi-bytes character encodings
	 * 멀티바이트 문자 인코딩을 고려하여 URL 의 세그먼트 분석하여 반환한다.
	 *
	 * @param $url
	 * @param int $component
	 * @return mixed
	 */
	function _parseUrl($url, $component = -1)
	{
		$encodedUrl = preg_replace_callback('%[^:/@?&=#]+%usD', function ($matches) {
			return urlencode($matches[0]);
		}, $url);

		$parts = parse_url($encodedUrl, $component);

		if ($parts === false) {
			throw new InvalidArgumentException('Malformed URL: ' . $url);
		}

		if (is_array($parts) && count($parts) > 0) {
			foreach ($parts as $name => $value) {
				$parts[$name] = urldecode($value);
			}
		}

		return $parts;
	}
}

if ( ! function_exists('_startsWith') )
{
    /**
     * Determine if a given string starts with a given substring.
     * 주어진 문자열이 주어진 서브 문자열로 시작하는지 여부를 결정한다.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $case_insensitive
     * @return bool
     */
    function _startsWith($haystack, $needle, $case_insensitive = false)
    {
        if ($case_insensitive) {
            return ($needle !== '' && strtolower( substr($haystack, 0, strlen($needle)) ) === strtolower( $needle ) );
        } else {
            return ($needle !== '' && substr($haystack, 0, strlen($needle)) === $needle);
        }
    }
}

if ( ! function_exists('_substr') )
{
    /**
     * Returns the portion of string specified by the start and length parameters.
     * 특정 위치의 부분 문자열을 반환한다.
     *
     * @param string $str
     * @param string $start
     * @param int $length
     * @return string
     */
    function _substr(string $str, string $start, int $length = null)
    {
        return mb_substr($str, $start, $length, 'UTF-8');
    }
}

if ( ! function_exists('_ucfirst') )
{
    /**
     * Make a string's first character uppercase.
     * 주어진 문자열의 첫 글자를 대문자로 변환하여 반환한다.
     *
     * @param string $value
     * @return string
     */
    function _ucfirst(string $value)
    {
        return _upper(_substr($value, 0, 1)) . _substr($value, 1);
    }
}

if ( ! function_exists('_underbarToDash') )
{
	/**
	 * In a given string, replace underbar symbols to dash symbols.
	 * 주어진 문자열에서, 언더바(_) 기호를 대쉬(-) 기호로 바꾸어 반환한다.
	 *
	 * @param string $str
	 * @return string
	 */
	function _underbarToDash(string $str)
	{
		return str_replace('_', '-', $str);
	}
}

if ( ! function_exists('_upper') )
{
    /**
     * Convert the given string to upper-case.
     * 주어진 문자열을 대문자로 변환한다.
     *
     * @param string $value
     * @return string
     */
    function _upper(string $value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }
}


/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_t') )
{
    /**
     * Returns translated and escaped text.
     * 번역하고 이스케이프된 문자열을 반환한다.
     *
     * @param   string  $line
     * @param   array   $args
     * @param   string  $locale
     * @param   string  $context
     * @return  string
     */
    function _t(string $line, array $args = [], string $locale = null, $context = 'html')
    {
        return esc( Services::l10n()->translate($line, $args, $locale), $context );
    }
}

if ( ! function_exists('_ta') )
{
    /**
     * Returns translated and escaped text for attribute.
     * 번역하고 어트리뷰트 출력을 위해 이스케이프된 문자열을 반환한다.
     *
     * @param   string  $line
     * @param   array   $args
     * @param   string  $locale
     * @return  string
     */
    function _ta(string $line, array $args = [], string $locale = null)
    {
        return esc( Services::l10n()->translate($line, $args, $locale), 'attr' );
    }
}

if ( ! function_exists('_tr') )
{
    /**
     * Returns raw translated text.
     * 번역하고 이스케이프 하지 않은 Raw 상태의 문자열을 반환한다.
     *
     * @param   string  $line
     * @param   array   $args
     * @param   string  $locale
     * @return  string
     */
    function _tr(string $line, array $args = [], string $locale = null)
    {
        return Services::l10n()->translateGlobals($line, $args, $locale);
    }
}

if ( ! function_exists('_g') )
{
    /**
     * Returns translated and auto escaped text in Globals.php language file.
     * Globals.php 언어 파일을 사용해 번역하고, 이스케이프된 문자열을 반환한다.
     *
     * @param   string  $line
     * @param   array   $args
     * @param   string  $locale
     * @param   string  $context
     * @return  string
     */
    function _g(string $line, array $args = [], string $locale = null, $context = 'html')
    {
        return esc( Services::l10n()->translateGlobals($line, $args, $locale), $context );
    }
}

if ( ! function_exists('_ga') )
{
    /**
     * Returns translated and escaped text for attribute. (Globals.php language file)
     * Globals.php 언어 파일을 사용해 번역하고, 어트리뷰트 출력을 위해 이스케이프된 문자열을 반환한다.
     *
     * @param   string  $line
     * @param   array   $args
     * @param   string  $locale
     * @return  string
     */
    function _ga(string $line, array $args = [], string $locale = null)
    {
        return esc( Services::l10n()->translateGlobals($line, $args, $locale), 'attr' );
    }
}

if ( ! function_exists('_gr') )
{
    /**
     * Returns raw translated text in Globals.php language file.
     * Globals.php 언어 파일을 사용해 번역하고 이스케이프 하지 않은 Raw 상태의 문자열을 반환한다.
     *
     * @param   string  $line
     * @param   array   $args
     * @param   string  $locale
     * @return  string
     */
    function _gr(string $line, array $args = [], string $locale = null)
    {
        return Services::l10n()->translateGlobals($line, $args, $locale);
    }
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_asset') )
{
    /**
     * Return...
     * 주어진 테마 파일을 테마 경로들에서 찾아서 URL 경로를 반환한다.
     *
     * 찾는 순서 :: 현재테마 -> 부모테마 -> base(기반)
     * 모든 경로에 파일이 존재하지 않는다면, 현재테마 경로를 반환한다.
     *
     * @param string $value
     * @return string
     */
    function _asset(string $value)
    {
    	if (isAdminPage()) {
			return adminThemeUrl($value);
		} else {
			return Services::theme()->getThemeFileUrl($value);
		}
    }
}

if ( ! function_exists('isAdminPage') )
{
    /**
	 * Returns whether current page is an admin page.
	 * 현재 페이지가 관리자 페이지인지 여부를 반환합니다.
	 *
	 * @return bool
	 */
	function isAdminPage() {
		return ( strpos(routerDirectory(), 'admin/') === 0 || _startsWith(routerController(), '\\admin\\') );
	}
}

if ( ! function_exists('isHome') )
{
    /**
     * Return...
     * 현재 페이지가 인덱스(Home) 페이지 인지 여부를 반환한다.
     *
     * @return bool
     */
    function isHome()
    {
        return Services::theme()->isHome();
    }
}

if ( ! function_exists('_path') )
{
    /**
     * Return...
     * 테마의 파일의 절대 경로를 가져온다.
     *
     * @param string $value
     * @return string
     */
    function _path(string $value)
    {
        return Services::theme()->getThemeFilePath($value);
    }
}

if ( ! function_exists('_url') )
{
    /**
     * Return base url with all parts or only path parts.
	 * 기반 URL 에 주어진 상대 경로를 포함하여, 완성 URL 를 반환한다.
	 *
	 * onlyPath 가 true 이면, scheme, host 등을 제외한 path 부분 이하만을,
	 * false 이면, 전체 URL 을 반환한다.
     *
     * @param string $uri
	 * @param bool $onlyPath
     * @return string
     */
    function _url(string $uri = '', $onlyPath = false)
    {
        return $onlyPath ? baseUrlOnlyPath($uri) : baseUrl($uri);
    }
}

if ( ! function_exists('_ipAddress') )
{
    /**
     * Gets the user's IP address.
     * 사용자의 IP 주소를 반환한다.
     *
     * @return string
     */
    function _ipAddress()
    {
        return Services::request()->getIPAddress();
    }
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_referrer') )
{
	/**
	 * Returns HTTP_REFERER value.
	 * HTTP_REFERER 값을 가져온다.
	 *
	 * @return bool
	 */
	function _referrer()
	{
		/** @var UserAgent $useragent */
		$useragent = Services::request()->getUserAgent();
		return $useragent->getReferrer();
	}
}

if ( ! function_exists('isBrowser') )
{
    /**
     * Returns TRUE/FALSE (boolean) if the user agent is a known web browser.
     * UserAgent 가 알려진 웹 브라우저인지 여부를 반환한다.
     *
     * @param string $key
     * @return bool
     */
    function isBrowser(string $key = null)
    {
        /** @var UserAgent $useragent */
        $useragent = Services::request()->getUserAgent();
        return $useragent->isBrowser($key);
    }
}

if ( ! function_exists('isMobile') )
{
    /**
     * Returns TRUE/FALSE (boolean) if the user agent is a known mobile device.
     * UserAgent 가 알려진 모바일 기기인지 여부를 반환한다.
     *
     * @param string $key
     * @return bool
     */
    function isMobile(string $key = null)
    {
        /** @var UserAgent $useragent */
        $useragent = Services::request()->getUserAgent();
        return $useragent->isMobile($key);
    }
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('isLoggedIn') )
{
    /**
     * Return if now user is already logged in.
     * 현재 이미 로그인 되어 있는지 여부를 반환한다.
     *
     * @return bool
     */
    function isLoggedIn()
    {
        return Services::auth()->isLoggedIn();
    }
}

if ( ! function_exists('isAdminLevel') )
{
	/**
	 * Return if current user is an administrator.
	 * 현재 로그인한 사용자가 '슈퍼관리자' 또는 '관리자' 인지 여부를 반환한다.
	 *
	 * @return bool
	 */
	function isAdminLevel()
	{
		return Services::auth()->isAdminLevel();
	}
}

if ( ! function_exists('isAdminRole') )
{
	/**
	 * Return if current user role name is Admin.
	 * 현재 로그인한 사용자의 역할이 '관리자'인지 여부를 반환한다.
	 *
	 * @return bool
	 */
	function isAdminRole()
	{
		return Services::auth()->isAdminRole();
	}
}

if ( ! function_exists('isSuperAdminRole') )
{
	/**
	 * Return if current user role name is SuperAdmin.
	 * 현재 로그인한 사용자의 역할이 '슈퍼관리자'인지 여부를 반환한다.
	 *
	 * @return bool
	 */
	function isSuperAdminRole()
	{
		return Services::auth()->isSuperAdminRole();
	}
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_date') )
{
	/**
	 * Return formatted date/time string from given date/time string or timestamp.
	 * 지정한 날짜/시간 문자열 또는 타임스탬프로부터 새로운 표시 형식으로 날짜/시간을 반환한다.
	 *
	 * @param string|int $datetime
	 * @param string $format
	 * @return string
	 */
	function _date($datetime, $format = null)
	{
		if (is_string($datetime)) $datetime = strtotime($datetime);
		if ($format === null) $format = getSetting(Settings::date_format) . ' ' . getSetting(Settings::time_format);

		return date($format, $datetime);
	}
}

if ( ! function_exists('_dateOnly') )
{
	/**
	 * Return formatted date string from given date/time string or timestamp.
	 * 지정한 날짜/시간 문자열 또는 타임스탬프로부터 날짜만을 반환한다.
	 *
	 * @param string|int $datetime
	 * @param string $format
	 * @return string
	 */
	function _dateOnly($datetime, $format = null)
	{
		if (is_string($datetime)) $datetime = strtotime($datetime);
		if ($format === null) $format = getSetting(Settings::date_format);

		return date($format, $datetime);
	}
}

if ( ! function_exists('_timeOnly') )
{
	/**
	 * Return formatted time string from given date/time string or timestamp.
	 * 지정한 날짜/시간 문자열 또는 타임스탬프로부터 시간만을 반환한다.
	 *
	 * @param string|int $datetime
	 * @param string $format
	 * @return string
	 */
	function _timeOnly($datetime, $format = null)
	{
		if (is_string($datetime)) $datetime = strtotime($datetime);
		if ($format === null) $format = getSetting(Settings::time_format);

		return date($format, $datetime);
	}
}

if ( ! function_exists('_now') )
{
	/**
	 * Return the current date/time as DB Datetime format (Y-m-d H:i:s).
	 * 현재 날짜/시간을 DB 날짜시간 형태로 반환한다.
	 *
	 * @param string $format
	 * @return string
	 */
	function _now($format = null)
	{
		return date($format ?? Arakny\Constants\Consts::DB_DATETIME_FORMAT);
	}
}

