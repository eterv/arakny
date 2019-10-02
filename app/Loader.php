<?php namespace Arakny;

use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;

/** Arakny Path */
defined( 'BASEURL' ) OR define( 'BASEURL', base_url() );

require_once 'Libraries/Functions.php';

/**
 * Arakny Loader Class
 * 아라크니 로더 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        https://arakny.com
 * @package     Arakny
 */
class Loader implements FilterInterface
{
	/**
	 * @inheritdoc
	 */
	public function before(RequestInterface $request)
	{
        // Validates package structures
        if ( !is_file(Consts::FILE_ENV) || !is_readable(Consts::FILE_ENV) || !is_writable(Consts::FILE_ENV) ) {
            //throw new \Error('An critical error has occurred. (Missing core file or permission denied.)');
            die( 'An critical error has occurred. (Missing core file or permission denied.)' );
        }
        $installcode = getInstallCode();

        // Set current controller name
        $directoryName = routerDirectory();
        $controllerName = routerController();
        $methodName = routerMethod();

        //log_message('critical', $directoryName . ' // ' . $controllerName . ' // ' . $methodName);

		/*
		// 임시.. 테스트용 .. 나중에 삭제...
		if ( $controllerName === 'a_content' ) {
			_log($_SERVER['PHP_SELF']);	// 주로 javascript map 파일...
		}
		*/

        //
        // 테스트용 임시 !!! (phpinfo 용)
        //
        if ($controllerName === 'phpinfo') {
            $this->phpinfo(); exit;
        } else if ($controllerName === 'reset') {
            return $this->reset();
        }

        switch ($installcode) {
            case 0:     // 완전 미설치 상태
                $this->loadBaseLibs();
                break;

            case 1:     // DB 만 설정된 상태 (여전히 미설치 상테)
                $this->loadDBAndModels();
                $this->loadBaseLibs();

				$this->loadSession(Database::connect());

                break;

            case 2:     // 완전 설치 상태
                $this->loadDBAndModels();
                $this->loadBaseLibs();
                $this->loadCommonLibs();
                break;
        }

        // 아직 설치되지 않았다면,
        if ( ! isInstalled() ) {
            // 이미 설치 페이지라면 통과, 아니라면 설치 페이지로 이동
            if ( $directoryName === 'admin/' && $controllerName === 'admin\install' ) {
                return null;
            } else {
                return redirectInstall();
            }
        }

        return null;
	}

	/**
     * @inheritdoc
	 */
	public function after(RequestInterface $request, ResponseInterface $response) { }

    /* -------------------------------------------------------------------------------- */

    /**
     * Connect database and load models.
     * 데이터베이스에 연결하고, 공용 모델 클래스들을 초기화한다.
     */
    protected function loadDBAndModels()
    {
        // Load Databases.
        Database::connect();

        // Load Common Models
        Services::userroles();
        Services::users();

        Services::docs();
        Services::files();

        // 퍼미션은 별도로...
		//Services::permissions();
    }

    /**
     * Load settings and l10n library classes.
     * 설정 및 지역화 라이브러리 클래스를 초기화한다.
     */
    protected function loadBaseLibs()
    {
        // Load App Settings
        Services::settings();

        // Load Common libraries.
        Services::l10n();
    }

    /**
     * Load common library classes.
     * 공용 라이브러리 클래스들을 초기화한다.
     *
     * 아라크니가 완전히 설치되었을 때에만 불러와야 한다.
     */
    protected function loadCommonLibs()
    {
        Services::auth();
        Services::theme();
        Services::page();

		Services::stat();
    }

    /**
     * Load session.
     * 세션을 로드한다.
     *
     * @param BaseConnection $db
     * @return boolean
     */
    protected function loadSession($db)
    {
        if (! $db->tableExists(Consts::DB_TABLE_SESSIONS)) {
            // Create sessions table
            $table = $db->prefixTable(Consts::DB_TABLE_SESSIONS);
            $ret = $db->query("
            CREATE TABLE IF NOT EXISTS `$table` (
                `id` varchar(128) NOT NULL,
                `ip_address` varchar(45) NOT NULL,
                `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
                `data` text NOT NULL,
                `u_id` bigint unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `{$table}_timestamp` (`timestamp`)
            ) " . Consts::SQL_CHARSET_COLLATE
            );
            if (!$ret) {
                return false;
            }
        }

        // Load session
        session();

        return true;
    }

    /* -------------------------------------------------------------------------------- */

    // 임시 - phpinfo 보기
    private function phpinfo()
    {
        phpinfo();
    }

	/**
	 * 임시 메소드 - Reset DB & installation
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
    private function reset()
    {
        //echo 'Why Reset ???'; return true;

        $installcode = getInstallCode();
        if ($installcode < 1) return redirect('/');

        session()->destroy();

        $dbforge = Database::forge();

        $dbforge->dropTable(Consts::DB_TABLE_SESSIONS, TRUE);
        $dbforge->dropTable(Consts::DB_TABLE_SETTINGS, TRUE);
        $dbforge->dropTable(Consts::DB_TABLE_USERROLES, TRUE);
        $dbforge->dropTable(Consts::DB_TABLE_ACTIONS, TRUE);
        $dbforge->dropTable(Consts::DB_TABLE_USERS, TRUE);
        $dbforge->dropTable(Consts::DB_TABLE_USERS_META, TRUE);
        $dbforge->dropTable(Consts::DB_TABLE_UAUTH, TRUE);
		$dbforge->dropTable(Consts::DB_TABLE_PERMISSIONS, TRUE);

		$dbforge->dropTable(Consts::DB_TABLE_DOCS, TRUE);
		$dbforge->dropTable(Consts::DB_TABLE_FILES, TRUE);

        // Reset PATH_ENV_FILE.
        $file = file_get_contents(Consts::FILE_ENV);
        $cfg = [
            'app.baseURL' => 'null',
            'app.defaultLocale' => '',
            'database.default.hostname' => '',
            'database.default.username' => '',
            'database.default.password' => '',
            'database.default.database' => '',
            'database.default.DBPrefix' => ''
        ];
        foreach ($cfg as $key => $value) {
            $file = preg_replace('/('.$key.')(\s|\t)*={1}(\s|\t)*(\'|\"){1}.*(\'|\"){1}/ix', $key.' = \''.$value.'\'', $file);
        }
        $file = preg_replace('/#INSTALL-CODE-\d{1}#/', '#INSTALL-CODE-0#', $file);
        file_put_contents(Consts::FILE_ENV, $file);

        return redirect('/');
    }

}
