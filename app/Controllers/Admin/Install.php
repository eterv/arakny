<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Libraries\Settings;
use Arakny\Models\UserRolesModel;
use Arakny\Models\UsersModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Installation Controller Class
 * 설치 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Install extends BaseController
{
    /**
     * Constructor / 생성자
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     *
     * @throws \CodeIgniter\HTTP\RedirectException
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // 이미 설치가 되어있는 경우라면
        // 슈퍼 관리자일 경우에, 아닐 경우에 분류하여 결과를 반환한다.
        if (isInstalled()) {
            // ■■■■■■■■■■ ... 미완성 ... ■■■■■■■■■■
            // exit;
        }

        $this->data['bootstrap_css'] = adminThemeUrl('assets/bootstrap.min.css');
        $this->data['jquery_js'] = adminThemeUrl('assets/js/jquery.min.js');
        $this->data['zxcvbn_js'] = adminThemeUrl('assets/js/zxcvbn.js');

        // Fetch CSRF Key, Hash
        $security = Services::security();
        $this->data['token']['name'] = $security->getCSRFTokenName();
        $this->data['token']['value'] = $security->getCSRFHash();

    }

    /**
     * Fist Page - Installation Step 1 - Select a default language
     * 첫 페이지 - 설치 단계 1 - 기본 언어 선택
     */
    public function index()
	{
	    $l10n = Services::l10n();

        $this->data['step'] = 1;

	    $this->data['next_step'] = 2;
        if ( getInstallCode() === 1 ) $this->data['next_step'] = 3;
        elseif ( getInstallCode() === 2 ) $this->data['next_step'] = 4;

        $this->data['locale'] = $l10n->getPreferredLanguage(false);

        $this->_view();
	}

    /**
     * Installation Step 2 - Agree License & Input Database Information
     * 설치 단계 2 - 라이센스 동의 및 DB 정보 입력
     */
	public function step2()
    {
        $this->data['step'] = 2;

        $post_keys = [ 'locale' ];
        foreach ($post_keys as $key) {
            $this->data[$key] = inputPost($key);
            if ( $this->data[$key] === null ) {
                return redirectInstall();
            }
        }

        $this->_view();
    }

    /**
     * Installation Step 3 - Set-up Application (Solution)
     * 설치 단계 3 - 솔루션 초기 설정
     */
    public function step3()
    {
        $this->data['step'] = 3;

        // DB 설정 검사
        if ( getInstallCode() < 1 ) {
            return redirectInstall();
        }

        $post_keys = [ 'locale' ];
        foreach ($post_keys as $key) {
            $this->data[$key] = inputPost($key);
            if ( $this->data[$key] === null ) {
                return redirectInstall();
            }
        }

        $this->_view();
    }

    /**
     * Installation Step 4 - Complete Set-up
     * 설치 단계 4 - 설치 완료
     */
    public function step4()
    {
        $this->data['step'] = 4;

        // 설치 마무리 검사
        if ( ! isInstalled() ) {
            return redirectInstall();
        }

        $post_keys = [ 'locale' ];
        foreach ($post_keys as $key) {
            $this->data[$key] = inputPost($key);
            if ( $this->data[$key] === null ) {
                return redirectInstall();
            }
        }

        $this->_view();
    }

    /**
     * AJAX Process - Trys to connect database and initializes ( in Installation Step 2 )
     * AJAX 처리 - 데이터베이스 연결 테스트 및 초기화 처리 (설치 단계 2 에서)
     */
    public function db_process()
    {
        $this->checkAjaxMethodWith404();

        $default = config('Database')->default;
        $default['hostname'] = inputPost('dbhost');
        $default['username'] = inputPost('dbuser');
        $default['password'] = inputPost('dbpass');
        $default['database'] = inputPost('dbname');
        $default['DBPrefix'] = inputPost('dbprefix');

        foreach ($default as $key => $value) {
            if ( $value === null ) {
                return $this->fail(_g(Consts::E_INVALID_REQUEST));
            }
            $cfg[$key] = str_replace('\'', '\\\'', $value);
        }

        // Try to connect database.
        $db = Database::connect($default, false);
        try { $db->initialize(); } catch (\Exception $ex) {  }
        if ( $db->connID === false ) {
            return $this->fail(_g(Consts::E_DB_CONN_FAILURE));
        }

        // Update Consts::FILE_ENV.
        $file = file_get_contents(Consts::FILE_ENV);
        $cfg = array_intersect_key($default, array_flip([ 'hostname', 'username', 'password', 'database', 'DBPrefix' ]));
        foreach ($cfg as $key => $value) {
            $file = preg_replace('/(database\.default\.'.$key.')(\s|\t)*={1}(\s|\t)*(\'|\"){1}.*(\'|\"){1}/ix',
                'database.default.'.$key.' = \''.$value.'\'', $file);
        }
        $file = preg_replace('/#INSTALL-CODE-0#/', '#INSTALL-CODE-1#', $file);
        file_put_contents(Consts::FILE_ENV, $file);

        // Initialize Database & Generate Tables & set some of settings
        $dbhelper = Services::dbhelper();
        if ( ! $dbhelper->initializeDatabase($db) ) {
            return $this->fail(_g(Consts::E_DB_INIT_FAILURE));
        }

        return $this->succeed();
    }

    /**
     * AJAX Process - Finish installation ( in Installation Step 3 )
     * AJAX 처리 - 설치 마무리 (설치 단계 3 에서)
     */
    public function finish()
    {
        $this->checkAjaxMethodWith404();

        // Check whether DB is set
        if (getInstallCode() !== 1) {
            return $this->fail(_g(Consts::E_INVALID_REQUEST));
        }

        // Load settings
        $settings = Services::settings();

        // Check POST Datas
        $post_keys = [ 'locale', 'sitename', 'adminid', 'adminpass', 'adminname', 'adminemail' ];
        $data = [];
        foreach ($post_keys as $key) {
            $data[$key] = inputPost($key);
            if ( $data[$key] === null ) {
                return $this->fail(_g(Consts::E_INVALID_REQUEST));
            }
        }

        // Load libraries.
        $userroles = Services::userroles();
        $users = Services::users();

        // Initialize setting values
        $settings->set( Settings::name, $data['sitename'] );
        $settings->set( Settings::desc, $data['sitename'] );
        $settings->set( Settings::locale, $data['locale'] );
        $settings->set( Settings::dt_created, date(Consts::DB_DATETIME_FORMAT) );
        $settings->set( Settings::theme, 'thema001' );
        $settings->set( Settings::title_format, '{{title}} - {{site_name}}' );

        $settings->set( Settings::date_format, 'Y-m-d' );
        $settings->set( Settings::date_format, 'H:i' );

        $settings->set( Settings::admin_email, $data['adminemail'] );
        $settings->set( Settings::admin_locale, $data['locale'] );

        $settings->set( Settings::users_default_ur_id, $userroles->getIdFromField(UserRolesModel::ur_name, 'general') );
		$settings->set( Settings::auth_jwt_key, _generateEncryptionKey() );
		$settings->set( Settings::enc_key, _generateEncryptionKey() );

        // Add Super Administrator in users table
        $admin_data = [
            UsersModel::u_login => $data['adminid'],
            UsersModel::u_pass => $data['adminpass'],
            UsersModel::u_ur_id => $userroles->getIdFromField(UserRolesModel::ur_name, 'superadmin'),
            UsersModel::u_name => $data['adminname'],
            UsersModel::u_nickname => $data['adminname'],
            UsersModel::u_email => $data['adminemail'],
			UsersModel::u_is_auth => true,
        ];
        $result = $users->add($admin_data);
        if (! $result) {
            $errors = $users->errors();
            $error_data = null;
            if ( is_array($errors) ) {
                foreach ($errors as $key => $value) {
                    if ($key === UsersModel::u_login) $key = 'adminid';
                    if ($key === UsersModel::u_pass) $key = 'adminpass';
                    if ($key === UsersModel::u_nickname) $key = 'adminname';
                    if ($key === UsersModel::u_email) $key = 'adminemail';

                    $error_data = [ 'library' => 'validation', 'field' => $key, 'message' => $value ];
                    break;
                }
            } else {
                $error_data = [ 'library' => 'database', 'message' => $errors ];
            }
            return $this->fail(_g(Consts::E_USERS_ADD_FAILURE), $error_data);
        }

        // Update Consts::FILE_ENV.
        $file = file_get_contents(Consts::FILE_ENV);
        $cfg = [];
        $cfg['app.baseURL'] = BASEURL;
        $cfg['app.defaultLocale'] = $data['locale'];

        foreach ($cfg as $key => $value) {
            if ($value == 'true' || $value == 'false' || is_numeric($value)) {
                $file = preg_replace('/('.$key.')(\s|\t)*=?(\s|\t)*(TRUE|FALSE|\d+)/ix', $key.' = '.$value, $file);
            } else {
                $value = str_replace('\'', '\\\'', $value);
                $file = preg_replace('/('.$key.')(\s|\t)*=?(\s|\t)*(\'|\"){1}.*(\'|\"){1}/ix', $key.' = \''.$value.'\'', $file);
            }
        }
        $file = preg_replace('/#INSTALL-CODE-1#/', '#INSTALL-CODE-2#', $file);
        file_put_contents(Consts::FILE_ENV, $file);

        return $this->succeed();
    }

}
