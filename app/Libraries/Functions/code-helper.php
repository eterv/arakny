<?php

use Arakny\Constants\Consts;
use Arakny\Libraries\Html;
use Arakny\Models\DocsModel as M_d;
use Arakny\Models\UserRolesModel as M_ur;
use Config\Services;

/**
 * Arakny HTML Code Helper
 * 아라크니 HTML 코드 도우미
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

/**
 * 함수 설명
 *
 * @return	mixed
 */
function load_script($src) {
	// 새 함수를 작성하기 위한 기본 템플릿 입니다.

    // install 페이지 작업 완료 후 설치 후에 작동될 함수...............

    $code = '<script src="' . $src . '"></script>';

    return null;
}

function load_admin_scripts(...$scripts) {

    // install 페이지 작업 완료 후 설치 후에 작동될 함수...............

    foreach ($scripts as $script) {
        //file_exists(  )
    }
}

/* -------------------------------------------------------------------------------- */
/* 		Script
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('addScriptInGroup') )
{
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
	function addScriptInGroup(string $group, string $id, string $src, array $deps = null, string $qs = null)
	{
		return Services::html()->addScript($group, $id, $src, $deps, $qs);
	}
}

if ( ! function_exists('addScriptInBody') )
{
	/**
	 * Add a script asset in body group.
	 * 하나의 스크립트 자산을 body 그룹에 추가한다.
	 *
	 * @param string $id
	 * @param string $src
	 * @param array $deps
	 * @param string $qs
	 * @return bool
	 */
	function addScriptInBody(string $id, string $src, array $deps = null, string $qs = null)
	{
		return Services::html()->addScript('body', $id, $src, $deps, $qs);
	}
}

if ( ! function_exists('addScriptInHead') )
{
	/**
	 * Add a script asset in the head group.
	 * 하나의 스크립트 자산을 head 그룹에 추가한다.
	 *
	 * @param string $id
	 * @param string $src
	 * @param array $deps
	 * @param string $qs
	 * @return bool
	 */
	function addScriptInHead(string $id, string $src, array $deps = null, string $qs = null)
	{
		return Services::html()->addScript('head', $id, $src, $deps, $qs);
	}
}

if ( ! function_exists('_scriptsGroup') )
{
	/**
	 * Mark to insert script codes of given group.
	 * 지정한 그룹의 스크립트 코드를 삽입하기 위해 마크한다.
	 *
	 * @param string $group
	 * @return string
	 */
	function _scriptsGroup(string $group)
	{
		return Services::html()->getScripts($group);
	}
}

if ( ! function_exists('_scriptsBody') )
{
	/**
	 * Mark to insert script codes of the body group.
	 * body 그룹의 스크립트 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	function _scriptsBody()
	{
		return Services::html()->getScripts('body');
	}
}

if ( ! function_exists('_scriptsHead') )
{
	/**
	 * Mark to insert script codes of the head group.
	 * head 그룹의 스크립트 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	function _scriptsHead()
	{
		return Services::html()->getScripts('head');
	}
}

if ( ! function_exists('_headConstantScript') )
{
	/**
	 * Mark to insert script codes of the head group.
	 * head 그룹의 스크립트 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	function _constants()
	{
		return Services::html()->getHeadConstScripts();
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Style (css)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('addStyle') )
{
	/**
	 * Add a style asset.
	 * 하나의 스타일 자산을 추가한다.
	 *
	 * @param string $id
	 * @param string $src
	 * @param array $deps
	 * @param string $qs
	 * @return bool
	 */
	function addStyle(string $id, string $src, array $deps = null, string $qs = null)
	{
		return Services::html()->addStyle($id, $src, $deps, $qs);
	}
}

if ( ! function_exists('_styles') )
{
	/**
	 * Mark to insert styly codes in head tag.
	 * head 태그 안에 스타일 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	function _styles()
	{
		return Services::html()->getStyles();
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Title & Menu
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_menu') )
{
	/**
	 * Return the page title.
	 * 페이지 제목을 반환한다.
	 *
	 * @return array|string
	 */
	function _menu()
	{
		if (isAdminPage()) {
			$ap = Services::adminpage();
			return [
				'main' => $ap->menu_main,
				'sub' => $ap->menu_sub,
			];
		} else {
			return Services::page()->getMenu();
		}
	}
}

if ( ! function_exists('_pageTitle') )
{
	/**
	 * Return the page title.
	 * 페이지 제목을 반환한다.
	 *
	 * @return string
	 */
	function _pageTitle()
	{
		if (isAdminPage()) {
			return Services::adminpage()->getPageTitle();
		} else {

			// @todo 일반 페이지 타이틀 가져오기 작업 필요...

			return Services::page()->getPageTitle();

		}
	}
}

