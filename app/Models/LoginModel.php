<?php namespace Arakny\Models;

use Arakny\BaseModel;
use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;

/**
 * User Auth Model Class
 * 사용자 인증 모델 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class LoginModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_UAUTH;

    /* Fields - BEGIN */

    const lo_id = 'lo_id';
    const lo_u_id = 'lo_u_id';
    const lo_ip = 'lo_ip';
    const lo_agent = 'lo_agent';
    const lo_browser = 'lo_browser';
    const lo_dt = 'lo_dt';
    const lo_dt_last = 'lo_dt_last';

    /* Fields - END */

    protected $primaryKey = self::lo_id;

	/* -------------------------------------------------------------------------------- */
	/* 		Initialization (초기화)
	/* -------------------------------------------------------------------------------- */

    /**
     * @inheritdoc
     */
    public function initTable() {
        $table = $this->db->prefixTable($this->table);
        $ret = $this->db->query("
        CREATE TABLE IF NOT EXISTS `$table` (
            `lo_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `lo_u_id` bigint unsigned NOT NULL,
            `lo_ip` varchar(45) NOT NULL,
            `lo_browser` varchar(32) NOT NULL,
            `lo_dt` datetime NOT NULL,
            `lo_dt_last` datetime NOT NULL,
            PRIMARY KEY (`lo_id`)
        ) " . Consts::SQL_CHARSET_COLLATE
        );
        if (! $ret) {
            return false;
        }

        return true;
    }

    /**
     * Constructor / 생성자
     *
     * @param BaseConnection $db
     * @param Validation $validation
     */
    public function __construct(BaseConnection &$db = null, Validation $validation = null)
    {
		// 기본 허용 필드 목록 정의
		$this->defaultAllowedFields = [
			static::lo_u_id, static::lo_ip, static::lo_agent, static::lo_browser,
			static::lo_dt, static::lo_dt_last,
		];

		// 부모 생성자
        parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
        $this->validationAllRules = [
			static::lo_id => [ 'rules' => 'is_natural_no_zero' ],
			static::lo_u_id => [ 'rules' => 'required|is_natural_no_zero' ],
			static::lo_ip => [ 'rules' => 'required|valid_ip' ],
			static::lo_browser => [ 'rules' => 'required|max_length[32]' ],
			static::lo_dt => [ 'rules' => 'required|valid_dt_def' ],
			static::lo_dt_last => [ 'rules' => 'required|valid_dt_def' ],
        ];
		foreach (array_keys($this->validationAllRules) as $key) {
			$this->validationAllRules[$key]['label'] = _g('l_' . $key);
		}

		// 삽입 전 이벤트 연결
		$this->beforeInsert[] = 'onBeforeInsert';
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Events (이벤트)
	/* -------------------------------------------------------------------------------- */

	/**
	 * before the insert operation, this event occurs.
	 * insert 작업 전에, 이 이벤트가 발생한다.
	 *
	 * @param array $datas
	 * @return array
	 */
	protected function onBeforeInsert(array $datas)
	{
		$ua = service_useragent();

		// 기본 정보들 저장
		$datas['data'][static::lo_ip] = _ipAddress();
		$datas['data'][static::lo_browser] = $ua->getBrowser();
		$datas['data'][static::lo_dt] = date(Consts::DB_DATETIME_FORMAT);
		$datas['data'][static::lo_dt_last] = date(Consts::DB_DATETIME_FORMAT);

		return $datas;
	}

}
