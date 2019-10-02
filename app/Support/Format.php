<?php namespace Arakny\Support;

use Arakny\Constants\Consts;

/**
 * Data Type & Format Static Library Class
 * 자료 형식 관련 정적 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Format
{

    // --------------------------------------------------------------------------------
    //      Check Data Formats
    // --------------------------------------------------------------------------------

    /**
     * Checks whether a variable is a boolean value.
     * 변수의 값이 bool 값인지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @param bool $orig    Optional. Whether it use PHP function is_bool. Default value is false.
     * @return bool
     */
    public static function isBool($var, $orig = false) {
        if ($orig) return is_bool($var);
        return (bool) preg_match('/^(true|false|1|0)$/i', (string)$var);
    }

    /**
     * Checks whether a variable is a natual number.
     * 변수의 값이 자연수인지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @param bool $zero    Optional. Whether to include '0'. Default value is true.
     * @return bool
     */
    public static function isNaturalNumber($var, $zero = true) {
        if (!preg_match('/^[0-9]+$/', (string)$var)) return false;
        return ((int)$var >= ($zero ? 0 : 1)) ? true : false;
    }

    /**
     * Checks whether a variable is a integer.
     * 변수의 값이 정수인지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @return bool
     */
    public static function isInteger($var) {
        //return (filter_var($var, FILTER_VALIDATE_INT) !== FALSE);
        return (bool) preg_match('/^\-?[0-9]+$/', (string)$var);
    }

    /**
     * Checks whether a variable consists of only alphabetic characters.
     * 변수의 값이 알파벳 문자로만 구성되어 있는지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @return bool
     */
    public static function isAlphabet($var) {
        return (bool) preg_match('/^[A-Za-z]+$/', $var);
    }

    /**
     * Checks whether a variable consists of only alphanumeric characters.
     * 변수의 값이 알파벳 문자와 숫자로만 구성되어 있는지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @return bool
     */
    public static function isAlphaNumeric($var) {
        return (bool) preg_match('/^[A-Za-z0-9]+$/', $var);
    }

    /**
     * Checks whether a variable consists of only alphanumeric characters and underbar(_) symbols.
     * 변수의 값이 알파벳 문자와 숫자, 언더바 기호로만 구성되어 있는지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @return bool
     */
    public static function isAlphaNumericUnderbar($var) {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $var);
    }

	/**
	 * Checks whether a variable consists of only alphanumeric characters, underbar(_) and dash(-) symbols.
	 * 변수의 값이 알파벳 문자와 숫자, 언더바(_), 대쉬(-) 기호로만 구성되어 있는지 여부를 반환한다.
	 *
	 * @param mixed $var    a value to check.
	 * @return bool
	 */
	public static function isAlphaNumericUnderbarDash($var) {
		return (bool) preg_match('/^[A-Za-z0-9_-]+$/', $var);
	}

    /**
     * Checks whether a variable consists of only alphanumeric characters, special characters on the keyboard and spaces.
     * 변수의 값이 알파벳 문자와 숫자, 키보드에 있는 특수기호, 공백으로만 구성되어 있는지 여부를 반환한다.
     *
     * @param mixed $var    a value to check.
     * @return bool
     */
    public static function isAlphaNumericSpecialchars($var) {
        return (bool) preg_match('/^[A-Za-z0-9!@#$%\\^&*()\\-_[\\]{}<>~`+=,.;:\\/?\\\\\\|\'"\s]+$/', $var);
    }

    /**
     * Checks whether a variable is a specified date and(or) time format value.
     * 변수의 값이 지정된 날짜와 시간 형식 값인지 여부를 반환한다.
     *
     * @param string $var       a value to check.
     * @param string $format    a date/time format.
     * @return bool
     */
    public static function isDateTime($var, $format = Consts::DB_DATETIME_FORMAT) {
        if (empty($format)) return (bool) strtotime($var);
        if ($format == 'Y-m-d H:i:s' && $var == '0000-00-00 00:00:00') return true;
        if ($format == 'Y-m-d' && $var == '0000-00-00') return true;
        if ($format == 'H:i:s' && $var == '00:00:00') return true;
        $d = \DateTime::createFromFormat($format, $var);
        return $d && $d->format($format) === $var;
    }

    /**
     * Checks whether a variable is an email address value.
     * 변수의 값이 이메일 주소 값인지 여부를 반환한다.
     *
     * @param string $var   a value to check.
     * @param bool $rfc5322 Optional. Whether to use the RFC5322 Guide rule. Default value is true.
     * @return bool
     */
    public static function isEmail($var, $rfc5322 = true) {
        // RFC 5322 표준으로 확인하지 않는다면, 경우의 수가 날이갈수록 많아진 까닭에 대략적으로 @ 문자만 하나 들어가면 통과한다.
        // RFC 5322 Official Standard
        // Reference site :: https://emailregex.com
        $regex = $rfc5322 ? '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD' : '/^[^@]+@[^@]+$/';
        return (bool) preg_match($regex, $var);
    }

    /**
     * Checks whether a variable is a URL value.
     * 변수의 값이 URL 값인지 여부를 반환한다.
     *
     * @param string $var   a value to check.
     * @return bool
     */
    public static function isURL($var) {
        // Reference site :: http://urlregex.com/
        return (bool) preg_match('%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $var);
    }

    /**
     * Checks whether a variable is an IP Address value. Checks both IPv4 and IPv6 formats.
     * 변수의 값이 IP 주소 값인지 여부를 반환한다. IPv4, IPv6 형식 모두 확인한다.
     *
     * @param string $var   a value to check.
     * @return bool
     */
    public static function isIPAddress($var) {
        return (filter_var($var, FILTER_VALIDATE_IP) !== false);
    }

    /**
     * Checks whether a variable is an IP Address value. Checks only IPv4 format.
     * 변수의 값이 IP 주소 값인지 여부를 반환한다. IPv4 형식만 확인한다.
     *
     * @param string $var   a value to check.
     * @return bool
     */
    public static function isIPv4($var) {
        return (filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false);
    }

    /**
     * Checks whether a variable is an IP Address value. Checks only IPv6 format.
     * 변수의 값이 IP 주소 값인지 여부를 반환한다. IPv6 형식만 확인한다.
     *
     * @param string $var   a value to check.
     * @return bool
     */
    public static function isIPv6($var) {
        return (filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false);
    }

}