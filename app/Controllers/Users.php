<?php namespace Arakny\Controllers;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Libraries\Settings as S;
use Arakny\Models\UsersModel as M;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Administration Controller Class
 * 관리자 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Users extends BaseController
{
	protected $eventGroupName = 'Admin.Users';

	/** @var M $model */
	protected $model = null;

    protected $data = [];

	/**
	 * @inheritdoc
	 */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

		$this->model = Services::users();
    }

    /**
     * Fist Page - Installation Step 1 - Select a default language
     * 첫 페이지 - 설치 단계 1 - 기본 언어 선택
     */
    public function index()
    {

    }



	public function login()
    {
        // 이미 로그온 되어 있는지 여부를 확인
        if ($this->auth->isLoggedIn()) {
            return redirect()->back();
        }

        /*$data['a1'] = '로그인 입니다.<br>하하하';
        $data['test'] = '테스트';
        $data['w'] = WRITEPATH . 'cache/twig';
        $data['arr'] = [ 'a' => 'a123', 'b' => 'b456' ];*/
        $data = [];

        $data['redirect'] = inputGet('redirect') ?? base_url();

        return $this->theme->render('core/'.$this->controllerName.'-'.__FUNCTION__, $data);
    }

    public function login_process() {
        $this->ajaxPOST();

        // 이미 로그온 되어 있는지 여부를 확인한 후, 되어 있으면, 실패 - 잘못된 요청
        if ($this->auth->isLoggedIn()) {
            return $this->fail(_g(Consts::E_INVALID_REQUEST));
        }

        $input = [];
        $post_keys = [ 'uid', 'upw', 'remember' ];
        $require_keys = [ 'uid', 'upw' ];
        foreach ($post_keys as $key) {
            $input[$key] = inputPost($key);
            if ( in_array($key, $require_keys) && $input[$key] === null ) {
                return $this->fail(_g(Consts::E_INVALID_REQUEST));
            }
        }
        $input['remember'] = $input['remember'] ?? false;

        // 로그인 후 바로 이동 페이지 지정 
        $data['redirect'] = inputPost('redirect') ?? base_url();

        try {
            $result = $this->auth->login($input['uid'], $input['upw'], $input['remember']);
            if (! $result) {
                return $this->fail(_gr(Consts::E_AUTH_LOGIN_FAILURE));
            }
        } catch (\Exception $ex) {
            return $this->fail(_g(Consts::E_INVALID_REQUEST));
        }

        return $this->succeed($data);
    }

    public function logout() {
        $this->auth->logout();

        $url = inputGet('url') ?? '/';

        // CodeIgniter\Http\Uri 참고...
        // 그누보드 참고하여, url 에 scheme 있는지 체크 (http(s):// 와 같은..)

		// 지정한 url 로 이동
		return $this->response->redirect($url);
    }

    public function isUnique() {
		$this->ajaxPOST();

		//_log('checkid :: ' . _referrer());

		$rules = [
			'field' => 'required|in_list[u_login,u_email,u_nickname]',
			'value' => 'required',
		];
		if (! $this->validate($rules)) return $this->failValidation();

		$field = inputPost('field');
		$value = inputPost('value');

		if ($field === M::u_login) {
			$result = $this->model->isLoginUnique($value);
		} else if ($field === M::u_email) {
			$result = $this->model->isEmailUnique($value);
		} else if ($field === M::u_nickname) {
			$result = $this->model->isNicknameUnique($value);
		} else {
			return $this->failErrorInvalidRequest();
		}

		$data = [
			'isUnique' => $result,
		];
		return $this->succeed($data);
	}

    public function signup() {
        // 회원가입

		$fields = [
			M::u_login, M::u_pass, 'u_pass_check', M::u_name,
		];
		if (getSetting(S::use_nickname)) $fields[] = M::u_nickname;
		$fields[] = M::u_email;
		$fields[] = M::u_phone;
		if (getSetting(S::use_gender)) $fields[] = M::u_gender;
		if (getSetting(S::use_birthdate)) $fields[] = M::u_birthdate;

		$rules = [];
		foreach ($fields as $field) {
			$rules[$field] = $this->model->getValidationRule($field);
			if ($field === M::u_login || $field === M::u_nickname || $field === M::u_email) {
				$rules[$field]['rules'] .= '|unique[' . $this->controllerName . '/isUnique,'. M::u_id .']';
			}
		}

		$choosableItems = [
			M::u_gender => getPreparedListForSelect('gender'),
		];

        $data = [
			'fields' => $fields,
			'choosableItems' => $choosableItems,
			'rules' => $rules,
			'redirect' => inputGet('redirect') ?? baseUrlOnlyPath(),
			'urlSubmit' => _url('users/signup-process'),
		];

        return $this->theme->render('core/'.$this->controllerName.'-'.__FUNCTION__, $data);
    }

    public function signup_process()
	{
		if (! Services::captcha()->testCaptchaByPostData()) {
			return $this->fail(_g(Consts::E_INVALID_CAPTCHA));
		}

		$data = [

		];
		return $this->succeed($data);
	}


	// 테스트...

	public function formtest() {
    	$data = [];
		return $this->theme->render('core/'.$this->controllerName.'-'.__FUNCTION__, $data);
	}
	public function formtest2() {
		$data = [];
		return $this->theme->render('core/'.$this->controllerName.'-'.__FUNCTION__, $data);
	}

}