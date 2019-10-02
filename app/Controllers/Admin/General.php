<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
use Arakny\Libraries\Settings as S;
use Arakny\Models\DocsModel as M;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Admin General Config Controller Class
 * 관리자 일반 설정 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class General extends BaseController
{
	protected $eventGroupName = 'Admin.General';

    /**
     * @inheritdoc
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * 설정 페이지
     */
    public function index()
	{
		// 작업중...

		$keys = [
			S::name, S::desc, S::locale, S::theme, S::title_format,
			S::date_format, S::time_format,
			S::admin_email, S::admin_locale,
			S::users_default_ur_id, S::use_nickname, S::use_gender, S::use_birthdate,
		];
		$arr = [];
		foreach ($keys as $key) {
			$arr['s_' . $key] = $this->settings->get($key);
		}

		$fields = [
			'siteinfo' => [
				_adminFieldText('s_'.S::name, $arr, [ 'required' ]),
				_adminFieldText('s_'.S::desc, $arr, [ 'required' ]),
				[
					_adminFieldSelect('s_'.S::locale, $arr, 'locale'),
					_adminFieldSelect('s_'.S::admin_locale, $arr, 'locale'),
				],
				_adminFieldSelect('s_'.S::theme, $arr, 'theme')
			],
			'format' => [
				_adminFieldText('s_'.S::title_format, $arr, [ 'required' ]),
				[
					_adminFieldText('s_'.S::date_format, $arr, [ 'required' ]),
					_adminFieldText('s_'.S::time_format, $arr, [ 'required' ]),
				],
			],
			'admin' => [
				_adminFieldEmail('s_'.S::admin_email, $arr),
			],
			'user' => [
				_adminFieldSelect('s_'.S::users_default_ur_id, $arr, 'userroles_notadmin', [ 'required' ]),
				_adminFieldGroup('s_users_fields', null, [
					_adminFieldBoolToggle('s_'.S::use_nickname, $arr, [ 'control' ]),
					_adminFieldBoolToggle('s_'.S::use_gender, $arr, [ 'control' ]),
					_adminFieldBoolToggle('s_'.S::use_birthdate, $arr, [ 'control' ]),
				]),
			],

		];

		$data = [
			'data' => $arr,
			'fields' => $fields,
			'urlSubmit' => adminUrl("general/save"),
		];
        return $this->theme->renderAdminPage('general', $data);
	}

	/**
	 * Save / 저장
	 */
	public function save()
	{
		$this->ajaxPOST();

		$postDatas = inputPost();
		$settingDatas = [];
		foreach ($postDatas as $key => $value) {
			if (_startsWith($key, 's_')) {
				$newKey = substr($key, 2);
				$settingDatas[$newKey] = $value;

				$this->settings->set($newKey, $value);
			}
		}

		// 작업중...


		$data = [

		];
		return $this->succeed($data);
	}

}
