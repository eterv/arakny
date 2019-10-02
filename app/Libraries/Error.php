<?php namespace Arakny\Libraries;

/**
 * Error Library Class
 * 오류 관련 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Error
{

	protected $errors = [];

	protected $lastErrorMessage = '';
	protected $lastErrorData = [];

    public function __construct()
	{

	}

	/**
	 * Return all datas of custom errors.
	 * 모든 사용자 오류 데이터를 반환한다.
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Return the data of last custom error(s) that occurred.
	 * 발생한 마지막 사용자 오류의 데이터를 반환한다.
	 *
	 * @return array
	 */
	public function getLastErrorData()
	{
		return $this->lastErrorData;
	}

	/**
	 * Return the message string of last custom error(s) that occurred.
	 * 발생한 마지막 사용자 오류의 문자열 메시지를 반환한다.
	 *
	 * @return string
	 */
	public function getLastErrorMessage()
	{
		return $this->lastErrorMessage;
	}

	/**
	 * Set error with message and array data
	 * 메시지와 배열 데이터를 사용하여 오류를 기록한다.
	 *
	 * @param string $message
	 * @param array $data
	 */
	public function setError(string $message, $data = [])
	{
		// 마지막 오류에 기록
		$this->lastErrorMessage = $message;
		$this->lastErrorData = $data ?? [];

		// 오류 배열에 추가
		$this->errors[] = [
			'message' => $this->lastErrorMessage,
			'data' => $this->lastErrorData,
		];
	}

}
