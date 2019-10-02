<?php namespace Arakny\Controllers;

use Arakny\BaseController;

/**
 * Home Controller Class
 * 홈 (홈페이지) 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Home extends BaseController
{

    public function index()
	{
        $data = [];

        return $this->theme->render('index', $data);
	}

	//--------------------------------------------------------------------

}
