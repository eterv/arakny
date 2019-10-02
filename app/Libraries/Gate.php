<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;

// Authorization 인가 에 대한 체계를 이 게이트가 대신한다.
// 어떤 방법으로 처리할지는 라라벨을 참고하면서 작업한다.

/**
 * Gate Library Class
 * 게이트(입구) 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Gate
{

    /** @var \CodeIgniter\Session\Session $session */
    protected $session;

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {
        //$this->session = session();
    }

	/* -------------------------------------------------------------------------------- */
	/* 		Test (테스트)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Generate new captcha value and return the image data that is encoded with base64.
	 * 새 캡챠 값을 생성하고, BASE64 인코딩된 이미지 데이터를 반환한다.
	 *
	 * @return string
	 */
	public function abc()
	{
		// ...
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Protected Methods
	/* -------------------------------------------------------------------------------- */



}
