<?php namespace Arakny\Libraries;

use Arakny\Models\UserRolesModel;
use Arakny\Models\UsersModel;
use CodeIgniter\Security\Exceptions\SecurityException;
use Config\Services;
use Exception;
use Firebase\JWT;

// 추후 작업...
// 사용자 암호 변경시... - 사용자의 모든 세션 / 쿠키 / 토큰 제거  (보안작업)

// 고려 사항 :: 정말 토큰과 DB 연동은 크게 필요없는가 다시한번 점검. Login Table 의 실효성은? Stat 으로 대체가능한 것인지??

/** @todo 일반 사용자 / 관리자계열 구분이 문제 없는지 확실한 테스트 필요 */

/**
 * User Authentication Library Class
 * 사용자 인증 라이브러리 클래스
 *
 * Using JWT & Encryption - Stateless / Sessionless
 *
 * [Reference]
 * -- https://tansfil.tistory.com/58 - JWT
 * -- https://idchowto.com/?p=5747
 *
 * 참고:: 2019-02-06 - CI4 alpha5 response - 내부 CI Response deleteCookie - 작동에 버그가 있음.
 *
 * 리멤버 토큰 - 1달
 * 일반 엑세스(로그인) 토큰 - 브라우저 닫힐때까지
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Authentication
{
    protected $tokenName = 'uat';
    protected $rememberCookieName = 'urt';

    protected $tokenExpire = 0;		// 0 :: Until the browser is closed
	protected $rememberCookieExpire = 86400 * 30;

	/** @var UserRolesModel $userroles */
	protected $userroles;
    /** @var UsersModel $users */
    protected $users;

    /** @var bool $isLoggedIn */
    protected $isLoggedIn = false;

    /** @var int $u_id */
    protected $u_id = 0;

	/** @var string $u_login */
    protected $u_login = '';

	/** @var int $u_ur_id */
    protected $u_ur_id = 0;

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {
        $this->userroles = Services::userroles();
        $this->users = Services::users();

		// 현재 로그인 상태인지 여부를 초기화한다.
		$this->checkLoggedIn();

		// 현재 로그인 되어 있지 않다면, 자동 로그인 토큰 쿠키의 존재 여부를 확인한다.
		if (! $this->isLoggedIn) $this->checkRememberCookie();
    }


	/* -------------------------------------------------------------------------------- */
	/* 		Public Methods (공용 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 현재 사용자 nid 를 반환한다.
	 *
	 * @return int
	 */
    public function getCurrentUserId()
	{
		return $this->u_id;
	}

	/**
	 * 현재 로그인한 사용자가 '슈퍼관리자' 또는 '관리자' 인지 여부를 반환한다.
	 *
	 * @return bool
	 */
	public function isAdminLevel()
	{
		return $this->isAdmin();
	}

	/**
	 * 현재 로그인한 사용자의 역할이 '관리자'인지 여부를 반환한다.
	 *
	 * @return bool
	 */
	public function isAdminRole()
	{
		return $this->isAdmin('admin');
	}

	/**
	 * Determine if the current user is a guest.
	 * 현재 사용자가 방문자인지 여부를 알아낸다.
	 *
	 * @return bool
	 */
	public function isGuest()
	{
		return ! $this->isLoggedIn;
	}

    /**
     * Determine if the current user is already logged in.
     * 현재 사용자가 이미 로그인 되어 있는지 여부를 알아낸다.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
		return $this->isLoggedIn;
    }

	/**
	 * 현재 로그인한 사용자의 역할이 '슈퍼관리자'인지 여부를 반환한다.
	 *
	 * @return bool
	 */
	public function isSuperAdminRole()
	{
		return $this->isAdmin('superadmin');
	}

    /**
     * Log in.
     * 로그인.
     *
     * @param string $u_login
     * @param string $u_pass
     * @param bool $remember
	 * @return bool
     */
    public function login($u_login, $u_pass, $remember = false)
    {
        if ( empty($u_login) || empty($u_pass) ) {
            return false;
        }

        // 로그인 아이디가 DB 에 존재하는지, 탈퇴한 사용자가 아닌지 체크한다.
        if ($this->users->isLoginPossible($u_login) === false) {
            return false;
        }
        // 필요한 사용자 정보를 DB 에서 가져온다.
        $this->users->select([ UsersModel::u_id, UsersModel::u_login, UsersModel::u_pass, UsersModel::u_ur_id ]);
        $this->users->where(UsersModel::u_login, $u_login);
        $query = $this->users->get();
        $user = $query->getRowArray();
		$u_id = $user[UsersModel::u_id];

        // 비밀번호 해쉬를 비교한다.
        // 참조 : https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
        $result = password_verify( base64_encode( hash( 'sha256', $u_pass, true ) ), $user[UsersModel::u_pass] );
        if ($result === false) {
            return false;
        }

        // 인증에 통과

		// 자동 로그인을 사용한다면, 자동 로그인 토큰 쿠키 생성
		if ($remember) {
			$this->generateRememberToken($u_id);
		}

		// 로그인 토큰 생성 (쿠키)
		$this->generateAccessToken($u_id);

		// 사용자 로그인 처리
		$this->setLoginInfo($u_id);

        return true;
    }

    /**
     * Log out.
     * 로그아웃.
     *
     * @return bool
     */
    public function logout()
    {
        if ($this->isLoggedIn) {
            // 로그인 쿠키 제거
			//   참고!!! 여기서 제거해달라는 응답 요청을 넣지만,
			//   만약 컨트롤러 내에서 redirect 등으로 응답객체가 바뀐다면 거기에서 수동으로 넣어줘야함.
            //

			_deleteCookie($this->tokenName);
			_deleteCookie($this->rememberCookieName);

			// 사용자 로그아웃 처리
			$this->unsetLoginInfo();
        }

        return true;
    }

    /* -------------------------------------------------------------------------------- */
    /* 		Protected Methods (내부 메소드)
    /* -------------------------------------------------------------------------------- */

	/**
	 * Return if now user is already logged in. (Internal method)
	 * 현재 이미 로그인 되어 있는지 여부를 반환한다. (내부메소드)
	 */
	protected function checkLoggedIn() {
		// 토큰 쿠키를 찾는다.
		$cookieName = $this->tokenName;
		$encToken = _getCookie($cookieName);
		if ($encToken === null) return false;

		try {
			// 암호화된 토큰을 복호화 시도한 다음, 실패시 쿠키 삭제
			$token = _decrypt($encToken);
			if (! $token) throw new SecurityException();

			$data = (array) JWT\JWT::decode($token, getSetting(Settings::auth_jwt_key), ['HS256']);

			$uid = (int) $data['uid'];

			// 현재 브라우저와 쿠키의 브라우저명이 다르다면, 쿠키 탈취 (XSS)
			$currentAgent = service_useragent()->getBrowser();
			$uan = $data['uan'];
			if ($currentAgent !== $uan) throw new SecurityException();

		} catch (JWT\ExpiredException $ex) {	// 토큰 유효기간 지남
			_deleteCookie($cookieName);
			return false;

		} catch (Exception $ex) {				// 기타 예외 (보안 예외, 서명 유효하지 않음 등)
			log_message('alert', '로그인 쿠키 토큰의 데이터가 올바르지 않거나 탈취되었습니다. 보안 강화 필요.');
			log_message('alert', $_SERVER['PHP_SELF'] . ' -- ' . routerController());
			_deleteCookie($cookieName);
			return false;
		}

		// 사용자 로그인 처리
		$this->setLoginInfo($uid);

		return true;
	}

	/**
	 * Find Remember-me token cookie, If the cookie exists and be valid, then will be logged in automatically.
	 * 자동 로그인 토큰 쿠키를 찾고, 올바른 데이터라면 자동으로 로그인한다.
	 *
	 * @return bool
	 */
	protected function checkRememberCookie()
	{
		// 자동 로그인 토큰 쿠키를 찾는다.
		$cookieName = $this->rememberCookieName;
		$encToken = _getCookie($cookieName);
		if ($encToken === null) return false;

		try {
			// 암호화된 토큰을 복호화 시도한 다음, 실패시 쿠키 삭제
			$token = _decrypt($encToken);
			if (! $token) throw new SecurityException();

			$data = (array) JWT\JWT::decode($token, getSetting(Settings::auth_jwt_key), ['HS256']);

			$uid = (int) $data['uid'];

			// 현재 브라우저와 쿠키의 브라우저명이 다르다면, 쿠키 탈취 (XSS)
			$currentAgent = service_useragent()->getBrowser();
			$uan = $data['uan'];
			if ($currentAgent !== $uan) throw new SecurityException();

		} catch (JWT\ExpiredException $ex) {	// 토큰 유효기간 지남
			_deleteCookie($cookieName);
			return false;

		} catch (Exception $ex) {				// 기타 예외 (보안 문제, 서명 유효하지 않음 등)
			log_message('alert', '자동 로그인 쿠키 토큰의 데이터가 올바르지 않거나 탈취되었습니다. 보안 강화 필요.');
			_deleteCookie($cookieName);
			return false;
		}

		// 자동 로그인 토큰이 유효하므로, 로그인 토큰을 생성 (쿠키에 저장)
		$this->generateAccessToken($uid);

		// 사용자 로그인 처리
		$this->setLoginInfo($uid);

		return true;
	}

	/**
	 * Generate new access token, then save it in cookie storage.
	 * 새 로그인 토큰을 생성하고 쿠키로 저장한다.
	 *
	 * @param int $uid
	 * @return bool
	 */
	protected function generateAccessToken($uid)
	{
		// Login 테이블에 로그인 정보 추가
		/*$insertData = [
			LoginModel::lo_u_id => $uid,
		];
		$id = $this->login->addAndGetId($insertData);
		if (! $id) {
			return false;
		}*/

		// Random ID - 12 bytes random string
		//   -- 암호화 했을때 매번 다른 문자열이 나오도록 데이터의 차별화를 두는 의도.
		// 참고) base64_encode 결과 공식 -- 인코딩 문자열 bytes = ( 인수 문자열의 bytes / 3 ) * 4
		// 		 단, 괄호 안의 값이 소수점이 나오면 올림하여 계산
		//		 따라서 9 바이트 문자열이면 12 바이트가 나옴
		//$randomID = base64_encode(_randomBytes(9));

		// JWT 를 생성하고 암호화한다.
		$data = [
			'iss' => BASEURL,
			'iat' => time(),
			//'exp' => time() + 3600,
			//'jti' => $randomID,

			'uid' => $uid,
			'uan' => service_useragent()->getBrowser(),
		];
		$token = JWT\JWT::encode($data, getSetting(Settings::auth_jwt_key), 'HS256');
		$result = _encrypt($token);

		// 쿠키 설정
		$cookie = [
			'name' => $this->tokenName,
			'value' => $result,
			'expire' => $this->tokenExpire,
			'httponly' => true,
		];
		_setCookie($cookie);

		return true;
	}

	/**
	 * Generate new Remember-Me token, then save it in cookie storage.
	 * 새로운 자동 로그인 토큰을 생성하고 쿠키로 저장한다.
	 *
	 * @param int $uid
	 * @return bool
	 */
	protected function generateRememberToken($uid)
	{
		// JWT 를 생성하고 암호화한다.
		$data = [
			'iss' => BASEURL,
			'iat' => time(),
			'exp' => time() + $this->rememberCookieExpire,

			'uid' => $uid,
			'uan' => service_useragent()->getBrowser(),
		];
		$token = JWT\JWT::encode($data, getSetting(Settings::auth_jwt_key), 'HS256');
		$result = _encrypt($token);

		// 쿠키 설정
		$cookie = [
			'name' => $this->rememberCookieName,
			'value' => $result,
			'expire' => $this->rememberCookieExpire,
			'httponly' => true,
		];
		_setCookie($cookie);

		return true;
	}

	/**
	 * 관리자(또는 슈퍼관리자)인지 여부를 반환한다.
	 *
	 * @param string $name
	 * @return bool
	 */
	protected function isAdmin($name = 'both')
	{
		if (! $this->isLoggedIn) {
			return false;
		}

		$ur_name = $this->users->getUserRoleName($this->u_id);

		if ($name === 'both') {
			return ($ur_name === 'superadmin' || $ur_name === 'admin');
		} else if ($name === 'superadmin' || $name === 'admin') {
			return ($ur_name === $name);
		} else {
			return false;
		}
	}

	/**
	 * 로그인 상태로 변수의 값을 지정한다.
	 *
	 * @param int $u_id
	 */
    protected function setLoginInfo($u_id)
	{
		$userdata = $this->users->getRowFromId($u_id, [ UsersModel::u_id, UsersModel::u_login, UsersModel::u_ur_id ]);

    	$this->u_id = $u_id;
    	$this->u_login = $userdata[UsersModel::u_login];
    	$this->u_ur_id = $userdata[UsersModel::u_ur_id];

		$this->isLoggedIn = true;
	}

	/**
	 * 로그인 상태를 해제한다.
	 */
	protected function unsetLoginInfo()
	{
		$this->u_id = 0;
		$this->isLoggedIn = false;
	}

}
