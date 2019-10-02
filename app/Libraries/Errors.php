<?php namespace Arakny\Libraries;

use CodeIgniter\Exceptions;
use Arakny\BaseController;

/**
 * 404 Error Controller Class
 * 404 Page Not Found Error 발생시 처리할 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Errors extends BaseController
{

    /**
     * Show 404 error. (Page not found)
     */
    public function show404()
    {
        throw Exceptions\PageNotFoundException::forPageNotFound(null);
	}

}
