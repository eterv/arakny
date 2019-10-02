<?php
/**
 * Korean - Globals Strings
 * 한국어 - 전역(글로벌) 문자열
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */
return [
    'Arakny' => 'Arakny',

    'installation' => 'Installation',

    'ok' => 'OK',
    'yes' => 'Yes',
    'no' => 'No',
    'prev' => 'Prev',
    'next' => 'Next',
    'show' => 'Show',
    'hide' => 'Hide',

    'agree' => 'Agree',

    't_ur_superadmin' => 'Super Admin',
    't_ur_admin' => 'Administrator',
    't_ur_general' => 'Gerenal Member',

    // Label

    'l_ur_id' => 'Role ID',
    'l_ur_name' => 'Role Name',
    'l_ur_text' => 'Role Display Text',
    'l_ur_order' => 'Role Order',

    'l_u_id' => '사용자 nid',
    'l_u_login' => '아이디',
    'l_u_pass' => '암호',
    'l_u_ur_id' => '역할 ID',
    'l_u_nickname' => '별명',
    'l_u_email' => '메일주소',
    'l_u_dt_joined' => '가입 일시',
    'l_u_dt_lastlogin' => '마지막 로그인 일시',
    'l_u_dt_lastchpass' => '마지막 암호 변경 일시',
    'l_u_is_auth' => '인증 여부',
    'l_u_authcode' => '인증코드',
    'l_u_dt_authcode' => '인증코드 발급 일시',
    'l_u_is_blocked' => '차단 여부',
    'l_u_is_deleted' => '탈퇴 여부',
    'l_u_dt_deleted' => '탈퇴 일시',

    // Fields & Descriptions

    'dbhost' => 'DB Hostname',
    'dbuser' => 'DB Username',
    'dbpass' => 'DB Password',
    'dbname' => 'DB Name',
    'dbprefix' => 'Table Prefix',

    'dbhost_desc' => 'localhost가 작동하지 않는다면 이 정보는 웹호스팅 서비스 업체에서 받을 수 있습니다.',
    'dbuser_desc' => 'Database user name.',
    'dbpass_desc' => 'Database user password.',
    'dbname_desc' => '아라크니에 사용할 데이터베이스 이름.',
    'dbprefix_desc' => '하나의 데이터베이스에서 여러 개의 아라크니 솔루션을 설치하여 운영하려면 반드시 이것을 변경하세요.',

    'sitename' => 'Site Name',
    'adminid' => 'Admin ID',
    'adminpass' => 'Admin Password',
    'adminname' => 'Admin Nickname',
    'adminemail' => 'Admin Email',

    'adminid_desc' => '아이디는 알파벳, 숫자, 밑줄, 하이픈, 마침표, @ 심볼만 가능합니다. 2자 이상 50자 이하로 가능합니다.',
    'adminpass_desc' => '보안을 위해 가능한 강력한 암호를 지정하세요. 암호는 8자리 이상으로 가능합니다.',
    'adminname_desc' => 'Input administrator name.',

    // Messages

    'm_install_step2_1' => '라이센스 내용을 반드시 확인하세요.<br>라이센스에 동의해야만 설치를 진행할 수 있습니다.',
    'm_install_step2_2' => '연결할 데이터베이스 정보를 입력하세요.',
    'm_install_step3' => '설치를 위해 다음 정보들을 입력하세요.',

    // Error Messages

    'e_critical' => '중대한 오류가 발생하였습니다. 어플리케이션을 새로 설치해 주세요.',
    'e_db_conn_failure' => '데이터베이스에 연결할 수 없습니다.',
    'e_db_init_failure' => '데이터베이스 초기화에 실패하였습니다.',
    'e_invalid_request' => '올바르지 않은 요청입니다.',
    'e_unknown' => '알 수 없는 오류입니다.',
    'e_users_add_failure' => '사용자 추가에 실패하였습니다.',

];
