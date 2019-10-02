<?php defined('_ARAKNY_') OR exit;

if ( ! function_exists( '_adminSubHead' ) )
{
    /**
     * 관리자 페이지의 서브 헤더 코드.
     */
    function _adminSubHead()
    {
    	return '<h4 class="ui fw-regular header">'. _pageTitle() .' 설정 수정해야합니다~~</h4>';
    }
}

if ( ! function_exists( '_adminSubHeader' ) )
{
	/**
	 * 관리자 페이지 서브 헤더의 코드를 반환한다.
	 *
	 * @param array $buttons
	 * @return string
	 */
	function _adminSubHeader($buttons)
	{
		$code = '<div class="sub-header">' . PHP_EOL
			. "\t<h4 class=\"ui fw-regular header\">" . _pageTitle() . '</h4>' . PHP_EOL
			. "\t<div class=\"ui compact icon buttons\">" . PHP_EOL;

		foreach ($buttons as $item) {
			$class = $item['class'];
			$title = $item['title'];
			$icon = $item['icon'];
			$menu = $item['menu'] ?? '';
			$code .= "\t\t<div class=\"ui button $class\" title=\"$title\"><i class=\"$icon icon\"></i>$menu</div>" . PHP_EOL;
		}

		$code .= "\t</div>" . PHP_EOL
			. "</div>" . PHP_EOL;

		return $code;
	}
}

if ( ! function_exists( '_adminIconButton' ) )
{
	/**
	 * 관리자 서브 헤더의 버튼을 정의한다.
	 *
	 * @param string $class
	 * @param string $title
	 * @param string $icon
	 * @return array
	 */
	function _adminIconButton($class, $title = '', $icon = '')
	{
		$key = $class;
		switch ($key) {
			case 'add':
				$class = 'btn-add positive';
				$title = _g($key);
				$icon = $key;
				break;
			case 'back':
				$class = 'btn-back';
				$title = _g($key);
				$icon = 'arrow left';
				break;
			case 'cancel':
				$class = 'btn-cancel';
				$title = _g($key);
				$icon = $key;
				break;
			case 'delete':
				$class = 'btn-delete';
				$title = _g($key);
				$icon = $key;
				break;
			case 'ok':
				$class = 'primary btn-ok';
				$title = _g($key);
				$icon = 'check';
				break;
			case 'save':
				$class = 'primary btn-save';
				$title = _g($key);
				$icon = 'check';
				break;
		}
		return [
			'class' => $class,
			'title' => $title,
			'icon' => $icon,
		];
	}
}

if ( ! function_exists( '_adminIconDropdownButton' ) )
{
	/**
	 * 관리자 서브 헤더의 버튼을 정의한다.
	 *
	 * @param string $class
	 * @param string $title
	 * @param string $icon
	 * @param array $items
	 * @return array
	 */
	function _adminIconDropdownButton($class, $title, $icon, $items)
	{
		$menu = '<div class="menu">';
		foreach ($items as $item) {
			$menu .= $item;
		}
		$menu .= '</div>';

		return [
			'class' => 'pointing top left dropdown ' . $class,
			'title' => $title,
			'icon' => $icon,
			'menu' => $menu,
		];
	}
}

if ( ! function_exists( '_adminDropdownItem' ) )
{
	/**
	 * 관리자 서브 헤더의 드롭다운 버튼의 목록 아이템을 정의한다.
	 *
	 * @param string $value
	 * @param string $content
	 * @param array $data
	 * @return string
	 */
	function _adminDropdownItem($value, $content, $data = null)
	{
		if (is_null($data)) $data = [];
		if ($value === null && $content === null) {
			return '<div class="divider"></div>';
		}

		$dataValue = isset($value) ? "data-value=\"$value\"" : '';

		$code = "<div class=\"item\" $dataValue >$content</div>";
		return $code;
	}
}