if ( ! function_exists('_pageHeadTitle') )
{
	/**
	 * Return the title in <head> tag.
	 * 페이지 <head> 태그 안의 제목을 반환한다.
	 *
	 * @return string
	 */
	function _pageHeadTitle()
	{
		if (isAdminPage()) {
			return Services::adminpage()->getPageTitle() . ' - ' . getSetting('name');
		} else {
			return Services::page()->getHeadTitle();
		}
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Captcha & CSRF
/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_captchaHtml') )
{
	/**
	 * Return captcha HTML code.
	 * 캡챠 img 와 input 이 포함된 HTML 코드를 반환한다.
	 *
	 * @param bool $onlyNumber
	 * @param int $height
	 * @param string $sizeClass
	 * @return string
	 */
	function _captchaHtml($onlyNumber = false, $height = 45, $sizeClass = 'h-l')
	{
		return Services::captcha()->getCaptchaCode($onlyNumber, $height, $sizeClass);
	}
}

if ( ! function_exists('_metaCSRF') )
{
	/**
	 * Mark to insert styly codes in head tag.
	 * head 태그 안에 스타일 코드를 삽입하기 위해 마크한다.
	 *
	 * @return string
	 */
	function _metaCSRF()
	{
		return Services::html()->getMetaCSRF();
	}
}

/* -------------------------------------------------------------------------------- */
/* 		Field (Input, Select, Textarea)
/* -------------------------------------------------------------------------------- */

if ( ! function_exists( '_adminFieldCheckboxGroup' ) )
{
	/**
	 * Retrieve HTML Code of a checkbox input field group.
	 * 지정한 Checkbox 입력요소 필드 그룹의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $items
	 * @param array $options
	 * @return string
	 */
	function _adminFieldCheckboxGroup(string $name, $data = null, array $items = [], array $options = [])
	{
		$options['items'] = $items;
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_CHECKBOXGROUP, $options);
	}
}

if ( ! function_exists( '_adminFieldEmail' ) )
{
	/**
	 * Retrieve HTML Code of a text input field.
	 * Text 입력요소 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _adminFieldEmail(string $name, $data = null, array $options = [])
	{
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_EMAIL, $options);
	}
}

if ( ! function_exists( '_adminFieldGroup' ) )
{
	/**
	 * Retrieve HTML Code of a input field group.
	 * 지정한 입력요소 필드 그룹의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param string $label
	 * @param array $items
	 * @param array $options
	 * @return string
	 */
	function _adminFieldGroup(string $name, $label = null, array $items = [], array $options = [])
	{
		$options['items'] = $items;
		$options['label'] = $label;
		return Services::html()->getAdminPageField($name, null, Html::FIELD_TYPE_GROUP, $options);
	}
}

if ( ! function_exists( '_adminFieldPassword' ) )
{
	/**
	 * Retrieve HTML Code of a password input field.
	 * Password 입력요소 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _adminFieldPassword(string $name, $data = null, array $options = [])
	{
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_PASSWORD, $options);
	}
}

if ( ! function_exists( '_adminFieldRadioGroup' ) )
{
	/**
	 * Retrieve HTML Code of a radio input field group.
	 * 지정한 Radio 입력요소 필드 그룹의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $items
	 * @param array $options
	 * @return string
	 */
	function _adminFieldRadioGroup(string $name, $data = null, array $items = [], array $options = [])
	{
		$options['items'] = $items;
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_RADIOGROUP, $options);
	}
}

if ( ! function_exists( '_adminFieldSelect' ) )
{
	/**
	 * Retrieve HTML Code of a select (dropdown) field.
	 * 지정한 Select (드롭다운) 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array|string $items
	 * @param array $options
	 * @return string
	 */
	function _adminFieldSelect(string $name, $data = null, $items = [], array $options = [])
	{
		$options['items'] = is_array($items) ? $items : getPreparedListForSelect($items);
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_SELECT, $options);
	}
}

if ( ! function_exists( '_adminFieldText' ) )
{
	/**
	 * Retrieve HTML Code of a text input field.
	 * Text 입력요소 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _adminFieldText(string $name, $data = null, array $options = [])
	{
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_TEXT, $options);
	}
}

if ( ! function_exists( '_adminFieldTextarea' ) )
{
	/**
	 * Retrieve HTML Code of a textarea field.
	 * 지정한 Textarea 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _adminFieldTextarea(string $name, $data = null, array $options = [])
	{
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_TEXTAREA, $options);
	}
}

if ( ! function_exists('_adminFieldBoolCheckbox') )
{
	/**
	 * Retrieve HTML Code of a normal checkbox (true/false) input field.
	 * 일반 Checkbox (참/거짓) 입력요소 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _adminFieldBoolCheckbox(string $name, $data = null, array $options = [])
	{
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_BOOL_CHECKBOX, $options);
	}
}

if ( ! function_exists('_adminFieldBoolToggle') )
{
	/**
	 * Retrieve HTML Code of a toggle checkbox (true/false) input field.
	 * 토글형 Checkbox (참/거짓) 입력요소 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _adminFieldBoolToggle(string $name, $data = null, array $options = [])
	{
		return Services::html()->getAdminPageField($name, $data, Html::FIELD_TYPE_BOOL_TOGGLE, $options);
	}
}

if ( ! function_exists( '_fieldHidden' ) )
{
	/**
	 * Retrieve HTML Code of a hidden input field.
	 * Hidden 입력요소 필드의 HTML 코드를 반환한다.
	 *
	 * @param string $name
	 * @param array|string $data
	 * @param array $options
	 * @return string
	 */
	function _fieldHidden(string $name, $data = null, array $options = [])
	{
		return Services::html()->getFieldHidden($name, $data, $options);
	}
}

if ( ! function_exists('getPreparedListForSelect') )
{
	/**
	 * select 입력요소에 들어갈 준비된 목록을 배열로 반환한다.
	 *
	 * @param string $type
	 * @return array
	 */
	function getPreparedListForSelect($type)
	{
		return Services::html()->getPreparedListForSelect($type);
	}
}