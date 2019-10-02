<?php namespace Arakny\Libraries;

/**
 * Arakny Admin Page Library Class
 * 아라크니 관리자 페이지 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */
class AdminPage
{

	/** @var array $menu_main */
	public $menu_main = null;
	/** @var array $menu_sub */
	public $menu_sub = null;

	protected $page_title = null;

	protected $controller = null;
	protected $method = null;
	protected $params = null;

	/**
	 * Constructor
	 * 생성자
	 */
	public function __construct()
	{
		$this->controller = routerController();
		$this->method = routerMethod();
		$this->params = routerParams();

		// 네비게이션바 초기화
		$this->loadAdminNav();
	}

    /* -------------------------------------------------------------------------------- */

    /**
     * Load admin menu.
     * 관리자 메뉴를 초기화한다.
     */
    protected function loadAdminNav()
    {
		// 아래 기반 메뉴 정의.
		// - 일반 - 일반, 메뉴, 일반페이지
		// - 사용자 - 역할, 목록
		// - 게시판 - 목록

		// @todo 플러그인에서 정의하는 메뉴가 추가될 수 있도록 처리해야...

		// 주 메뉴
		$this->menu_main = [
			[ 'id' => 'home', 'icon_class' => 'home icon', 'link' => base_url('admin') ],
			[ 'id' => 'general', 'icon_class' => 'cogs icon' ],
			[ 'id' => 'user', 'icon_class' => 'user icon' ],
			[ 'id' => 'board', 'icon_class' => 'list ol icon' ],
		];

		// 하위 메뉴 (사실은 페이지 연동에 불과함)
		$this->menu_sub = [
			[ 'group' => 'general', 'id' => 'general' ],
			[ 'group' => 'general', 'id' => 'nav' ],
			[ 'group' => 'general', 'id' => 'docs' ],
			[ 'group' => 'general', 'id' => 'files' ],

			[ 'group' => 'user', 'id' => 'userroles' ],
			[ 'group' => 'user', 'id' => 'users' ],

			[ 'group' => 'board', 'id' => 'boards' ],
		];

		// 현재 페이지의 컨트롤러에 따라 현재 메뉴가 무엇인지를 표시.
		$page_id = $this->controller;
		$is_home = true;
		for ($i = 0; $i < count($this->menu_sub); $i++) {
			$item = $this->menu_sub[$i];

			if ('admin\\' . $item['id'] === $page_id) {
				$this->menu_sub[$i]['on'] = 'on';
				$this->page_title = _t('Admin.l_menu_sub_' . $item['id']);

				if ($this->method === 'write') {
					if (count($this->params) === 0) {
						$this->page_title .= ' - ' . _g('add');
					} else {
						$this->page_title .= ' - ' . _g('edit');
					}

				} else {
					$this->page_title .= ' ' . _g('settings');
				}

				
				for ($j = 0; $j < count($this->menu_main); $j++) {
					$main = $this->menu_main[$j];

					if ($main['id'] === $item['group']) {
						$this->menu_main[$j]['on'] = 'on';
						break;
					}
				}
				$is_home = false;
				break;
			}
		}

		// 현재페이지가 관리자 메인(홈) 페이지라면,
		if ($is_home) {
			$this->menu_main[0]['on'] = 'on';
		}

    }

	/* -------------------------------------------------------------------------------- */

	/**
	 * Return the page title.
	 * 페이지 제목을 반환한다.
	 *
	 * @return string
	 */
    public function getPageTitle()
	{
    	return $this->page_title;
	}

}
