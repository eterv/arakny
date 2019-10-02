<?php namespace Arakny\Filters;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Security\Exceptions\SecurityException;
use Config\Services;

/**
 * Authentication Guard Filter Class
 * 인증 가드 필터 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */
class Guard implements FilterInterface
{
	/**
	 * @inheritdoc
	 */
	public function before(RequestInterface $request)
	{
		if ($request->isCLI() || ! isInstalled()) {
			return null;
		}

		// 사용되지 않는 public 함수를 제외시킨다. (Bug 해결)
		$exceptMethods = [ 'initcontroller', 'cachepage', 'forcehttps', 'validate' ];
		if (in_array(routerMethod(), $exceptMethods)) {
			throw PageNotFoundException::forPageNotFound(null);
		}

        $auth = Services::auth();
		$isLoggedIn = $auth->isLoggedIn();

		if ($isLoggedIn) {      // 로그인 상태
			if (isAdminPage()) {

				// 작업중...

				if ($auth->isAdminLevel()) {
					// 관리자 페이지 라이브러리 로드
					Services::adminpage();

				} else {
					throw PageNotFoundException::forPageNotFound(null);
				}
			}

        } else {                // 비 로그인 상태
			if ( isAdminPage() && routerController() !== 'admin\install' ) {
				return _redirect('/users/login?redirect=' . current_url());
			}

        }

		//$this->test1();


        // 유저역할에 따른 퍼미션 작업
        // -- 아마도, DB 에는 특정 페이지 (컨트롤러의 메소드)에 대하여 허가/불허 작업이 필요함
        // 여기 가드 필터에서 그것을 걸러냄.



		try {
			//$security->CSRFVerify($request);
		}
		catch (SecurityException $e) {
			/*if (config('App')->CSRFRedirect && ! $request->isAJAX()) {
				//return redirect()->back()->with('error', $e->getMessage());
				return redirect()->back();
			}*/

			//throw $e;
		}

		return null;
	}

    /* -------------------------------------------------------------------------------- */

	/**
	 * @inheritdoc
	 */
	public function after(RequestInterface $request, ResponseInterface $response) { }

    /* -------------------------------------------------------------------------------- */

    protected function test1()
    {
        echo routerDirectory() . ' // ' . routerController() . ' // ' . routerMethod() . '<br>';
    }

}
