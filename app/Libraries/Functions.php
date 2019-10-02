<?php

use Arakny\Constants\Consts;
use Config\Services;

/**
 * Arakny Functions Library
 * 아라크니 함수 라이브러리
 *
 * 개발을 용이하게 해주는 함수들을 모아놓은 라이브러리 입니다.
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

/**
 * 함수 설명
 *
 * @return	mixed
 */
function _unknownFunction() {
    // 새 함수를 작성하기 위한 기본 템플릿 입니다.
    return true;
}

/* -------------------------------------------------------------------------------- */

function _echo($var, $end_br = true) {
    echo $var . ($end_br ? '<br>' : '');
}

function _log($message, ...$context) {
	log_message('critical', $message, $context);
}

/* -------------------------------------------------------------------------------- */
/* 		base URL
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('baseUrl') )
{
	/**
	 * Return base url with all parts.
	 * 기반 URL 에 주어진 상대 경로를 포함하여, 완성 URL 를 반환한다.
	 * URL 안에는 scheme, host 등이 모두 포함된다.
	 *
	 * @param string $uri
	 * @return string
	 */
	function baseUrl($uri = '')
	{
		return BASEURL . (_startsWith($uri, '/') ? _substr($uri, 1) : $uri);
	}
}

if ( ! function_exists('baseUrlOnlyPath') )
{
	/**
	 * Return base url with only path parts.
	 * 기반 URL 에 주어진 상대 경로를 포함하여, 완성 URL 를 반환한다.
	 * URL 안에는 scheme, host 등을 제외하고 path 부분 이하만이 포함된다.
	 *
	 * @param string $uri
	 * @return string
	 */
	function baseUrlOnlyPath($uri = '')
	{
		$base = _parseUrl(BASEURL, PHP_URL_PATH);
		if (! _endsWith($base, '/')) $base .= '/';
		return $base . $uri;
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Installation (설치)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('isInstalled') )
{
	/**
	 * Determines whether Arakny is installed.
	 * 아라크니가 설치되어 있는지 여부를 알려줍니다.
	 *
	 * @return	bool
	 */
	function isInstalled()
	{
		if (getInstallCode() !== 2) {
			return false;
		}
		return true;

		// 설정 파일 헤더 체크에 더해,,
		// - base_url 과 db settings 의 site_url 이 같은지 (그전에 값이 다 있는지 null 아닌지 체크)
		// 모두 문제가 없다면 설치 완료된 상태

		// 작업중...
	}
}

if ( ! function_exists('getInstallCode') )
{
	/**
	 * Returns the Arakny installation code.
	 * 아라크니 설치 코드값을 반환합니다.
	 *
	 * @return int
	 */
	function getInstallCode()
	{
		$file = fopen(Consts::FILE_ENV, 'r');
		$line = fgets($file);
		fclose($file);
		if (preg_match('/^#INSTALL-CODE-(\d{1})#\r?$/', $line, $code) !== 1) {
			die( 'An critical error has occurred. (Core file has been damaged.)' );
		}
		return (int) $code[1];
	}
}

if ( ! function_exists('redirectInstall') )
{
	/**
	 * 설치 메인 페이지로 바로 리다이렉트 한다.
	 *
	 * @return CodeIgniter\HTTP\RedirectResponse
	 */
	function redirectInstall()
	{
		return redirect()->to(BASEURL . 'admin/install');
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Request Helper (요청 헬퍼)
/* -------------------------------------------------------------------------------- */

/**
 * Fetch an item from the GET array.
 * If the value doesn't exist, return the defualt value.
 *
 * @since 0.1.0
 *
 * @param	mixed	$index		    Optional. Index for item to be fetched from $_GET
 * @param	mixed	$default_value	Optional. Default value.
 * @param	mixed	$filter	        Optional. A filter name to apply.
 * @param   mixed   $flags
 * @return	mixed
 */
function inputGet($index = null, $default_value = null, $filter = null, $flags = null)
{
    return Services::request()->getGet($index, $filter, $flags) ?? $default_value;
}

/**
 * Fetch an item from the POST array.
 * If the value doesn't exist, return the defualt value.
 *
 * @since 0.1.0
 *
 * @param	mixed	$index		    Optional. Index for item to be fetched from $_POST
 * @param	mixed	$default_value	Optional. Default value.
 * @param	mixed	$filter	        Optional. A filter name to apply.
 * @param   mixed   $flags
 * @return	mixed
 */
function inputPost($index = null, $default_value = null, $filter = null, $flags = null)
{
    return Services::request()->getPost($index, $filter, $flags) ?? $default_value;
}

/* -------------------------------------------------------------------------------- */
/* 		URI
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('url_exists')) {
    /**
     * 지정한 URL 주소의 페이지가 존재하는지 아닌지를 반환한다.
     *
     * @param   string $url URL 주소
     * @return    bool
     */
    function url_exists($url)
    {
        return (@file_get_contents($url, 0, null, 0, 1) !== false) ? true : false;
        /*$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $retcode;*/
    }
}

/* -------------------------------------------------------------------------------- */
/* 		Routing Helper (라우팅 헬퍼)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('routerDirectory') )
{
	/**
	 * Return the directory name of this routed page.
	 * 현재 라우트된 페이지의 디렉토리 이름을 반환한다.
	 *
	 * @param bool $lowercase
	 * @return string
	 */
	function routerDirectory($lowercase = true)
	{
		$result = Services::router()->directory();
		if ($lowercase) {
			$result = strtolower($result);
		}
		return $result;
	}
}

if ( ! function_exists('routerController') )
{
	/**
	 * Return the controller name of this routed page.
	 * 현재 라우트된 페이지의 컨트롤러 이름을 반환한다.
	 *
	 * @param bool $isFullName
	 * @param bool $lowercase
	 * @return string
	 */
	function routerController($isFullName = false, $lowercase = true)
	{
		$ns = Services::routes()->getDefaultNamespace();
		$result = Services::router()->controllerName();
		if (! $isFullName) {
			$result = str_replace($ns, '', $result);
		}
		if ($lowercase) {
			$result = strtolower($result);
		}
		return $result;
	}
}

if ( ! function_exists('routerMethod') )
{
	/**
	 * Return the method name of this routed page.
	 * 현재 라우트된 페이지의 메소드 이름을 반환한다.
	 *
	 * @param bool $lowercase
	 * @return string
	 */
	function routerMethod($lowercase = true)
	{
		$result = Services::router()->methodName();
		if ($lowercase) {
			$result = strtolower($result);
		}
		return $result;
	}
}

if ( ! function_exists('routerParams') )
{
	/**
	 * Return the parameter array of this routed page.
	 * 현재 라우트된 페이지의 매개변수 배열을 반환한다.
	 *
	 * @return array
	 */
	function routerParams()
	{
		return Services::router()->params();
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Error Helper (오류 헬퍼)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('errorData') )
{
	/**
	 * Return the data of last custom error(s) that occurred.
	 * 발생한 마지막 사용자 오류의 데이터를 반환한다.
	 *
	 * @return array
	 */
	function errorData()
	{
		return Services::error()->getLastErrorData();
	}
}

if ( ! function_exists('errorMessage') )
{
	/**
	 * Return the message string of last custom error(s) that occurred.
	 * 발생한 마지막 사용자 오류의 문자열 메시지를 반환한다.
	 *
	 * @return string
	 */
	function errorMessage()
	{
		return Services::error()->getLastErrorMessage();
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Events Helper (이벤트 헬퍼)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('onEvent') )
{
    /**
     * Registers an action to happen on an event. The action can be any sort
     * of callable:
     *
     *  add_event('event_name', 'myFunction');               // procedural function
     *  add_event('event_name', ['myClass', 'myMethod']);    // Class::method
     *  add_event('event_name', [$myInstance, 'myMethod']);  // Method on an existing instance
     *  add_event('event_name', function() {});              // Closure
     *
     * @param string $eventName
     * @param callable $callback
     * @param int $priority
     * @return void
     */
    function onEvent($eventName, callable $callback, $priority = EVENT_PRIORITY_NORMAL)
    {
        CodeIgniter\Events\Events::on($eventName, $callback, $priority);
    }
}

if ( ! function_exists('triggerEvent') )
{
    /**
     * Trigger given event
     *
     * @param string $eventName
     * @param array $arguments
     * @return mixed
     */
    function triggerEvent($eventName, ...$arguments)
    {
        return CodeIgniter\Events\Events::trigger($eventName, ...$arguments);
    }
}

if ( ! function_exists('triggerFilter') )
{
    /**
     * Trigger given filter event.
     *
     * @param string $eventName
     * @param mixed $value
     * @param array $arguments
     * @return mixed
     */
    function triggerFilter($eventName, $value, ...$arguments)
    {
        return CodeIgniter\Events\Events::triggerFilter($eventName, $value, ...$arguments);
    }
}

/* -------------------------------------------------------------------------------- */
/* 		Settings Helper (설정 헬퍼)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('getSetting') )
{
	/**
	 * Return a specified setting value
	 * 설정 값을 가져옵니다.
	 *
	 * @param string $name      설정 이름(키)
	 * @param mixed $defvalue   기본값
	 * @return mixed
	 */
	function getSetting($name, $defvalue = null)
	{
		return Services::settings()->get($name, $defvalue);
	}
}

if ( ! function_exists('setSetting') )
{
	/**
	 * Save the setting value in database.
	 * 설정 값을 데이터베이스에 저장한다.
	 *
	 * @param string $name  설정 이름(키)
	 * @param mixed $value  새로 지정할 값
	 * @return mixed
	 */
	function setSetting($name, $value)
	{
		return Services::settings()->set($name, $value);
	}
}

if ( ! function_exists('getFileUploadMaxSize') )
{
	/**
	 * Return the bytes of max file upload size.
	 * 업로드 할 수 있는 최대 크기를 바이트단위의 정수로 반환한다.
	 *
	 * @return int
	 */
	function getFileUploadMaxSize()
	{
		/**
		 * @param string $size
		 * @return int
		 */
		function convertPHPSizeToBytes($size)
		{
			$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
			$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
			if ($unit) {
				// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
				return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
			} else {
				return round($size);
			}
		}

		return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Include Function Files
/* -------------------------------------------------------------------------------- */

include_once 'Functions/service-helper.php';
include_once 'Functions/path-helper.php';
include_once 'Functions/base-helper.php';
include_once 'Functions/array-helper.php';

include_once 'Functions/filesystem.php';
include_once 'Functions/security.php';
include_once 'Functions/code-helper.php';

/* -------------------------------------------------------------------------------- */

function test1(array $datas) {
	$datas['data']['isTest'] = true;
	return $datas;
}

function test2(array $datas) {
	$datas['data']['isTest'] = false;
	$datas['data']['numTest2'] = 48;
	$datas['rules']['test2rule'] = 'required';
	return $datas;
}