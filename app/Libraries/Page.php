<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use Arakny\Models\DocsModel;
use Config\Services;

/**
 * Page Library Class
 * 페이지 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Page
{

	/** @var Settings */
	protected $settings = null;

	/** @var string Page Type 페이지 형식 */
	protected $pageType = null;

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {
    	$this->settings = Services::settings();

    	// 페이지 형식 결정
		if (isAdminPage()) {
			$this->pageType = Consts::PAGETYPE_ADMIN;
		} else {
			if (empty(routerDirectory()) && routerController() === 'docs') {
				$this->pageType = Consts::PAGETYPE_DOC;
			} else if (empty(routerDirectory()) && routerController() === 'boards') {
				/** @todo 나중에 게시판을 완성 후에 재작업 필요.. 지금은 임시로... */
				$this->pageType = Consts::PAGETYPE_BOARD;
			}
		}
    }

	/* -------------------------------------------------------------------------------- */
	/* 		Public Methods (공용 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Returns whether current page is an admin page.
	 * 현재 페이지가 관리자 페이지인지 여부를 반환합니다.
	 *
	 * @return bool
	 */
	public function isAdminPage()
	{
		return ($this->pageType === Consts::PAGETYPE_ADMIN);
	}

	/**
	 * Returns whether current page is an board page.
	 * 현재 페이지가 게시판 페이지인지 여부를 반환합니다.
	 *
	 * @return bool
	 */
	public function isBoardPage()
	{
		return ($this->pageType === Consts::PAGETYPE_BOARD);
	}

	/**
	 * Returns whether current page is an general document page.
	 * 현재 페이지가 일반 문서 페이지인지 여부를 반환합니다.
	 *
	 * @return bool
	 */
	public function isDocPage()
	{
		return ($this->pageType === Consts::PAGETYPE_DOC);
	}

	/**
	 * Return the menu data.
	 * 메뉴 데이터를 반환한다.
	 *
	 * @return array
	 */
	public function getMenu()
	{
		return $this->generateMenuArray();
	}

	/**
	 * Return the page title.
	 * 페이지 제목을 반환한다.
	 *
	 * @return string
	 */
	public function getPageTitle()
	{
		$search = _underbarToDash(routerMethod());

		if ($this->isDocPage()) {
			$row = Services::docs()->getFirstRowWhere([ 'LOWER(' . DocsModel::d_name . ')' => $search ], [ DocsModel::d_title ]);
			if ($row === null) return '?';

			return $row[DocsModel::d_title];

		} else if ($this->isBoardPage()) {
			/** @todo 게시판 작업해야함... */
			return '작업중...';

		} else {
			return '?';
		}
	}

	/**
	 * Return the title in <head> tag.
	 * 페이지 <head> 태그 안의 제목을 반환한다.
	 *
	 * @return string
	 */
	public function getHeadTitle()
	{
		$title_format = $this->settings->get(Settings::title_format, '{{title}} - {{site_name}}');

		$search = [ '{{title}}', '{{site_name}}', '{{site_desc}}' ];
		$replace = [
			$this->getPageTitle(),
			$this->settings->get(Settings::name),
			$this->settings->get(Settings::desc),
		];
		$title = str_replace($search, $replace, $title_format);

		return $title;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Protected Methods (내부 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 스킨 제작자가 사용할 수 있는 메뉴 배열을 생성한다.
	 *
	 * @param null $menus
	 * @return mixed|null
	 */
	protected function generateMenuArray($menus = null)
	{
		if ($menus === null) {
			$json = $this->settings->get(Settings::menu, '[]');
			$json = str_replace('"m_', '"', $json);
			$menus = json_decode($json, true);
		}

		foreach ($menus as & $item) {
			$item['items'] = $this->generateMenuArray($item['items']);

			switch ($item['linktype']) {
				case Consts::MENU_LINKTYPE_NONE:
					$item['href'] = 'javascript:void(0)';
					$item['target'] = '';
					break;
				case Consts::MENU_LINKTYPE_TOPLEVELITEM:
					if (count($item['items']) > 0) {
						$item['href'] = $item['items'][0]['href'];
						$item['target'] = $item['items'][0]['target'];
					} else {
						$item['href'] = 'javascript:void(0)';
						$item['target'] = '';
					}
					break;
				case Consts::MENU_LINKTYPE_DOCS:
					$item['href'] = '/docs/' . $item['link'];
					break;
				case Consts::MENU_LINKTYPE_BOARDS:

					// 작업중...

					$item['href'] = 'javascript:void(0)';
					break;
				case Consts::MENU_LINKTYPE_URL:
					$item['href'] = $item['link'];
					break;
			}

			$item['label'] = _e($item['label']);
			$item['target_attr'] = isset($item['target']) && ! empty($item['target']) ? 'target="' . $item['target'] . '"' : '';
		}
		unset($item);

		return $menus;
	}

}
