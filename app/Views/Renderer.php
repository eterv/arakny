<?php defined('_ARAKNY_') OR exit;
/**
 * View - Site Page Renderer
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */

/** @var string $_render_name */
/** @var Arakny\Libraries\Theme $_theme */

/** @var mixed $view */
/** @var mixed $options */
/** @var mixed $saveData */
/** @var mixed $fileExt */
/** @var mixed $realPath */

if (isset($_content) && isset($_useHeaderFooter)) {
	$_templates = $_theme->getIncludeTemplatesByContent($_content, $_useHeaderFooter);
	unset( $_content, $_useHeaderFooter );

} else if (isset($_path) && isset($_useHeaderFooter)) {
	$_templates = $_theme->getIncludeTemplatesByPath($_path, $_useHeaderFooter);
	unset( $_path, $_useHeaderFooter );

} else {
	$_templates = $_theme->getIncludeTemplates($_render_name);
}

// Theme 파일에서 출력되지 말아야 할 변수를 제거한다.
// get_defined_vars() 함수로 알 수 있다.
unset( $_theme, $_render_name );
if ( $view === 'Renderer' ) unset( $view );
if ( $options === [] ) unset( $options );
if ( $saveData === null ) unset( $saveData );
if ( $fileExt === '' ) unset( $fileExt );
if ( $realPath === 'Renderer.php' ) unset( $realPath );

// Load Theme files
foreach ( $_templates as $_template ) {
	if (is_array($_template)) {
		if (array_key_exists('content', $_template)) {
			echo $_template['content'];
		}
	} else {
		include_once $_template;
	}
}