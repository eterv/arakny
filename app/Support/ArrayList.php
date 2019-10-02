<?php namespace Arakny\Support;

use ArrayObject;
use RuntimeException;

/**
 * Array & List Static Library Class
 * 배열 & 목록 관련 정적 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class ArrayList
{

    // --------------------------------------------------------------------------------
    //		Array
    // --------------------------------------------------------------------------------

	/**
	 * 배열에 지정된 위치에 데이터를 삽입한다.
	 *
	 * $position 은 숫자 인덱스 값 또는 문자열 키 값을 지정할 수 있다.
	 *
	 * @param array      $array
	 * @param int|string $position
	 * @param mixed      $insert
	 */
	public static function arrayInsert(&$array, $position, $insert)
	{
		if (is_int($position)) {
			array_splice($array, $position, 0, $insert);
		} else {
			$pos   = array_search($position, array_keys($array));
			$array = array_merge(
				array_slice($array, 0, $pos),
				$insert,
				array_slice($array, $pos)
			);
		}
	}

    /**
     * Checks if the given keys or indexes exists in the array.
     * 배열 안에 주어진 키 또는 인덱스가 모두 있는지를 확인한다.
     *
     * 하나라도 없으면 false, 모두 존재하면 true 를 반환한다.
     *
     * @param array $keys                   An array of values to check.
     * @param array|ArrayObject $search     An array with keys to check.
     * @return bool     true on success or false on failure.
     */
    public static function arrayKeysExist($keys, $search)
    {
        if ( ! is_array($keys) ) return false;

        foreach ($keys as $key) {
            $result = array_key_exists($key, $search);
            if (!$result) return false;
        }
        return true;
    }


	// --------------------------------------------------------------------------------
	//		Dependency Resolver
	// --------------------------------------------------------------------------------

	/**
	 * Resolve dependency array and sort it.
	 * 의존성 목록을 해결하여 정렬한다.
	 *
	 * @param array $tree
	 * @return array|mixed
	 */
	public static function dependencyResolve(array $tree)
	{
		$resolved = [];
		$unresolved = [];
		// Resolve dependencies for each table
		foreach (array_keys($tree) as $table) {
			list ($resolved, $unresolved) = self::dependencyResolver($table, $tree, $resolved, $unresolved);
		}
		return $resolved;
	}

	/**
	 * @param $item
	 * @param array $items
	 * @param array $resolved
	 * @param array $unresolved
	 * @return array
	 */
	private static function dependencyResolver($item, array $items, array $resolved, array $unresolved)
	{
		array_push($unresolved, $item);
		foreach ($items[$item] as $dep) {
			if (!in_array($dep, $resolved)) {
				if (!in_array($dep, $unresolved)) {
					array_push($unresolved, $dep);
					list($resolved, $unresolved) = self::dependencyResolver($dep, $items, $resolved, $unresolved);
				} else {
					throw new RuntimeException("Circular dependency: $item -> $dep");
				}
			}
		}
		// Add $item to $resolved if it's not already there
		if (!in_array($item, $resolved)) {
			array_push($resolved, $item);
		}
		// Remove all occurrences of $item in $unresolved
		while (($index = array_search($item, $unresolved)) !== false) {
			unset($unresolved[$index]);
		}
		return [ $resolved, $unresolved ];
	}

}