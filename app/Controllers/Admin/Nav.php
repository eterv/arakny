<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Libraries\Settings;
use Arakny\Support\Format;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

// @todo Library 의 Page 클래스를 추가한후, getMenu(), getPageTitle() 등을 작성해야 하지 않을까 싶다.

/**
 * Admin Navigation-Bar Config Controller Class
 * 관리자 메뉴설정 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Nav extends BaseController
{
	protected $eventGroupName = 'Admin.Menu';

	/* Fields - BEGIN */

	const m_id = 'm_id';
	const m_label = 'm_label';

	const m_linktype = 'm_linktype';
	const m_link = 'm_link';
	const m_target = 'm_target';

	const m_parent = 'm_parent';

	const m_items = 'm_items';

	/* Fields - END */

	/* -------------------------------------------------------------------------------- */

    /**
     * @inheritdoc
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

	/**
	 * Index page
	 * 인덱스 페이지
	 */
    public function index()
	{
		// 작업중...

		$data = [
			'list' => $this->getMenus(),
		];
        return $this->theme->renderAdminPage('nav', $data);
	}

	/* -------------------------------------------------------------------------------- */

	/**
	 * Add (추가)
	 *
	 * @return mixed
	 */
	public function add()
	{
		$this->ajaxPOST();

		// 데이터, 유효성 검증 규칙
		$datas = [];
		$datas['data'] = inputPost();
		$datas['rules'] = [
			static::m_parent => 'required|is_natural',
			static::m_label => 'required|max_length[64]',
			static::m_linktype => 'required|in_list[0,t,d,b,u]',
		];

		/** 이벤트 -- 추가 전 */
		$datas = $this->triggerFilter('beforeAdd', $datas);
		if (! $datas) return $this->failError();

		// 유효성 검사
		if (! $this->validate($datas['rules'])) {
			return $this->failValidation();
		}

		// 새 메뉴 아이템 생성
		$datas['row'] = [
			static::m_id => $this->getNewMenuId(),
			static::m_label => $datas['data'][static::m_label],

			static::m_linktype => $datas['data'][static::m_linktype],
			static::m_link => $datas['data'][static::m_link] ?? null,
			static::m_target => $datas['data'][static::m_target] ?? null,

			static::m_parent => $datas['data'][static::m_parent],

			static::m_items => [],
		];

		// 메뉴에 추가
		$menus = $this->getMenus();
		$found = false;
		if ($datas['data'][static::m_parent] > 0) {
			foreach ($menus as & $menu) {
				if ($menu[static::m_id] == $datas['data'][static::m_parent]) {
					$menu[static::m_items][] = $datas['row'];
					$found = true; break;

				} else {
					foreach ($menu[static::m_items] as & $menu2) {
						if ($menu2[static::m_id] == $datas['data'][static::m_parent]) {
							$menu2[static::m_items][] = $datas['row'];
							$found = true; break;
						}
					}
					unset($menu2);
					if ($found) break;
				}
			}
			unset($menu);

			if (! $found) {
				$menus[] = $datas['row'];
			}

		} else {
			$menus[] = $datas['row'];
		}
		$this->setMenus($menus);

		/** 이벤트 -- 추가 후 */
		$datas = $this->triggerFilter('afterAdd', $datas);
		if (! $datas) return $this->failError();

		$data = [
			'id' => $datas['row'][static::m_id],
			'parentid' => $datas['row'][static::m_parent],
			'row' => $datas['row'],
		];
		return $this->succeed($data);
	}

	/**
	 * Edit (수정)
	 *
	 * @return mixed
	 */
	public function edit()
	{
		$this->ajaxPOST();

		// 데이터, 유효성 검증 규칙
		$datas = [];
		$datas['data'] = inputPost();
		$datas['rules'] = [
			static::m_id => 'required|is_natural_no_zero',
			static::m_label => 'required|max_length[64]',

			static::m_linktype => 'required|in_list[0,t,d,b,u]',

			static::m_parent => 'required|is_natural',
		];

		/** 이벤트 -- 수정 전 */
		$datas = $this->triggerFilter('beforeEdit', $datas);
		if (! $datas) return $this->failError();

		// 유효성 검사
		if (! $this->validate($datas['rules'])) {
			return $this->failValidation();
		}

		// 메뉴 아이템 행 생성
		$datas['row'] = [
			static::m_label => $datas['data'][static::m_label],

			static::m_linktype => $datas['data'][static::m_linktype],
			static::m_link => $datas['data'][static::m_link] ?? null,
			static::m_target => $datas['data'][static::m_target] ?? null,

			static::m_parent => $datas['data'][static::m_parent],
		];

		$id = $datas['data'][static::m_id];

		// 수정
		$menus = $this->getMenus();
		$found = false;
		foreach ($menus as $i => $menu) {
			if ($menu[static::m_id] == $id) {
				$menus[$i] = array_merge($menu, $datas['row']);
				$datas['row'] = $menus[$i];
				$found = true; break;

			} else {
				foreach ($menu[static::m_items] as $i2 => $menu2) {
					if ($menu2[static::m_id] == $id) {
						$menus[$i][static::m_items][$i2] = array_merge($menu2, $datas['row']);
						$datas['row'] = $menus[$i][static::m_items][$i2];
						$found = true; break;

					} else {
						foreach ($menu2[static::m_items] as $i3 => $menu3) {
							if ($menu3[static::m_id] == $id) {
								$menus[$i][static::m_items][$i2][static::m_items][$i3] = array_merge($menu3, $datas['row']);
								$datas['row'] = $menus[$i][static::m_items][$i2][static::m_items][$i3];
								$found = true; break;
							}
						}
						if ($found) break;
					}
				}
				if ($found) break;
			}
		}
		if ($found)	$this->setMenus($menus);

		/** 이벤트 -- 수정 후 */
		$datas = $this->triggerFilter('afterEdit', $datas);
		if (! $datas) return $this->failError();

		$data = [
			'row' => $datas['row'],
		];
		return $this->succeed($data);
	}

	/**
	 * Delete (삭제)
	 *
	 * @return mixed
	 */
	public function delete()
	{
		$this->ajaxPOST();

		// 데이터, 유효성 검증 규칙
		$datas = [];
		$datas['data'] = inputPost();
		$datas['rules'] = [
			static::m_id => 'required|is_natural_no_zero',
		];

		/** 이벤트 -- 삭제 전 */
		$datas = $this->triggerFilter('beforeDelete', $datas);
		if (! $datas) return $this->failError();

		// 유효성 검사
		if (! $this->validate($datas['rules'])) {
			return $this->failValidation();
		}

		$id = $datas['data'][static::m_id];

		// 삭제
		$menus = $this->getMenus();
		$found = false;
		foreach ($menus as $i => $menu) {
			if ($menu[static::m_id] == $id) {
				unset($menus[$i]);
				$found = true; break;

			} else {
				foreach ($menu[static::m_items] as $i2 => $menu2) {
					if ($menu2[static::m_id] == $id) {
						unset($menus[$i][static::m_items][$i2]);
						$found = true; break;

					} else {
						foreach ($menu2[static::m_items] as $i3 => $menu3) {
							if ($menu3[static::m_id] == $id) {
								unset($menus[$i][static::m_items][$i2][static::m_items][$i3]);
								$found = true; break;
							}
						}
						if ($found) break;
					}
				}
				if ($found) break;
			}
		}
		if ($found)	$this->setMenus($menus);

		/** 이벤트 -- 삭제 후 */
		$datas = $this->triggerFilter('afterDelete', $datas);
		if (! $datas) return $this->failError();

		$data = [];
		return $this->succeed($data);
	}

	/**
	 * Save (저장 / 확인)
	 *
	 * @return mixed
	 */
	public function save()
	{
		$this->ajaxPOST();

		// 데이터, 유효성 검증 규칙
		$datas = [];
		$datas['data'] = inputPost();
		$datas['rules'] = [
			'menu' => 'required|valid_json',
		];

		/** 이벤트 -- 저장 전 */
		$datas = $this->triggerFilter('beforeSave', $datas);
		if (! $datas) {
			return $this->failError();
		}

		// 유효성 검사
		if (! $this->validate($datas['rules'])) {
			return $this->failValidation();
		}

		// 메뉴 JSON 데이터 저장
		$jsondata = inputPost('menu');
		$menus = json_decode($jsondata, true);

		$mainmenu = [];
		foreach($menus as $menu) {
			$mainmenu[] = $menu[static::m_id];
		}

		setSetting(Settings::menu, $jsondata);

		/** 이벤트 -- 저장 후 */
		$result = $this->triggerFilter('afterSave', $datas);
		if (! $result) {
			return $this->failError();
		}

		$data = [
			'result' => $mainmenu,
		];
		return $this->succeed($data);
	}

	/**
	 * Get a row from id
	 * ID 값으로부터 한 행을 가져온다.
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function fromId(int $id = null)
	{
		$this->ajaxGET();

		// id -- 0을 포함하지 않는 자연수만 허용
		if (! Format::isNaturalNumber($id, false)) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		// id 에 해당하는 하나의 전체 행을 가져온다.
		$row = $this->getRowFromId($id);

		if ($row === null) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		$data = [
			'row' => $row,
		];
		return $this->succeed($data);
	}

	private function getRowFromId($id, $menuArray = null)
	{
		if ($menuArray === null) $menuArray = $this->getMenus();
		$row = null;
		foreach ($menuArray as $menu) {
			if ($id == $menu[static::m_id]) {
				//return $this->arrayDataWithM($menu);
				return $menu;

			} else {
				$row = $this->getRowFromId($id, $menu[static::m_items]);
				if ($row) return $row;
			}
		}
		return $row;
	}

	private function getNewMenuId($menus = null)
	{
		if ($menus === null) $menus = $this->getMenus();

		$i = 0;
		foreach ($menus as $menu) {
			if (intval($menu[static::m_id]) > $i) $i = $menu[static::m_id];

			foreach ($menu[static::m_items] as $menu2) {
				if (intval($menu2[static::m_id]) > $i) $i = $menu2[static::m_id];

				foreach ($menu2[static::m_items] as $menu3) {
					if (intval($menu3[static::m_id]) > $i) $i = $menu3[static::m_id];
				}
			}
		}
		return ($i + 1);
	}

	private function getMenus()
	{
		return json_decode($this->settings->get(Settings::menu, '[]'), true);
	}

	private function setMenus($menuArr)
	{
		$this->settings->set(Settings::menu, json_encode($menuArr));
	}

}
