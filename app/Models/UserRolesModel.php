<?php namespace Arakny\Models;

use Arakny\BaseModel;
use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;

/**
 * User Roles Management Model Class
 * 사용자 역할 관리 모델 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class UserRolesModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_USERROLES;

    /* Fields - BEGIN */

    const ur_id = 'ur_id';
	const ur_name = 'ur_name';
	const ur_text = 'ur_text';
	const ur_order = 'ur_order';

    /* Fields - END */

    protected $primaryKey = self::ur_id;

    protected $order_next = 0;

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
            `ur_id` int unsigned NOT NULL AUTO_INCREMENT,
            `ur_name` varchar(64) NOT NULL,
            `ur_text` varchar(64) NOT NULL DEFAULT '',
            `ur_order` int unsigned NOT NULL,
            PRIMARY KEY (`ur_id`),
            UNIQUE KEY `ur_name` (`ur_name`),
            KEY `ur_order_id` (`ur_order`, `ur_id`)
        ) " . Consts::SQL_CHARSET_COLLATE
        );
        if (! $ret) {
            return false;
        }

        // 기본 사용자역할 추가 (최고관리자 superadmin, 관리자 admin)
        $this->skipValidation(true);
        $this->add([ 'ur_name' => 'superadmin', 'ur_text' => '::Globals.l_ur_superadmin', 'ur_order' => 1 ]);
        $this->add([ 'ur_name' => 'admin', 'ur_text' => '::Globals.l_ur_admin', 'ur_order' => 2 ]);
        $this->add([ 'ur_name' => 'general', 'ur_text' => '::Globals.l_ur_general', 'ur_order' => 3 ]);
        $this->skipValidation(false);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function __construct(BaseConnection &$db = null, Validation $validation = null)
    {
		// 기본 허용 필드 목록 정의
		$this->defaultAllowedFields = [
			static::ur_name, static::ur_text, static::ur_order
		];

		// 부모 생성자
		parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
		$this->validationAllRules = [
			static::ur_id => [ 'rules' => 'is_natural_no_zero' ],
			static::ur_name => [ 'rules' => 'required|min_length[1]|max_length[64]|regex_match[/^[A-Za-z0-9_]+$/]|is_unique['.$this->table.'.ur_name,ur_id,{ur_id}]' ],
			static::ur_text => [ 'rules' => 'required|min_length[1]|max_length[64]|is_unique['.$this->table.'.ur_text,ur_id,{ur_id}]' ],
			static::ur_order => [ 'rules' => 'required|is_natural_no_zero' ]
		];

        foreach (array_keys($this->validationAllRules) as $key) {
            $this->validationAllRules[$key]['label'] = _g('l_' . $key);
        }

        // 찾기 이벤트 연결
		$this->afterGetResult[] = 'onAfterGetResult';
		$this->afterGetRow[] = 'onAfterGetRow';

        // 변경 성공 이벤트 연결
        $this->afterChanged[] = 'onAfterChanged';

        // 삽입 전 이벤트 연결
		$this->beforeInsert[] = 'onBeforeInsert';

        // 새로고침
        $this->_refresh();
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
		$this->orderBy(static::ur_order, 'ASC');
		return parent::getAll($select, $limit, $offset);
	}

	/**
	 * 관리자를 제외한 일반 사용자 역할 목록을 순서 기준 가나다순 정렬하여 전부 가져온다.
	 *
	 * @return array
	 */
	public function getAllWithoutAdmin()
	{
		$this->where(static::ur_id . ' >', 2);
		return $this->getAll();
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Events (이벤트)
	/* -------------------------------------------------------------------------------- */

	/**
	 * When the getResult method succeeds, this event occurs.
	 * getResult 작업에 성공할 때, 이 이벤트가 발생한다.
	 *
	 * @param array $datas
	 * @return array
	 */
	protected function onAfterGetResult(array $datas)
	{
		foreach ($datas['data'] as & $item) {
			if (isset($item[static::ur_text])) {
				$text = $item[static::ur_text];

				if (_startsWith($text, '::')) {
					$text = _t( _substr($text, 2) );
				}

				$item['text'] = $text;
			}
		}
		unset($item);

		return $datas;
	}

	/**
	 * When the getRow method succeeds, this event occurs.
	 * getRow 작업에 성공할 때, 이 이벤트가 발생한다.
	 *
	 * @param array $datas
	 * @return array
	 */
	protected function onAfterGetRow(array $datas)
	{
		if (isset($datas['data'][static::ur_text])) {
			$text = $datas['data'][static::ur_text];

			if (_startsWith($text, '::')) {
				$text = _t( _substr($text, 2) );
			}

			$datas['data']['text'] = $text;
		}

		return $datas;
	}

	/**
	 * When the insert, update or delete operation succeeds, this event occurs.
	 * insert, update, delete 작업에 성공할 때, 이 이벤트가 발생한다.
	 *
	 * @param array $datas
	 */
	protected function onAfterChanged(array $datas)
	{
		$this->_refresh();
	}

	/**
	 * before the insert operation, this event occurs.
	 * insert 작업 전에, 이 이벤트가 발생한다.
	 *
	 * @param array $datas
	 * @return array
	 */
	protected function onBeforeInsert(array $datas)
	{
		// 순서 값 자동 지정
		$datas['data'][static::ur_order] = $datas['data'][static::ur_order] ?? $this->order_next;
		
		return $datas;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Local Methods (지역 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Refresh datas in this model.
	 * 모델안의 데이터를 새로고침한다.
	 */
	protected function _refresh()
	{
		// 다음 추가시에 자동 지정될 순서값을 결정한다. (현재 존재하는 순서값들 중 최대값 + 1)
		$this->order_next = (int) $this->selectMax(static::ur_order)->get()->getRowArray()[static::ur_order];
		$this->order_next += 1;
	}

}
