<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Config\Database;
use RuntimeException;

/**
 * Settings Library Class
 * 설정 관련 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 *
 */
class Settings
{
    /** Settings Name(Key) Constants */

    /** @var string 홈페이지 기본 URL 주소 (끝에 슬래시(/)가 반드시 붙는다.) */
    const homeurl = 'homeurl';
    /** @var string 사이트 이름 */
    const name = 'name';
	/** @var string 사이트 설명 */
    const desc = 'desc';
	/** @var string 사이트 언어 */
    const locale = 'locale';
	/** @var string 사이트 생성 일시 */
    const dt_created = 'dt_created';
	/** @var string 사이트 테마 */
    const theme = 'theme';
	/** @var string 사이트 제목 표시 형식 */
    const title_format = 'title_format';

	/** @var string 날짜 표시 형식 */
	const date_format = 'date_format';
	/** @var string 시간 표시 형식 */
	const time_format = 'time_format';

	/** @var string 관리자 이메일 주소 */
    const admin_email = 'admin_email';
	/** @var string 관리 페이지 언어 */
    const admin_locale = 'admin_locale';

    /** @var string 메뉴 */
    const menu = 'menu';

	/** @var string 사용자 - 사용자를 추가할 때, 기본 역할 id */
    const users_default_ur_id = 'users_default_ur_id';
    /** @var string 사용자 필드 - 별명 사용여부 */
    const use_nickname = 'use_nickname';
	/** @var string 사용자 필드 - 성별 사용여부 */
	const use_gender = 'use_gender';
	/** @var string 사용자 필드 - 생년월일 사용여부 */
	const use_birthdate = 'use_birthdate';


    // Security

	/** @var string JWT 인증키 */
	const auth_jwt_key = 'auth_jwt_key';
	/** @var string 암복호화에 사용할 키 */
	const enc_key = 'enc_key';

    /* -------------------------------------------------------------------------------- */

    protected $items = [];

    protected $qb = null;

    /* -------------------------------------------------------------------------------- */

    /**
     * Initialize the table that be connected with this model.
     * 현재 모델과 연관된 테이블을 초기화 한다.
     *
     * @param BaseConnection $db
     * @return bool
     */
    protected function initTable(BaseConnection &$db = null) {
        if ($db === null) {
            $db = Database::connect();
        }

        $table = $db->prefixTable(Consts::DB_TABLE_SETTINGS);
        $ret = $db->query("
        CREATE TABLE IF NOT EXISTS `$table` (
            `key` varchar(64) NOT NULL,
            `value` mediumtext NOT NULL,
            PRIMARY KEY (`key`)
        ) " . Consts::SQL_CHARSET_COLLATE
        );
        if (! $ret) {
            return false;
        }

        // Set QueryBuilder
        $this->qb = $db->table(Consts::DB_TABLE_SETTINGS);

        // Base URL 등록
        $this->set( 'homeurl', BASEURL );

        return true;
    }

    /**
     * Constructor / 생성자
     *
     * @param BaseConnection $db
     */
    public function __construct(BaseConnection &$db = null)
    {
        if (getInstallCode() === 0) {
            return;
        }

        if ($db === null) {
            $db = Database::connect();
        }

        if ( ! $db->tableExists(Consts::DB_TABLE_SETTINGS) && ! $this->initTable($db) ) {
            throw new DatabaseException(_g('e_db_init_failure'));
        }

        $this->qb = $db->table(Consts::DB_TABLE_SETTINGS);

        // DB settings 모든 값을 로드한다.
        $query = $this->qb->get();
        $result = $query->getResultArray();
        foreach ($result as $row) {
            $this->items[$row['key']] = $row['value'];
        }
        
        // 기본 값 지정

    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if ( isset($this->items[$key]) ) {
            return $this->items[$key];
        } else {
            return null;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $except = [ 'qb', 'items' ];
        if ( in_array($key, $except) ) {
            throw new RuntimeException('This is a read-only variable.');
        } else {
            throw new RuntimeException('This is a read-only variable.');
        }
    }

    /* -------------------------------------------------------------------------------- */

    /**
     * 설정 값을 가져옵니다.
     *
     * @param string $key
     * @param mixed $defvalue
     * @return mixed
     */
    public function get($key, $defvalue = null)
    {
        // 이미 불러와진 배열에서 값을 얻어온다.
        if ( isset($this->items[$key]) ) return $this->items[$key];
        return $defvalue;
    }

    /**
     * 설정 값을 데이터베이스에 저장한다.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value)
    {
        if ($key === null || $key == '' || $value === null) return false;
        
        // 작업중...
        
        // 중요 설정변경은 관리자 권한 반드시 필요

        if ($this->qb->where('key', $key)->countAllResults() > 0) {
            $q = $this->qb->where('key', $key)->set('value', $value)->update();
        } else {
            $q = $this->qb->insert([ 'key' => $key, 'value' => $value ]);
        }
        if ($q) {
            $this->items[$key] = $value;
        }
        return (bool) $q;
    }

	/**
	 * 데이터베이스에 설정 값이 저장되어 있지 않다면 값을 저장한다.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
    public function setDefault($key, $value)
	{
		if ( isset($this->items[$key]) ) return false;

		return $this->set($key, $value);
	}

}
