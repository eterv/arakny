<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use Arakny\Support\Format;

/**
 * Validation Extended Rules Class
 * 유효성 검사 확장 규칙 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class ValidationRules
{

    /**
     * Checks whether a variable consists of only alphanumeric characters, special characters on the keyboard and spaces.
     * 변수의 값이 알파벳 문자와 숫자, 키보드에 있는 특수기호, 공백으로만 구성되어 있는지 여부를 반환한다.
     *
     * @param string $str
     * @return bool
     */
    public function alnum_specialchars(string $str = null): bool
    {
        return (bool) Format::isAlphaNumericSpecialchars($str);
    }

    /**
     * Checks whether a variable is a boolean value.
     * 변수의 값이 bool 값인지 여부를 반환한다.
     *
     * @param string $str
     * @return bool
     */
    public function bool(string $str = null): bool
    {
        return (bool) Format::isBool($str, false);
    }

    /**
     * Checks whether a variable is a specified date and(or) time format value.
     * 변수의 값이 지정된 날짜와 시간 형식 값인지 여부를 반환한다.
     *
     * @param string $str
     * @param string $format
     * @return bool
     */
    public function valid_dt(string $str = null, string $format = Consts::DB_DATETIME_FORMAT): bool
    {
        return (bool) Format::isDateTime($str, $format);
    }

	/**
	 * Checks whether the variable type is default date/time format of database.
	 * 변수의 형식이 DB 의 기본 날짜/시간 형식인지 여부를 반환한다.
	 *
	 * @param string $str
	 * @return bool
	 */
	public function valid_dt_def(string $str = null): bool
	{
		return (bool) Format::isDateTime($str, Consts::DB_DATETIME_FORMAT);
	}

	/**
	 * Checks whether the variable type is default date format of database.
	 * 변수의 형식이 DB 의 기본 날짜 형식인지 여부를 반환한다.
	 *
	 * @param string $str
	 * @return bool
	 */
	public function valid_date_def(string $str = null): bool
	{
		return (bool) Format::isDateTime($str, Consts::DB_DATE_FORMAT);
	}

	/**
	 * Checks whether the variable type is default time format of database.
	 * 변수의 형식이 DB 의 기본 시간 형식인지 여부를 반환한다.
	 *
	 * @param string $str
	 * @return bool
	 */
	public function valid_time_def(string $str = null): bool
	{
		return (bool) Format::isDateTime($str, Consts::DB_TIME_FORMAT);
	}

}
