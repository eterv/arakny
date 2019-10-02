<?php

use Arakny\Support\ArrayList;

/**
 * Array & List Helper
 * 배열 & 목록 도우미
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('___fa') )
{
	/**
	 * Returns ?
	 * ? 반환한다.
	 *
	 * @param string $value
	 * @return string
	 */
	function ___fa(string $value)
	{
		// 내용 작성
		return $value;
	}
}

if ( ! function_exists('arrayInsert') )
{
	/**
	 * Resolve dependency array and sort it.
	 * 의존성 목록을 해결하여 정렬한다.
	 *
	 * @param array      $array
	 * @param int|string $position
	 * @param mixed      $insert
	 */
	function arrayInsert(array &$array, $position, $insert)
	{
		ArrayList::arrayInsert($array, $position, $insert);
	}
}

if ( ! function_exists('dependencyResolve') )
{
	/**
	 * Resolve dependency array and sort it.
	 * 의존성 목록을 해결하여 정렬한다.
	 *
	 * @param array $tree
	 * @return array|mixed
	 */
	function dependencyResolve(array $tree)
	{
		return ArrayList::dependencyResolve($tree);
	}
}

/* -------------------------------------------------------------------------------- */