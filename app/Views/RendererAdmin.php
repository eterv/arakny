<?php defined('_ARAKNY_') OR exit;
/**
 * View - Site Admin Page Renderer
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */

/** @var string $_render_name */
/** @var \Arakny\Libraries\Theme $_theme */

/** @var mixed $view */
/** @var mixed $options */
/** @var mixed $saveData */
/** @var mixed $fileExt */
/** @var mixed $realPath */

$_templates = $_theme->getIncludeAdminTemplates($_render_name);

// Theme 파일에서 출력되지 말아야 할 변수를 제거한다.
// get_defined_vars() 함수로 알 수 있다.
unset( $_theme, $_render_name );
if ( $view === 'RendererAdmin' ) unset( $view );
if ( $options === [] ) unset( $options );
if ( $saveData === null ) unset( $saveData );
if ( $fileExt === '' ) unset( $fileExt );
if ( $realPath === 'RendererAdmin.php' ) unset( $realPath );

// Load Theme files
foreach ( $_templates as $_template ) {
    include_once $_template;
}