<?php

/**
 * Arakny Bootstrap Extension
 * 아라크니 부트스트랩 확장
 *
 * CodeIgniter 의 부트스트랩에서 부분적 기능을 확장한다.
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

/** Arakny Signature */
define( '_ARAKNY_', 'Arakny' );

/** Arakny's current version (main.sub.build) */
define( 'A_VERSION', '1.0.0' );

/** The date Arakny was last builded */
define( 'A_LASTBUILD_DATE', '2019-03-01' );

/** Arakny public(www) base path */
define( 'A_PATH', FCPATH );

/* -------------------------------------------------------------------------------- */

/**
 * session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다.
 * 아라크니 기본값은 2 입니다. (2% 확률로 가비지 콜렉터가 작동됩니다.)
 */
ini_set("session.gc_probability", 2);

/**
 * session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다.
 * 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다.
 * session.gc_divisor의 기본값은 100입니다.
 */
ini_set("session.gc_divisor", 100);

/* -------------------------------------------------------------------------------- */

// baseURL 값이 지정되지 않았을 때 (아직 초기 설치전의 경우)
// 적당하게 추측하여 지정한다.
if ( trim($appConfig->baseURL) == '' || $appConfig->baseURL == 'null' ) {
    $a_path_fix = str_replace( '\\', '/', ROOTPATH );
    $script_filename_dir = dirname( $_SERVER['SCRIPT_FILENAME'] );

    $path = '';
    if ( $script_filename_dir . '/' == $a_path_fix ) {
        $path = preg_replace( '#/[^/]*$#i', '', $_SERVER['SCRIPT_NAME'] );
        $path = preg_replace( '#/(.*)$#i', '${1}/', $path );
    }

    $is_https = false;
    if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') $is_https = true;
    elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') $is_https = true;
    elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') $is_https = true;

    $appConfig->baseURL = ($is_https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/' . $path;
}

/** ARAKNY EDIT < */
//\CodeIgniter\Events\Events::on('pre_system', 'Arakny\Arakny::init');
/** ARAKNY EDIT > */