<?php

use Arakny\Constants\Consts;

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

    // Command

    'installation' => '설치',

    'ok' => '확인',
    'cancel' => '취소',
    'yes' => '네',
    'no' => '아니오',
    'prev' => '이전',
    'next' => '다음',
    'show' => '표시',
    'hide' => '숨김',

	'add' => '추가',
	'back' => '이전',
	'edit' => '수정',
	'delete' => '삭제',
	'format' => '표시 형식',
	'group' => '그룹',
	'menu' => '메뉴',
	'none' => '없음',
	'save' => '저장',
	'selectmode' => '선택 모드',
	'settings' => '관리',
	'site' => '사이트',
	'siteinfo' => '사이트 정보',
	'sort' => '정렬',
	'task' => '작업',

    'agree' => '동의',
    'agree_and_signup' => '동의 및 가입',
	'captcha' => '캡챠',
	'captcha_input' => '캡챠 입력',
    'leave' => '탈퇴',
    'login' => '로그인',
    'logout' => '로그아웃',
    'rememberme' => '자동 로그인 사용',
    'signup' => '가입',

	'admin' => '관리자',
	'guest' => '비회원',
	'member' => '회원',
	'user' => '사용자',

	'male' => '남자',
	'female' => '여자',


	/* ////////////////////////////////////////////////////////////////
	 *		Labels
	 * ///////////////////////////////////////////////////////////// */

	// List of User-Role

	'l_ur_superadmin' => '최고관리자',
	'l_ur_admin' => '관리자',
	'l_ur_general' => '일반회원',

	// Settings

	'l_s_homeurl' => '기반 URL',
	'l_s_name' => '사이트 이름',
	'l_s_desc' => '사이트 설명',
	'l_s_locale' => '사이트 언어',
	'l_s_dt_created' => '사이트 생성 일시',
	'l_s_theme' => '사이트 테마',
	'l_s_title_format' => '사이트 제목 표시 형식',

	'l_s_date_format' => '날짜 표시 형식',
	'l_s_time_format' => '시간 표시 형식',

	'l_s_admin_email' => '관리자 이메일 주소',
	'l_s_admin_locale' => '관리 페이지 언어',

	'l_s_menu' => '메뉴',

	'l_s_users_default_ur_id' => '사용자 기본 역할',

	'l_s_users_fields' => '사용자 정보 필드',
	'l_s_use_nickname' => '별명 (닉네임) 사용',
	'l_s_use_gender' => '성별 사용',
	'l_s_use_birthdate' => '생년월일 사용',


	// Userroles

	'l_userroles' => '사용자 역할',

    'l_ur_id' => '역할 ID',
    'l_ur_name' => '역할 이름',
    'l_ur_text' => '역할 텍스트',
    'l_ur_order' => '역할 순서',

	// Users

	'l_users' => '사용자',

    'l_u_id' => '사용자 nid',
    'l_u_login' => '아이디',
    'l_u_pass' => '비밀번호',
    'l_u_pass_check' => '비밀번호 확인',
    'l_u_ur_id' => '역할 ID',
    'l_u_name' => '이름',
    'l_u_nickname' => '별명',
    'l_u_email' => '이메일',
    'l_u_phone' => '휴대폰 번호',
    'l_u_gender' => '성별',
    'l_u_birthdate' => '생년월일',
    'l_u_dt_joined' => '가입 일시',
    'l_u_dt_lastlogin' => '마지막 로그인 일시',
    'l_u_dt_lastchpass' => '마지막 암호 변경 일시',
    'l_u_is_auth' => '인증 여부',
    'l_u_authcode' => '인증코드',
    'l_u_dt_authcode' => '인증코드 발급 일시',
    'l_u_is_blocked' => '차단 여부',
    'l_u_is_deleted' => '탈퇴 여부',
    'l_u_dt_deleted' => '탈퇴 일시',

	// Uauth

    'l_ua_id' => '사용자 인증 ID',
    'l_ua_u_id' => '사용자 nid',
    'l_ua_identifier' => '식별자',
    'l_ua_token' => '토큰',
    'l_ua_dt_expire' => '만료 일시',

	// Login

	'l_lo_id' => '로그인 기록 ID',
	'l_lo_u_id' => '사용자 nid',
	'l_lo_ip' => 'IP 주소',
	'l_lo_agent' => '사용자 에이전트',
	'l_lo_browser' => '브라우저',
	'l_lo_dt' => '로그인 일시',
	'l_lo_dt_last' => '마지막 연결 일시',

	// Permissions

	'l_p_id' => '퍼미션 ID',
	'l_p_pagetype' => '페이지 종류',
	'l_p_page' => '페이지 식별자',
	'l_p_ur_id' => '역할 ID',
	'l_p_u_id' => '사용자 nid',
	'l_p_mode' => '허용된 작업',

	// Docs

	'l_docs' => '일반 문서',

	'l_d_id' => '문서 ID',
	'l_d_name' => '문서 이름',
	'l_d_title' => '문서 제목',
	'l_d_content_type' => '내용 종류',
	'l_d_content' => '내용',
	'l_d_path' => '문서 경로',
	'l_d_use_header_footer' => '공용 헤더(Header) 및 푸터(Footer) 사용',
	'l_d_is_wide' => '넓은 와이드 페이지 여부',

	'l_d_auth_read' => '읽기 권한',

	'l_d_hit' => '조회',

	'l_d_u_id_created' => '작성한 사용자 nid',
	'l_d_dt_created' => '작성 일시',
	'l_d_u_id_updated' => '수정한 사용자 nid',
	'l_d_dt_updated' => '수정 일시',

	'l_d_content_type_0' => '에디터',
	'l_d_content_type_1' => '외부 파일',

	'l_d_auth_read_default' => '모두 (*)',

	'l_d_etc_options' => '기타 옵션2',


	// Files

	'l_f_id' => '파일 ID',
	'l_f_u_id' => '사용자 nid',
	'l_f_pagetype' => '파일 업로드 지점',
	'l_f_page' => '파일 업로드 페이지 ID',

	'l_f_path' => '파일 경로',
	'l_f_name' => '파일 이름',

	'l_f_size' => '파일 크기',
	'l_f_width' => '이미지 너비',
	'l_f_height' => '이미지 높이',
	'l_f_type' => '파일 종류',

	'l_f_dt_uploaded' => '업로드 일시',


	// Menu

	'l_m_id' => '메뉴 ID',
	'l_m_label' => '메뉴 라벨',
	'l_m_linktype' => '링크 종류',
	'l_m_link' => '링크',
	'l_m_target' => '타겟',

	'l_m_linktype_' . Consts::MENU_LINKTYPE_NONE => '링크 없음',
	'l_m_linktype_' . Consts::MENU_LINKTYPE_TOPLEVELITEM => '하위메뉴 첫번째 아이템 동일',
	'l_m_linktype_' . Consts::MENU_LINKTYPE_DOCS => '일반 문서',
	'l_m_linktype_' . Consts::MENU_LINKTYPE_BOARDS => '게시판',
	'l_m_linktype_' . Consts::MENU_LINKTYPE_URL => '사용자 정의 URL',


	/* ////////////////////////////////////////////////////////////////
	 *		Fields & Descriptions
	 * ///////////////////////////////////////////////////////////// */

    'dbhost' => 'DB 호스트',
    'dbuser' => '사용자명',
    'dbpass' => '암호',
    'dbname' => 'DB 이름',
    'dbprefix' => '테이블 접두어',

    'dbhost_desc' => 'localhost가 작동하지 않는다면 이 정보는 웹호스팅 서비스 업체에서 받을 수 있습니다.',
    'dbuser_desc' => '데이터베이스 사용자명.',
    'dbpass_desc' => '데이터베이스 암호.',
    'dbname_desc' => '아라크니에 사용할 데이터베이스 이름.',
    'dbprefix_desc' => '하나의 데이터베이스에서 여러 개의 아라크니 솔루션을 설치하여 운영하려면 반드시 이것을 변경하세요.',

    'sitename' => '사이트 이름',
    'adminid' => '관리자 아이디',
    'adminpass' => '관리자 암호',
    'adminname' => '관리자 이름',
    'adminemail' => '관리자 이메일',

    'adminid_desc' => '아이디는 알파벳, 숫자, 밑줄, 하이픈, 마침표, @ 심볼만 가능합니다. 2자 이상 50자 이하로 가능합니다.',
    'adminpass_desc' => '보안을 위해 가능한 강력한 암호를 지정하세요. 암호는 8자리 이상으로 가능합니다.',
    'adminname_desc' => '관리자의 이름을 입력하세요.',

    // Messages

    'm_install_step2_1' => '라이센스 내용을 반드시 확인하세요.<br>라이센스에 동의해야만 설치를 진행할 수 있습니다.',
    'm_install_step2_2' => '연결할 데이터베이스 정보를 입력하세요.',
    'm_install_step3' => '설치를 위해 다음 정보들을 입력하세요.',

    // Error Messages

    Consts::E_AUTH_LOGIN_FAILURE => '아이디 또는 비밀번호를 다시 확인하세요.<br>등록되지 않은 아이디거나, 아이디 또는 비밀번호를 잘못 입력하셨습니다.',

    Consts::E_CRITICAL => '중대한 오류가 발생하였습니다. 어플리케이션을 새로 설치해 주세요.',

    Consts::E_DB_CONN_FAILURE => '데이터베이스에 연결할 수 없습니다.',
    Consts::E_DB_INIT_FAILURE => '데이터베이스 초기화에 실패하였습니다.',
    Consts::E_DB_INSERT_FAILURE => '데이터베이스 Insert 작업에 실패하였습니다.',
    Consts::E_DB_UPDATE_FAILURE => '데이터베이스 Update 작업에 실패하였습니다.',
    Consts::E_DB_DELETE_FAILURE => '데이터베이스 Delete 작업에 실패하였습니다.',
    Consts::E_DB_SELECT_FAILURE => '데이터베이스 Select 작업에 실패하였습니다.',

    Consts::E_FILE_DELETE_FAILURE => '파일 삭제에 할 수 없습니다.',
    Consts::E_FILE_NOTFOUND => '{0} 파일이 존재하지 않습니다.',
    Consts::E_FILE_UPLOAD_FAILURE => '파일 업로드에 실패하였습니다.',
    Consts::E_FILE_UPLOAD_EXCEED_SIZE => '파일이 업로드 최대 크기를 초과하였습니다.',
    Consts::E_FILE_UPLOAD_WRONG_TYPE => '업로드 할 수 없는 파일 형식입니다.',

    Consts::E_INVALID_ARGUMENT => '올바르지 않은 매개변수 입니다.',
    Consts::E_INVALID_CAPTCHA => 'Captcha 값이 올바르지 않습니다.',
    Consts::E_INVALID_REQUEST => '올바르지 않은 요청입니다.',

    Consts::E_SEC_COOKIE_HIJACKING => '악의적인 누군가에 의해 쿠키 탈취가 발생하였습니다. [ 탈취된 사용자 nid :: {0} ]',

    Consts::E_USERS_ADD_FAILURE => '사용자 추가에 실패하였습니다.',

    Consts::E_UNKNOWN => '알 수 없는 오류입니다.',

];
