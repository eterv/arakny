<?php namespace Arakny\Controllers;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Config\Services;

/**
 * Home Controller Class
 * 홈 (홈페이지) 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Auth extends BaseController
{

    public function index()
	{
        $data = [];

        //return $this->theme->render('index', $data);
	}

	public function regenerate_captcha()
    {
        $this->ajaxPOST();

        $onlyNumber = inputPost('onlyNumber', false, FILTER_VALIDATE_BOOLEAN);
        $height = inputPost('height', 45);

		$captcha = Services::captcha()->generateCaptcha($onlyNumber, $height);
        $data = [
            'captcha' => $captcha,
        ];

        return $this->succeed($data);
    }

	//--------------------------------------------------------------------

}
