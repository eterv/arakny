<?php namespace Arakny\Models;

use Arakny\BaseModel;
use Arakny\Constants\Consts;
use Arakny\Libraries\Settings;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;
use Config\Services;

// 작업 사항 ::
//
// @todo 추후... 사용자 정의 user 필드(meta) 어떻게 연동하여 db 에 삽입할 것인지 연구

// - 삽입, 삭제, 수정 작업 모두 처리해야..
// - DB 초기화시에 최고관리자 추가하기.

/**
 * Users Management Model Class
 * 사용자 관리 모델 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class UsersModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_USERS;

    /* Fields - BEGIN */

    const u_id = 'u_id';
    const u_login = 'u_login';
    const u_pass = 'u_pass';
    const u_pass_check = 'u_pass_check';	// 가상 필드
    const u_ur_id = 'u_ur_id';

    const u_name = 'u_name';
    const u_nickname = 'u_nickname';
    const u_email = 'u_email';
    const u_phone = 'u_phone';
    const u_gender = 'u_gender';
    const u_birthdate = 'u_birthdate';

    const u_dt_joined = 'u_dt_joined';
    const u_dt_lastlogin = 'u_dt_lastlogin';
    const u_dt_lastchpass = 'u_dt_lastchpass';

    const u_is_auth = 'u_is_auth';
    const u_authcode = 'u_authcode';
    const u_dt_authcode = 'u_dt_authcode';

    const u_is_blocked = 'u_is_blocked';
    const u_is_deleted = 'u_is_deleted';
    const u_dt_deleted = 'u_dt_deleted';

    /* Fields - END */

    protected $primaryKey = 'u_id';

    protected $useSoftDeletes = true;
    protected $deletedField = 'u_is_deleted';

    // delete 필드 설정 해야함..

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
            `u_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `u_login` varchar(100) NOT NULL,
            `u_pass` varchar(255) NOT NULL,
            `u_ur_id` smallint unsigned NOT NULL,
            
            `u_name` varchar(100) NOT NULL DEFAULT '',
            `u_nickname` varchar(50) NOT NULL DEFAULT '',
            `u_email` varchar(100) NOT NULL DEFAULT '',
            `u_phone` varchar(32) NOT NULL DEFAULT '',
            
            /* @todo 성별, 생년월일 삽입 해야.... */
            
            `u_dt_joined` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `u_dt_lastlogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            `u_dt_lastchpass` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            
            `u_is_auth` bool NOT NULL DEFAULT FALSE,
            `u_authcode` varchar(32) NOT NULL DEFAULT '',
            `u_dt_authcode` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            
            `u_is_blocked` bool NOT NULL DEFAULT FALSE,
            `u_is_deleted` bool NOT NULL DEFAULT FALSE,
            `u_dt_deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            
            PRIMARY KEY (`u_id`),
            UNIQUE KEY `u_login` (`u_login`),
            KEY `u_nickname` (`u_nickname`),
            KEY `u_email` (`u_email`)
        ) " . Consts::SQL_CHARSET_COLLATE
        );
        if (! $ret) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function __construct(BaseConnection &$db = null, Validation $validation = null)
    {
		// 기본 허용 필드 목록 정의
		$this->defaultAllowedFields = [
			static::u_login, static::u_pass, static::u_ur_id,
			static::u_name, static::u_nickname, static::u_email, static::u_phone,
			static::u_dt_joined, static::u_dt_lastlogin, static::u_dt_lastchpass,
			static::u_is_auth, static::u_authcode, static::u_dt_authcode,
			static::u_is_blocked, static::u_is_deleted, static::u_dt_deleted,
		];

		// 부모 생성자
		parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
        $this->validationAllRules = [
            static::u_id => [ 'rules' => 'is_natural_no_zero' ],
			static::u_login => [ 'rules' => 'required|min_length[2]|max_length[100]|regex_match[/^[A-Za-z0-9@._-]+$/]|is_unique['.$this->table.'.u_login,u_id,{u_id}]' ],
			static::u_pass => [ 'rules' => 'required|min_length[4]|max_length[100]|alnum_specialchars' ],
			static::u_pass_check => [ 'rules' => 'required|matches['.static::u_pass.']' ],
			static::u_ur_id => [ 'rules' => 'required|is_natural_no_zero' ],

			static::u_name => [ 'rules' => 'required|min_length[2]|max_length[100]' ],
			static::u_nickname => [ 'rules' => 'required|min_length[1]|max_length[50]|is_unique['.$this->table.'.u_nickname,u_id,{u_id}]' ],
			static::u_email => [ 'rules' => 'required|min_length[5]|max_length[100]|valid_email|is_unique['.$this->table.'.u_email,u_id,{u_id}]' ],
			static::u_phone => [ 'rules' => 'required|min_length[10]|max_length[32]|regex_match[/^[0-9+-]+$/]' ],
			static::u_gender => [ 'rules' => 'required|in_list[m,f,t]' ],
			static::u_birthdate => [ 'rules' => 'required|valid_date_def' ],

			static::u_dt_joined => [ 'rules' => 'required|valid_dt_def' ],
			static::u_dt_lastlogin => [ 'rules' => 'required|valid_dt_def' ],
			static::u_dt_lastchpass => [ 'rules' => 'required|valid_dt_def' ],

			static::u_is_auth => [ 'rules' => 'required|bool' ],
			static::u_authcode => [ 'rules' => 'required|exact_length[10]|alnum_specialchars' ],
			static::u_dt_authcode => [ 'rules' => 'required|valid_dt_def' ],

			static::u_is_blocked => [ 'rules' => 'required|bool' ],
			static::u_is_deleted => [ 'rules' => 'required|bool' ],
			static::u_dt_deleted => [ 'rules' => 'required|valid_dt_def' ],
        ];
        foreach (array_keys($this->validationAllRules) as $key) {
            $this->validationAllRules[$key]['label'] = _g('l_' . $key);
        }

		// 이벤트 연결

		// 삽입 전 이벤트 연결
		$this->beforeInsert[] = 'onBeforeInsert';

		// 수정 전 이벤트 연결
		$this->beforeUpdate[] = 'onBeforeUpdate';

    }


	/* -------------------------------------------------------------------------------- */
	/* 		Get (Select) (조회)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 목록을 순서 기준 가나다순 정렬하여 전부 가져온다.
	 *
	 * @param array|string $select
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAll($select = '*', int $limit = 0, int $offset = 0)
	{
		$this->orderBy(static::u_login, 'ASC');
		return parent::getAll($select, $limit, $offset);
	}

	/**
	 * 이름 기준 가나다순 정렬하여 모든 열의 요약을 가져온다.
	 *
	 * @param array|string $select
	 * @param int $limit
	 * @param int $offset
	 * @return array|null
	 */
	public function getAllSummary($select = '*', int $limit = 0, int $offset = 0)
	{
		// 관리자 페이지 내에서, 관리자 등급만 허용
		if (! isAdminPage() || ! isAdminLevel()) {
			return null;
		}

		// 필드 내에서 요약 데이터 선택
		if ($select === '*') {
			$select = [
				static::u_id, static::u_login, static::u_name, static::u_nickname, static::u_email,
				static::u_dt_joined,
			];
		}

		$this->orderBy(static::u_login, 'ASC');
		$result = parent::getAll($select, $limit, $offset);
		if ($result === null) return $result;

		// 추가로 포함할 요약 데이터
		foreach ($result as & $item) {
			$item['url_edit'] = adminUrl('users/write/' . $item[static::u_id]);
			$item['url_delete'] = adminUrl('users/delete/' . $item[static::u_id]);
		}
		unset($item);

		return $result;
	}

	/**
	 * Returns user role name
	 * 사용자 nid 값에 해당하는 역할 이름을 반환한다.
	 *
	 * @param int $u_id
	 * @return mixed
	 */
	public function getUserRoleName($u_id)
	{
		$userroles = Services::userroles();

		// 두 테이블을 JOIN 하여 사용자의 역할 이름을 가져온다.
		$query = $userroles->select('ur_name')
			->join($this->table, 'ur_id = u_ur_id')
			->where('u_id', $u_id)
			->get();
		$row = $query->getRowArray();
		return $row['ur_name'];
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Check (확인)
	/* -------------------------------------------------------------------------------- */

    /**
     * Returns whether the specified `u_email` value is unique in the table.
     * 테이블에서 지정한 `u_email` 값이 기존에 없는 새 값인지 여부를 반환한다.
     *
     * @param string $value
     * @return bool
     */
    public function isEmailUnique($value) {
        return ! $this->isFieldValueExists(static::u_email, $value);
    }

    /**
     * Returns whether the specified `u_login` value is unique in the table.
     * 테이블에 지정한 `u_login` 값이 기존에 없는 새 값인지 여부를 반환한다.
     *
     * @param string $value
     * @return bool
     */
    public function isLoginUnique($value) {
        return ! $this->isFieldValueExists(static::u_login, $value);
    }

    /**
     * Returns whether the given `u_login` value is possible to login.
     * 지정한 `u_login` 값으로 로그인을 할 수 있는지 여부를 반환한다.
     *
     * @param string $value
     * @return bool
     */
    public function isLoginPossible($value) {
        $query = $this->builder()->select(static::u_login)
            ->getWhere([ static::u_login => $value, static::u_is_deleted => false ]);
        return !empty($query->getResultArray());
    }

    /**
     * Returns whether the specified `u_nickname` value is unique in the table.
     * 테이블에 지정한 `u_nickname` 값이 기존에 없는 새 값인지 여부를 반환한다.
     *
     * @param string $value
     * @return bool
     */
    public function isNicknameUnique($value) {
        return ! $this->isFieldValueExists(static::u_nickname, $value);
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
		// 비밀번호 - 해시(hash) 처리
		$datas['data'][static::u_pass] = $this->getPasswordHash($datas['data'][static::u_pass]);

		// 역할 id - 기본값 : general (일반회원)
		if ( ! isset($datas['data'][static::u_ur_id]) ) {
			$datas['data'][static::u_ur_id] = $this->settings->get(Settings::users_default_ur_id);
		}

		// 가입 일시
		$datas['data'][static::u_dt_joined] = _now();
		// 마지막 로그인 일시
		$datas['data'][static::u_dt_lastlogin] = '0000-00-00 00:00:00';
		// 마지막 암호 변경 일시
		$datas['data'][static::u_dt_lastchpass] = _now();

		// 인증 여부
		if ( ! isset( $datas['data'][static::u_is_auth] ) ) {
			$datas['data'][static::u_is_auth] = false;
		}
		// 인증코드
		$datas['data'][static::u_authcode] = _generatePassword(8);
		// 인증코드가 생성된 일시
		$datas['data'][static::u_dt_authcode] = _now();

		// 차단 여부
		$datas['data'][static::u_is_blocked] = false;
		// 탈퇴 여부
		$datas['data'][static::u_is_deleted] = false;
		// 탈퇴 일시
		$datas['data'][static::u_dt_deleted] = '0000-00-00 00:00:00';

		return $datas;
	}

	/**
	 * before the update operation, this event occurs.
	 * update 작업 전에, 이 이벤트가 발생한다.
	 *
	 * @param array $datas
	 * @return array
	 */
	protected function onBeforeUpdate(array $datas)
	{
		return $datas;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Local Methods (지역 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Returns the hash value of given password.
	 * 지정한 비밀번호의 해시(hash) 값을 반환한다.
	 *
	 * SHA-256 해싱 후, BASE64 인코딩 (보안)
	 * 참조 :: https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
	 *
	 * @param string $u_pass
	 * @return mixed
	 */
	protected function getPasswordHash($u_pass)
	{
		return password_hash( base64_encode( hash('sha256', $u_pass, true) ), PASSWORD_DEFAULT );
	}

}
