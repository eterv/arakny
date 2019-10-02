<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use Arakny\Models\DocsModel as M_d;
use Arakny\Models\UserRolesModel as M_ur;
use Arakny\Support\Format;
use Config\Services;

/**
 * HTML Tag Code Library Class
 * HTML 태그 코드 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Html
{

	/** Available Asset Types */
	const ASSET_TYPE_SCRIPT = 'script';
	const ASSET_TYPE_STYLE = 'style';

	/** Available Field Types */
	const FIELD_TYPE_CHECKBOX = 'checkbox';
	const FIELD_TYPE_CHECKBOX_SLIDER = 'slider';
	const FIELD_TYPE_CHECKBOX_TOGGLE = 'toggle';
	const FIELD_TYPE_CHECKBOXGROUP = 'checkboxes';
	const FIELD_TYPE_EMAIL = 'email';
	const FIELD_TYPE_GROUP = 'group';
	const FIELD_TYPE_HIDDEN = 'hidden';
	const FIELD_TYPE_PASSWORD = 'password';
	const FIELD_TYPE_RADIO = 'radio';
	const FIELD_TYPE_RADIOGROUP = 'radios';
	const FIELD_TYPE_SELECT = 'select';
	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_TEXTAREA = 'textarea';

	const FIELD_TYPE_BOOL_CHECKBOX = 'boolcheckbox';
	const FIELD_TYPE_BOOL_TOGGLE = 'booltoggle';

	protected $scripts = [];
	protected $styles = [];

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {

    }

	/* -------------------------------------------------------------------------------- */
	/* 		Asset (Script, Style)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Add a script/style asset in given group.
	 * 하나의 스크립트/스타일 자산을 지정 그룹에 추가한다.
	 *
	 * @param string $type
	 * @param string $group
	 * @param string $id
	 * @param string $src
	 * @param array $deps
	 * @param string $qs
	 * @return bool
	 */
	protected function addAsset(string $type, string $group, string $id, string $src, array $deps = null, string $qs = null)
	{
		if (! $this->checkAssetType($type)) {
			return false;
		}

		if (! Format::isAlphaNumeric($group) || ! Format::isAlphaNumericUnderbarDash($id)) {
			return false;
		}

		// 쿼리 문자열이 지정되지 않았다면, 파일의 수정 시간으로 지정
		// (내부 파일 URL 이고 파일이 존재하는지 검사)
		if (empty($qs)) {
			$internal = _startsWith($src, BASEURL, true);
			if ($internal) {
				$path = A_PATH . substr($src, strlen(BASEURL));
				if (is_file($path)) {
					$qs = filemtime($path);
				}
			}
		}
		$qs = empty($qs) ? '' : '?t=' . $qs;

		$data = [
			'group' => $group ?? 'head',
			'src' => $src,
			'deps' => $deps ?? [],
			'qs' => $qs,
		];

		if ($type === static::ASSET_TYPE_SCRIPT) {
			$this->scripts[$id] = $data;
		} elseif ($type === static::ASSET_TYPE_STYLE) {
			$this->styles[$id] = $data;
		}
		return true;
	}

	/**
	 * Check if the type of asset is valid
	 * 사용가능한 자산의 종류인지 확인한다.
	 *
	 * @param string $type
	 * @return bool
	 */
	protected function checkAssetType(string $type)
	{
		return ($type === static::ASSET_TYPE_SCRIPT || $type === static::ASSET_TYPE_STYLE);
	}

	/**
	 * Mark to insert the script/style asset code of given group.
	 * 지정된 그룹의 스크립트/스타일 자산 코드를 삽입하기 위해 마크한다.
	 *
	 * @param string $type
	 * @param string $group
	 * @return string
	 */
	protected function getAsset(string $type, string $group)
	{
		if (! $this->checkAssetType($type)) {
			return '';
		}
		return '<!--[asset-' . $type . '::' . $group . ']-->';
	}

	/**
	 * 추가된 자산 코드를 모두 출력한다.
	 * (view 작업 이후에 내부적으로 호출된다.)
	 *
	 * @param string $output
	 * @return string
	 */
	public function printAssets($output)
	{
		$assets = [ static::ASSET_TYPE_SCRIPT => $this->scripts, static::ASSET_TYPE_STYLE => $this->styles ];

		foreach ($assets as $type => $asset) {
			// 디펜던시 리졸버를 사용하여 의존성에 따라 정렬
			$deps = [];
			$codes = [];
			foreach ($asset as $id => $item) {
				$deps[$id] = $item['deps'];
			}
			$deps = dependencyResolve($deps);
			foreach ($deps as $id) {
				$item = $asset[$id];
				$group = $item['group'];
				$src = $item['src'] . $item['qs'];
				$codes[$group] = $codes[$group] ?? [];

				if ($type === static::ASSET_TYPE_SCRIPT) {
					$codes[$group][] = "<script id=\"script-$id\" src=\"$src\"></script>\n";
				} else {
					$codes[$group][] = "<link id=\"stylesheet-$id\" rel=\"stylesheet\" href=\"$src\">\n";
				}

			}

			// 마크 된 부분을 생성된 코드로 변경
			foreach ($codes as $group => $code) {
				if ($type === static::ASSET_TYPE_SCRIPT) {
					$output = str_replace($this->getScripts($group), implode('', $code), $output);
				} else {
					$output = str_replace($this->getStyles(), implode('', $code), $output);
				}
			}
		}

		return $output;

		/* 디펜던시 리졸버 예제...  의존성에 따라 정렬  - 스크립트는 의존성때문에 이 알고리즘 정렬이 반드시 필요
		$arr = [
			'car1' => [ 'owner1', 'brand1' ],
			'brand1' => [ ],
			'brand2' => [ ],
			'owner1' => [ 'brand1' ],
			'owner2' => [ 'brand2' ],
		];
		$result = dependencyResolve($arr);
		var_dump($result);*/
	}

    /* -------------------------------------------------------------------------------- */
    /* 		Script
    /* -------------------------------------------------------------------------------- */

	/**
	 * Add a script asset in given group.
	 * 하나의 스크립트 자산을 지정한 그룹에 추가한다.
	 *
	 * @param string $group
	 * @param string $id
	 * @param string $src
	 * @param array $deps
	 * @param string $qs
	 * @return bool
	 */
	public function addScript(string $group, string $id, string $src, array $deps = null, string $qs = null)
	{
		// 이미 존재하면 패스
		if (array_key_exists($id, $this->scripts)) {
			return true;
		}

		return $this->addAsset(static::ASSET_TYPE_SCRIPT, $group, $id, $src, $deps, $qs);
	}

	/**
	 * Mark to insert script codes of given group.
	 * 지정된 그룹의 스크립트 코드를 삽입하기 위해 마크한다.
	 *
	 * @param string $group
	 * @return string
	 */
	public function getScripts(string $group)
	{
		return $this->getAsset(static::ASSET_TYPE_SCRIPT, $group);
	}

	public function getHeadConstScripts()
	{
		$code = '<script>
			var BASEURL = "' . baseUrl() . '";
			var BASEURL_ONLYPATH = "' . baseUrlOnlyPath() . '";
			</script>' . PHP_EOL;

		return $code;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Style (css)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Add a style asset in given group.
	 * 하나의 스타일 자산을 지정 그룹에 추가한다.
	 *
	 * @param string $id
	 * @param string $src
	 * @param array $deps
	 * @param string $qs
	 * @return bool
	 */
	public function addStyle(string $id, string $src, array $deps = null, string $qs = null)
	{
		// 이미 존재하면 패스
		if (array_key_exists($id, $this->styles)) {
			return true;
		}

		return $this->addAsset(static::ASSET_TYPE_STYLE, 'head', $id, $src, $deps, $qs);
	}

	/**
	 * Mark to insert styly codes in head tag.
	 * head 태그 안에 스타일 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	public function getStyles()
	{
		return $this->getAsset(static::ASSET_TYPE_STYLE, 'head');
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Field (Input, Select, Textarea)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Retrieve HTML Code of a field.
	 * 지정한 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param string $type
	 * @param array $options
	 * @return string
	 */
	public function getAdminPageField(string $name, $data = null, string $type = self::FIELD_TYPE_TEXT, array $options = [])
	{
		if ($name == '') return '[Field Error]';

		$showLabel = $options['showLabel'] ?? true;

		$langGroup = $options['langGroup'] ?? 'Globals';
		$labelText = $name ? _t($langGroup . '.l_' . $name) : '';

		// Input Element Attributes

		$id = $options['id'] ?? 'field_' . $name;
		$label = $options['label'] ?? $labelText;

		// Classes
		$fieldClass = $options['fieldClass'] ?? '';
		$helpClass = $options['helpClass'] ?? '';

		// Retrieve Relevant Value
		$value = $this->getEscFieldValue($name, $data, ($type === self::FIELD_TYPE_TEXTAREA) ? 'html' : 'attr');
		if ($value === null) $value = $options['default'] ?? '';

		$defaultLabel = (_t($langGroup . '.l_' . $name . '_default') === $langGroup . '.l_' . $name . '_default')
			? null : _t($langGroup . '.l_' . $name . '_default');
		$placeholder = $options['placeholder'] ?? $defaultLabel ?? $labelText;

		$help = isset($options['help']) ? $options['help'] : null;
		$hidden = isset($options['hidden']) && $options['hidden'] ? 'hidden' : '';

		$required = in_array('required', $options);
		$maxlength = $options['maxlength'] ?? '';
		$minlength = $options['minlength'] ?? '';

		$onlyControl = in_array('control', $options);

		$attrString = $options['attr'] ?? '';

		$isGroup = false;
		$scriptHtml = '';
		switch ($type) {
			case self::FIELD_TYPE_EMAIL:
			case self::FIELD_TYPE_PASSWORD:
			case self::FIELD_TYPE_TEXT:
				$pattern = $options['pattern'] ?? '';

				$fieldHtml = "<div class=\"control input\"><input type=\"$type\" name=\"$name\" id=\"$id\" value=\"$value\" placeholder=\"$placeholder\""
					. ($required ? " required" : '')
					. ($minlength ? " minlength=\"$minlength\"" : '')
					. ($maxlength ? " maxlength=\"$maxlength\"" : '')
					. ($pattern ? " pattern=\"$pattern\"" : '')
					. " $attrString></div>";

				break;

			case self::FIELD_TYPE_TEXTAREA:
				$rows = $data['rows'] ?? '';

				$fieldHtml = "<textarea name=\"$name\" id=\"$id\" placeholder=\"$placeholder\" "
					. ($required ? " required" : '')
					. ($minlength ? " minlength=\"$minlength\"" : '')
					. ($maxlength ? " maxlength=\"$maxlength\"" : '')
					. ($rows ? " rows=\"$rows\"" : '')
					. " $attrString>" . $value . "</textarea>";

				break;

			case self::FIELD_TYPE_SELECT:
				$multiple = in_array('multiple', $options) ? 'multiple' : '';
				$clearable = in_array('clearable', $options) ? 'clearable' : '';
				$search = in_array('search', $options) ? 'search' : '';

				$isDropwdown = $options['dropdown'] ?? true;
				if ($isDropwdown) {
					$fieldHtml = "<div class=\"control $multiple $clearable $search dropdown\" id=\"$id\">" . PHP_EOL
						. "<input type=\"hidden\" name=\"$name\" value=\"$value\""
						. " placeholder=\"$placeholder\""
						. ($required ? " required" : '') . " $attrString>" . PHP_EOL
						. '<nav class="menu">' . PHP_EOL;

					// 반복문
					foreach ($options['items'] as $item) {
						$item_text = $item['text'];
						$item_value = $item['value'];
						$fieldHtml .= "<div class=\"item\" data-value=\"$item_value\">$item_text</div>";
					}

					$fieldHtml .= '</nav>' . PHP_EOL
						. '</div>' . PHP_EOL;

					$dropdownOptions_str = '';
					if (isset($options['dropdownOptions']) && !empty($options['dropdownOptions'])) {
						if (is_array($options['dropdownOptions'])) {
							$dropdownOptions_str = json_encode($options['dropdownOptions']);
						} else if (is_string($options['dropdownOptions'])) {
							$dropdownOptions_str = $options['dropdownOptions'];
						}
					}

					// 스크립트 :: FomanticUI - Dropdown 초기화
					//$scriptHtml = "<script> $(function() { $('#{$id}').dropdown(" . $dropdownOptions_str . "); }); </script>" . PHP_EOL;

				} else {
					$fieldHtml = "<select name=\"$name\" id=\"$id\" class=\"ui dropdown\" "
						. ($required ? " required" : '') . " placeholder=\"$placeholder\" $attrString>" . PHP_EOL;

					// 반복문
					foreach ($options['items'] as $item) {
						$item_text = $item['text'];
						$item_value = $item['value'];
						$selected = ($item_value === $value) ? 'selected' : '';
						$fieldHtml .= "<option value=\"$item_value\" $selected>$item_text</option>";
					}

					$fieldHtml .= '</select>' . PHP_EOL;
				}

				break;

			case self::FIELD_TYPE_CHECKBOXGROUP:		// 체크 상자 그룹
				$isGroup = true;
				$groupClass = in_array('noSegment', $options) ? '' : 'ui segment';

				$fieldHtml = "<div class=\"$groupClass\">";
				foreach ($options['items'] as $item) {
					if (is_array($item)) {
						$fieldHtml .= $this->getAdminPageFieldChoosable($name, $item['value'], $item['data'] ?? $value, self::FIELD_TYPE_CHECKBOX, $item['options'] ?? []);
					} else {
						$fieldHtml .= $this->getAdminPageFieldChoosable($name, $item, $value, self::FIELD_TYPE_CHECKBOX);
					}
				}
				$fieldHtml .= '</div>';

				break;

			case self::FIELD_TYPE_RADIOGROUP:			// 라디오 상자 그룹
				$isGroup = true;
				$groupClass = in_array('noSegment', $options) ? '' : 'ui segment';

				$fieldHtml = "<div class=\"$groupClass\">";
				foreach ($options['items'] as $item) {
					if (is_array($item)) {
						$fieldHtml .= $this->getAdminPageFieldChoosable($name, $item['value'], $item['data'] ?? $value, self::FIELD_TYPE_RADIO, $item['options'] ?? []);
					} else {
						$fieldHtml .= $this->getAdminPageFieldChoosable($name, $item, $value, self::FIELD_TYPE_RADIO);
					}
				}
				$fieldHtml .= '</div>';

				break;

			case self::FIELD_TYPE_GROUP:				// 일반 그룹
				$groupClass = in_array('noSegment', $options) ? '' : 'controls box';

				$fieldHtml = "<div class=\"$groupClass\">";
				foreach ($options['items'] as $item) {
					$fieldHtml .= $item;
				}
				$fieldHtml .= '</div>';

				break;

			case self::FIELD_TYPE_BOOL_CHECKBOX:
			case self::FIELD_TYPE_BOOL_TOGGLE:
				$showLabel = false;
				$checked = $value ? 'checked' : '';

				$postfix = $type === self::FIELD_TYPE_BOOL_TOGGLE ? 'toggle' : '';

				$fieldHtml = "<label class=\"control checkbox bool $postfix\">" . PHP_EOL
					. "<input type=\"hidden\" name=\"$name\" id=\"$id\" value=\"$value\" $attrString>" . PHP_EOL
					. "<span>$label</span>" . PHP_EOL
					. '</label>' . PHP_EOL;

				// 스크립트 :: hidden 입력요소가 실제적인 값을 운반한다. 토글 상자는 UI 일뿐...
				/*$fieldHtml .= "<script> (function() { document.getElementById('{$id}_bool').addEventListener('change', function () {
					document.getElementById('$id').value = this.checked ? 1 : 0;
 					}); })(); </script>" . PHP_EOL;*/

				break;

			default:
				return '[Field Error]';
		}

		// 최종 HTML 코드 (Fomantic UI 적용 - 2019-07-01)
		if ($onlyControl) {
			$code = $fieldHtml;

		} else {
			$code = "<div class=\"" . ($isGroup ? 'grouped fields' : '') . " field field-$name $fieldClass $hidden\">" . PHP_EOL;
			if ($showLabel) {
				$code .= "<label" . ($isGroup ? '' : " for=\"$id\"") . ">$label</label>" . PHP_EOL;
			}
			$code .= $fieldHtml;
			if ($help !== null) {
				$code .= "<div class=\"helptext {$helpClass}\">$help</div>" . PHP_EOL;
			}
			$code .= '</div>' . PHP_EOL;

			$code .= $scriptHtml;
		}

		return $code;
	}

	/**
	 * Retrieve HTML Code of a choosable field.
	 * 지정한 선택형 필드의 HTML 코드를 반환한다.
	 *
	 * Radio, Checkbox, Toogle 등
	 *
	 * @param string $name
	 * @param string $value
	 * @param array|string $data
	 * @param string $type
	 * @param array $options
	 * @return string
	 */
	public function getAdminPageFieldChoosable(string $name, string $value, $data = null, string $type = self::FIELD_TYPE_CHECKBOX, array $options = [])
	{
		if ($name == '') return '[Field Error]';

		$langGroup = $options['langGroup'] ?? 'Globals';
		$labelText = _t("{$langGroup}.l_{$name}_{$value}");

		// Input Element Attributes

		$id = $options['id'] ?? "field_{$name}_{$value}";
		$label = $options['label'] ?? $labelText;

		// Retrieve Relevant Value
		$dataValue = $this->getEscFieldValue($name, $data);
		if ($dataValue === null) $dataValue = $options['default'] ?? '';

		$checked = ($value == $dataValue) ? 'checked' : '';

		$hidden = isset($data['hidden']) && $data['hidden'] ? 'hidden' : '';
		$attrString = $options['attr'] ?? '';

		switch ($type) {
			case self::FIELD_TYPE_CHECKBOX:
			case self::FIELD_TYPE_CHECKBOX_SLIDER:
			case self::FIELD_TYPE_CHECKBOX_TOGGLE:
			case self::FIELD_TYPE_RADIO:
				$inputType = ($type === self::FIELD_TYPE_RADIO) ? $type : 'checkbox';
				$cbType = ($type !== self::FIELD_TYPE_CHECKBOX) ? $type : '';

				// 체크박스는 name 을 배열 형태로 해줘야 한다.
				$name2 = ($type === self::FIELD_TYPE_RADIO) ? $name : $name . '[]';

				$fieldHtml = "<div class=\"ui $cbType checkbox\">" . PHP_EOL
					. "<input type=\"$inputType\" name=\"$name2\" id=\"$id\" value=\"$value\" $checked $attrString>" . PHP_EOL
					. "<label for=\"$id\">$label</label>" . PHP_EOL
					. '</div>' . PHP_EOL;
				break;

			default:
				return '[Field Error]';
		}

		// 최종 HTML 코드 (Fomantic UI 적용 - 2019-07-01)
		$code = "<div class=\"field field-$name $hidden\">" . PHP_EOL;
		$code .= $fieldHtml;
		$code .= '</div>' . PHP_EOL;

		return $code;
	}

	/**
	 * Retrieve HTML Code of a hidden input field.
	 * Hidden 입력요소 필드 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	public function getFieldHidden(string $name, $data = null, array $options = [])
	{
		if ($name == '') return '[Field Error]';

		$id = $options['id'] ?? 'field_' . $name;
		$value = $this->getEscFieldValue($name, $data);

		$attrString = $options['attr'] ?? '';

		return "<input type=\"hidden\" name=\"$name\" id=\"$id\" value=\"$value\" $attrString>" . PHP_EOL;
	}

	/**
	 * select 입력요소에 들어갈 준비된 목록을 배열로 반환한다.
	 *
	 * @param string $type
	 * @return array
	 */
	public function getPreparedListForSelect($type)
	{
		$arr = [];
		switch ($type) {
			case 'boards':
				$arr[] = [ 'text' => '작업중...', 'value' => '0' ];
				break;

			case 'docs':
				$data = Services::docs()->getAll([ M_d::d_name, M_d::d_title ]);
				foreach ($data as $item) {
					$arr[] = [
						'text' => $item[M_d::d_name],
						'value' => $item[M_d::d_name],
						'title' => $item[M_d::d_title],
					];
				}
				break;

			case 'gender':
				$arr = [
					[ 'text' => _g('male'), 'value' => 'm' ],
					[ 'text' => _g('female'), 'value' => 'f' ],
					//[ 'text' => _g('transgender'), 'value' => 't' ],
				];
				break;

			case 'locale':
				$data = Services::l10n()->getSupportedLocales();
				foreach ($data as $item) {
					$arr[] = [
						'text' => $item['label'],
						'value' => $item['v'],
					];
				}
				break;

			case 'menu_linktype':
				$arr = [
					[ 'value' => Consts::MENU_LINKTYPE_NONE, ],
					[ 'value' => Consts::MENU_LINKTYPE_TOPLEVELITEM, ],
					[ 'value' => Consts::MENU_LINKTYPE_DOCS, ],
					[ 'value' => Consts::MENU_LINKTYPE_BOARDS, ],
					[ 'value' => Consts::MENU_LINKTYPE_URL, ],
				];
				foreach ($arr as & $item) {
					$item['text'] = _g('l_m_linktype_' . $item['value']);
				}
				unset($item);
				break;

			case 'theme':
				$data = Services::theme()->getThemes();
				foreach ($data as $item) {
					$arr[] = [
						'text' => $item['id'] . ' (' . $item['name'] . ')',
						'value' => $item['id'],
					];
				}
				break;

			case 'userroles':
				$data = Services::userroles()->getAll();
				foreach ($data as $item) {
					$arr[] = [
						'text' => $item['text'],
						'value' => $item[M_ur::ur_id],
					];
				}
				break;

			case 'userroles_notadmin':
				$data = Services::userroles()->getAllWithoutAdmin();
				foreach ($data as $item) {
					$arr[] = [
						'text' => $item['text'],
						'value' => $item[M_ur::ur_id],
					];
				}
				break;

			case 'year':
				// 미완성..
				break;
		}
		return $arr;
	}

	/**
	 * data 가 배열이라면 지정된 필드에 해당하는 값을 이스케이프하여 가져오고,
	 * 문자열이라면 그 값 자체를 이스케이프하여 가져온다.
	 *
	 * escapeContext :: attr, html, raw 등 ( esc()전역 함수 참고 )
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param string $escapeContext
	 * @return string
	 */
	protected function getEscFieldValue(string $name, $data = null, string $escapeContext = 'attr')
	{
		if (is_array($data) && isset($data[$name]))
			return _e($data[$name], $escapeContext);
		else if (is_string($data))
			return _e($data, $escapeContext);
		return null;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		CSRF
	/* -------------------------------------------------------------------------------- */

	/**
	 * Mark to insert styly codes in head tag.
	 * head 태그 안에 스타일 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	public function getMetaCSRF()
	{
		$code = '<meta name="sec-name" content="' . csrf_token() . '">'
			. '<meta name="sec-value" content="' . csrf_hash() . '">';
		return $code;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		HtmlPurifier Library
	/* -------------------------------------------------------------------------------- */

	/**
	 * HTML 코드를 HtmlPurifier 로 정리하여 반환한다.
	 *
	 * @param string $html
	 * @return string
	 */
	public function purifyHTML($html)
	{
		require_once(APPPATH . '/ThirdParty/HtmlPurifier/HTMLPurifier.standalone.php');

		// 기본 설정을 불러온 후 적당히 커스터마이징을 해줌
		$config = \HTMLPurifier_Config::createDefault();
		$config->set('Attr.EnableID', false);
		$config->set('Attr.DefaultImageAlt', '');
		$config->set('CSS.AllowTricky', true);

		// 인터넷 주소를 자동으로 링크로 바꿔주는 기능
		$config->set('AutoFormat.Linkify', true);

		// 이미지 크기 제한 해제 (한국에서 많이 쓰는 웹툰이나 짤방과 호환성 유지를 위해)
		$config->set('HTML.MaxImgLength', null);
		$config->set('CSS.MaxImgLength', null);

		// 인코딩 및 DocType 지정
		$config->set('Core.Encoding', 'UTF-8');
		$config->set('HTML.Doctype', 'XHTML 1.0 Transitional');

		// 플래시 삽입 허용
		$config->set('HTML.FlashAllowFullScreen', true);
		$config->set('HTML.SafeEmbed', true);
		$config->set('HTML.SafeIframe', true);
		$config->set('HTML.SafeObject', true);
		$config->set('Output.FlashCompat', true);

		// 최근 많이 사용하는 iframe 동영상 삽입 허용
		$config->set('URI.SafeIframeRegexp', '#^(?:https?:)?//(?:'.implode('|', [
				'www\\.youtube(?:-nocookie)?\\.com/',
				'maps\\.google\\.com/',
				'player\\.vimeo\\.com/video/',
				'www\\.microsoft\\.com/showcase/video\\.aspx',
				'(?:serviceapi\\.nmv|player\\.music)\\.naver\\.com/',
				'(?:api\\.v|flvs|tvpot|videofarm)\\.daum\\.net/',
				'v\\.nate\\.com/',
				'play\\.mgoon\\.com/',
				'channel\\.pandora\\.tv/',
				'www\\.tagstory\\.com/',
				'play\\.pullbbang\\.com/',
				'tv\\.seoul\\.go\\.kr/',
				'ucc\\.tlatlago\\.com/',
				'vodmall\\.imbc\\.com/',
				'www\\.musicshake\\.com/',
				'www\\.afreeca\\.com/player/Player\\.swf',
				'static\\.plaync\\.co\\.kr/',
				'video\\.interest\\.me/',
				'player\\.mnet\\.com/',
				'sbsplayer\\.sbs\\.co\\.kr/',
				'img\\.lifestyler\\.co\\.kr/',
				'c\\.brightcove\\.com/',
				'www\\.slideshare\\.net/',
			]).')#');

		// Set some HTML5 properties
		$config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
		$config->set('HTML.DefinitionRev', 1);
		$def = $config->maybeGetRawHTMLDefinition();
		if ($def) {
			// http://developers.whatwg.org/sections.html
			$def->addElement('section', 'Block', 'Flow', 'Common');
			$def->addElement('nav',     'Block', 'Flow', 'Common');
			$def->addElement('article', 'Block', 'Flow', 'Common');
			$def->addElement('aside',   'Block', 'Flow', 'Common');
			$def->addElement('header',  'Block', 'Flow', 'Common');
			$def->addElement('footer',  'Block', 'Flow', 'Common');

			// Content model actually excludes several tags, not modelled here
			$def->addElement('address', 'Block', 'Flow', 'Common');
			$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

			// http://developers.whatwg.org/grouping-content.html
			$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
			$def->addElement('figcaption', 'Inline', 'Flow', 'Common');

			// http://developers.whatwg.org/the-video-element.html#the-video-element
			$def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
				'src' => 'URI',
				'type' => 'Text',
				'width' => 'Length',
				'height' => 'Length',
				'poster' => 'URI',
				'preload' => 'Enum#auto,metadata,none',
				'controls' => 'Bool',
			]);
			$def->addElement('source', 'Block', 'Flow', 'Common', [
				'src' => 'URI',
				'type' => 'Text',
			]);

			// http://developers.whatwg.org/text-level-semantics.html
			$def->addElement('s',    'Inline', 'Inline', 'Common');
			$def->addElement('var',  'Inline', 'Inline', 'Common');
			$def->addElement('sub',  'Inline', 'Inline', 'Common');
			$def->addElement('sup',  'Inline', 'Inline', 'Common');
			$def->addElement('mark', 'Inline', 'Inline', 'Common');
			$def->addElement('wbr',  'Inline', 'Empty', 'Core');
			// http://developers.whatwg.org/edits.html
			$def->addElement('ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);
			$def->addElement('del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);

			// TinyMCE
			$def->addAttribute('img', 'data-mce-src', 'Text');
			$def->addAttribute('img', 'data-mce-json', 'Text');

			// Others
			$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
			$def->addAttribute('table', 'height', 'Text');
			$def->addAttribute('td', 'border', 'Text');
			$def->addAttribute('th', 'border', 'Text');
			$def->addAttribute('tr', 'width', 'Text');
			$def->addAttribute('tr', 'height', 'Text');
			$def->addAttribute('tr', 'border', 'Text');
		}

		// 설정을 저장하고 필터링 라이브러리 초기화
		$purifier = new \HTMLPurifier($config);

		// HTML 필터링 실행
		$html = $purifier->purify($html);

		return $html;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Simple Html Dom Library
	/* -------------------------------------------------------------------------------- */

	/**
	 * HTML 코드를 DOM 구조화 하여 반환한다.
	 * (Simple Html Dom 라이브러리 사용)
	 *
	 * @param string $html
	 * @return \simple_html_dom
	 */
	public function loadDOM($html)
	{
		require_once(APPPATH . '/ThirdParty/SimpleHtmlDom/simple_html_dom.php');

		return str_get_html($html);
	}

    /* -------------------------------------------------------------------------------- */
    /* 		Protected methods
    /* -------------------------------------------------------------------------------- */



}
