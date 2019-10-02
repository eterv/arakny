<?php namespace Arakny\Models;

use Arakny\BaseModel;
use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;
use Config\Services;

/**
 * General Pages Management Model Class
 * 일반 페이지 관리 모델 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class DocsModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_DOCS;

    /* Fields - BEGIN */

    const d_id = 'd_id';
    const d_name = 'd_name';
    const d_title = 'd_title';
    const d_content_type = 'd_content_type';
    const d_content = 'd_content';
    const d_path = 'd_path';
	const d_use_header_footer = 'd_use_header_footer';
	const d_is_wide = 'd_is_wide';

    const d_auth_read = 'd_auth_read';

    const d_hit = 'd_hit';

    const d_u_id_created = 'd_u_id_created';
    const d_dt_created = 'd_dt_created';
    const d_u_id_updated = 'd_u_id_updated';
    const d_dt_updated = 'd_dt_updated';

    /* Fields - END */

    protected $primaryKey = 'd_id';

    /* -------------------------------------------------------------------------------- */

    /**
     * @inheritdoc
     */
    public function initTable() {
		$table = $this->db->prefixTable($this->table);
		$ret = $this->db->query("
        CREATE TABLE IF NOT EXISTS `$table` (
            `d_id` int unsigned NOT NULL AUTO_INCREMENT,
            `d_name` varchar(64) NOT NULL,
            `d_title` varchar(100) NOT NULL DEFAULT '',
            `d_content_type` tinyint unsigned NOT NULL DEFAULT 0,		/* 0: 컨텐츠, 1: 파일 */
            `d_content` text NOT NULL DEFAULT '',						/* 64KB 이내 */
            `d_path` varchar(150) NOT NULL DEFAULT '',
            `d_use_header_footer` bool NOT NULL DEFAULT FALSE,
            `d_is_wide` bool NOT NULL DEFAULT FALSE,
            
            `d_auth_read` text NOT NULL DEFAULT '',
            
            `d_hit` bigint unsigned NOT NULL,
            
        	`d_dt_created` datetime NOT NULL,
        	`d_u_id_updated` bigint unsigned NOT NULL,
        	`d_dt_updated` datetime NOT NULL,
          
            PRIMARY KEY (`d_id`),
            UNIQUE KEY `d_name` (`d_name`)
        ) " . Consts::SQL_CHARSET_COLLATE
        );
        if (! $ret) {
            return false;
        }

        // 기본적인 문서 추가
        $this->skipValidation(true);
        /*$this->addRole([ 'ur_name' => 'superadmin', 'ur_text' => 't_ur_superadmin', 'ur_order' => 1 ]);*/
		/** todo 나중에 코드 정리해야함... */
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
			static::d_name, static::d_title, static::d_content_type, static::d_content, static::d_path,
			static::d_use_header_footer, static::d_is_wide,
			static::d_auth_read,
			static::d_hit,
			static::d_u_id_created, static::d_dt_created, static::d_u_id_updated, static::d_dt_updated,
		];

		// 부모 생성자
		parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
        $this->validationAllRules = [
            static::d_id => [ 'rules' => 'is_natural_no_zero' ],
			static::d_name => [ 'rules' => 'required|min_length[1]|max_length[64]|regex_match[/^[A-Za-z0-9_-]+$/]|is_unique[' . $this->table . '.d_name,d_id,{d_id}]' ],
			static::d_title => [ 'rules' => 'required|min_length[1]|max_length[100]' ],
			static::d_content_type => [ 'rules' => 'required|in_list[0,1]' ],
			static::d_content => [ 'rules' => 'max_length[65535]' ],
			static::d_path => [ 'rules' => 'max_length[150]' ],
			static::d_use_header_footer => [ 'rules' => 'bool' ],
			static::d_is_wide => [ 'rules' => 'bool' ],

			static::d_auth_read => [ 'rules' => 'regex_match[/^[0-9,]*$/]' ],

			static::d_hit => [ 'rules' => 'regex_match[/^[0-9,]*$/]' ],

			static::d_u_id_created => [ 'rules' => 'required|is_natural_no_zero' ],
			static::d_dt_created => [ 'rules' => 'required|valid_dt_def' ],
			static::d_u_id_updated => [ 'rules' => 'required|is_natural_no_zero' ],
			static::d_dt_updated => [ 'rules' => 'required|valid_dt_def' ],
        ];
        foreach (array_keys($this->validationAllRules) as $key) {
            $this->validationAllRules[$key]['label'] = _g('l_' . $key);
        }

        // 이벤트 연결

		// 삽입 전 이벤트 연결
		$this->beforeInsert[] = 'onBeforeInsert';

        // 수정 전 이벤트 연결
		$this->beforeUpdate[] = 'onBeforeUpdate';

        // 새로고침
        $this->_refresh();
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Get (Select) (조회)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 사용자 역할 목록을 순서 기준 가나다순 정렬하여 전부 가져온다.
	 *
	 * @param array|string $select
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAll($select = '*', int $limit = 0, int $offset = 0)
	{
		$this->orderBy(static::d_name, 'ASC');
		return parent::getAll($select, $limit, $offset);
	}

	/**
	 * 이름 기준 가나다순 정렬하여 모든 열의 요약을 가져온다.
	 *
	 * 요약 데이터 : d_id, d_name, d_title, url_edit, url_delete
	 *
	 * @param array|string $select
	 * @param int $limit
	 * @param int $offset
	 * @return array|null
	 */
	public function getAllSummary($select = '*', int $limit = 0, int $offset = 0)
	{
		if ($select === '*') {
			$select = [ static::d_id, static::d_name, static::d_title ];
		}

		$this->orderBy(static::d_name, 'ASC');
		$result = parent::getAll($select, $limit, $offset);
		if ($result === null) return $result;

		foreach ($result as & $item) {
			$item['url_edit'] = adminUrl('docs/write/' . $item[static::d_id]);
			$item['url_delete'] = adminUrl('docs/delete/' . $item[static::d_id]);
		}
		unset($item);

		return $result;
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
		// HtmlPurifier 로 코드를 정리한다.
		$datas['data'][static::d_content] = Services::html()->purifyHTML($datas['data'][static::d_content]);

		// 조회수, 날짜, 생성한 사람 정보를 기록
		$datas['data'][static::d_hit] = 0;
		$datas['data'][static::d_dt_created] = _now();
		$datas['data'][static::d_u_id_created] = Services::auth()->getCurrentUserId();
		$datas['data'][static::d_dt_updated] = _now();

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
		// HtmlPurifier 로 코드를 정리한다.
		$datas['data'][static::d_content] = Services::html()->purifyHTML($datas['data'][static::d_content]);

		// 수정 날짜를 기록
		$datas['data'][static::d_dt_updated] = _now();

		return $datas;
	}




    /* -------------------------------------------------------------------------------- */

    /**
     * Refresh datas in this model.
     * 모델안의 데이터를 새로고침한다.
     */
    protected function _refresh() {

    }

}
