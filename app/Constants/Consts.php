<?php namespace Arakny\Constants;

/**
 * Base Constants Class
 * 기본 상수 모음 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */
final class Consts
{

    /** Default Database character set and collate */
    const DB_CHARSET = 'utf8mb4';
    const DB_COLLATE = 'utf8mb4_unicode_ci';
    const SQL_CHARSET_COLLATE = ' DEFAULT CHARACTER SET ' . self::DB_CHARSET . ' COLLATE ' . self::DB_COLLATE ;

    const DB_DATE_FORMAT = 'Y-m-d';
    const DB_TIME_FORMAT = 'H:i:s';
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** List of Arakny core tables */
    const DB_TABLE_SESSIONS = 'sessions';
    const DB_TABLE_SETTINGS = 'settings';
    const DB_TABLE_USERROLES = 'userroles';
    const DB_TABLE_ACTIONS = 'actions';
    const DB_TABLE_USERS = 'users';
    const DB_TABLE_USERS_META = 'users_meta';
    const DB_TABLE_UAUTH = 'uauth';
	const DB_TABLE_PERMISSIONS = 'permissions';
    const DB_TABLE_DOCS = 'docs';
    const DB_TABLE_FILES = 'files';
    const DB_TABLE_LOGIN = 'login';
    const DB_TABLE_STAT = 'stat';

    /* -------------------------------------------------------------------------------- */

    /** .env config file path */
    const FILE_ENV = APPPATH . '.env';

    /* -------------------------------------------------------------------------------- */

    const FORDER_CONTENT = 'a-content';
    const FORDER_ADMIN = 'admin';
    const FORDER_BASE_THEME = 'base';
    const FORDER_THEMES = 'themes';
    const FORDER_UPLOADS = 'uploads';

    /* -------------------------------------------------------------------------------- */

    /** Errors */

    const E_AUTH_LOGIN_FAILURE = 'e_login_failure';

    const E_CRITICAL = 'e_critical';

    const E_DB_CONN_FAILURE = 'e_db_conn_failure';
    const E_DB_INIT_FAILURE = 'e_db_init_failure';
    const E_DB_INSERT_FAILURE = 'e_db_insert_failure';
    const E_DB_UPDATE_FAILURE = 'e_db_update_failure';
    const E_DB_DELETE_FAILURE = 'e_db_delete_failure';
    const E_DB_SELECT_FAILURE = 'e_db_select_failure';

    const E_FILE_DELETE_FAILURE = 'e_file_delete_failure';
    const E_FILE_NOTFOUND = 'e_file_notfound';
    const E_FILE_UPLOAD_FAILURE = 'e_file_upload_failure';
    const E_FILE_UPLOAD_EXCEED_SIZE = 'e_file_upload_exceed_size';
	const E_FILE_UPLOAD_WRONG_TYPE = 'e_file_upload_wrong_type';

    const E_INVALID_ARGUMENT = 'e_invalid_argument';
    const E_INVALID_CAPTCHA = 'e_invalid_captcha';
    const E_INVALID_REQUEST = 'e_invalid_request';

    const E_SEC_COOKIE_HIJACKING = 'e_sec_cookie_hijacking';

    const E_USERS_ADD_FAILURE = 'e_users_add_failure';

    const E_UNKNOWN = 'e_unknown';

    /* -------------------------------------------------------------------------------- */

	const FILETYPE_AUDIO = 'a';
	const FILETYPE_GENERAL = 'g';
	const FILETYPE_IMAGE = 'i';
	const FILETYPE_VIDEO = 'v';

	const PAGETYPE_ADMIN = 'a';
	const PAGETYPE_BOARD = 'b';
	const PAGETYPE_DOC = 'd';
	const PAGETYPE_FILEEXPLORER = 'f';
	const PAGETYPE_TEMP = 't';

	const MENU_LINKTYPE_NONE = '0';
	const MENU_LINKTYPE_TOPLEVELITEM = 't';
	const MENU_LINKTYPE_DOCS = 'd';
	const MENU_LINKTYPE_BOARDS = 'b';
	const MENU_LINKTYPE_URL = 'u';

    /* -------------------------------------------------------------------------------- */

    const SESS_CAPTCHA_CODE = 'captcha_code';

}
