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
class UAuthModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_UAUTH;

    /* Fields - BEGIN */

    const ua_id = 'ua_id';
    const ua_selector = 'ua_selector';
	const ua_token = 'ua_token';
	const ua_u_id = 'ua_u_id';
    const ua_dt_expire = 'ua_dt_expire';

    /* Fields - END */

    protected $primaryKey = self::ua_id;

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
            `ua_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `ua_selector` char(12) NOT NULL,
            `ua_token` char(64) NOT NULL,
            `ua_u_id` bigint unsigned NOT NULL,
            `ua_dt_expire` datetime NOT NULL,
            PRIMARY KEY (`ua_id`)
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
			static::ua_selector, static::ua_token, static::ua_u_id, static::ua_dt_expire,
		];

		// 부모 생성자
        parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
        $this->validationAllRules = [
			static::ua_id => [ 'rules' => 'is_natural_no_zero' ],
			static::ua_selector => [ 'rules' => 'required|max_length[12]|valid_base64' ],
			static::ua_token => [ 'rules' => 'required|max_length[64]' ],
			static::ua_u_id => [ 'rules' => 'required|is_natural_no_zero' ],
			static::ua_dt_expire => [ 'rules' => 'required|valid_dt_def' ],
        ];
        foreach (array_keys($this->validationAllRules) as $key) {
            $this->validationAllRules[$key]['label'] = _g('l_' . $key);
        }
	}

    /* -------------------------------------------------------------------------------- */

    /**
     * 주어진 사용자와 식별자에 해당하는 아이템을 삭제한다.
     *
     * @param integer $ua_u_id
     * @param mixed $ua_selector
     */
    public function deleteItem($ua_u_id, $ua_selector)
    {
        $this->where(self::ua_u_id, $ua_u_id);
        $this->where(self::ua_selector, $ua_selector);
        $this->delete();
    }

}
