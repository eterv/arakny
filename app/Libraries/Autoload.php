<?php namespace Arakny\Libraries;

// 현재 사용되지 않음.
// 추후에 필요한 경우 사용될 수 있음.

/**
 * Arakny Extend Autoload Class
 * 아라크니 확장 자동로드 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Autoload
{
    /**
     * @return Autoload|null
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new Autoload();
        }
        return $instance;
    }

    private function __construct()
    {
        // Twig Library
        //spl_autoload_register([$this, 'loadTwigClass'], true, true);
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone() { $this->__wakeup(); }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup() { }

    /**
     * Autoloads Twig classes
     *
     * @param $class
     */
    protected function loadTwigClass($class) {
        // Twig 로 시작하는 클래스만 필터링한다.
        if (strpos($class, 'Twig_') !== 0) return;

        // 언더바 개수
        $n = substr_count($class, '_');

        $path = '';
        if ($n == 1) {
            $path = APPPATH . 'Libraries' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        } elseif ($n > 1) {
            $path = substr($class, 0, strrpos($class, '_'));
            $path = APPPATH . 'Libraries' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . 'Classes.php';
        }
        //echo $class . '<br>';
        //echo $path . '<br>';
        include_once($path);
    }

}
//Autoload::getInstance();
