<?php namespace Arakny\Models;

use Arakny\BaseModel;
use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;
use Config\Services;

/**
 * Files Management Model Class
 * 파일 관리 모델 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class FilesModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_FILES;

    /* Fields - BEGIN */

    const f_id = 'f_id';
    const f_u_id = 'f_u_id';
    const f_pagetype = 'f_pagetype';
    const f_page = 'f_page';

    const f_path = 'f_path';
    const f_origname = 'f_origname';

    const f_size = 'f_size';
    const f_width = 'f_width';
    const f_height = 'f_height';
    const f_type = 'f_type';

    const f_dt_uploaded = 'f_dt_uploaded';

    /* Fields - END */

    protected $primaryKey = 'f_id';

    protected $useSoftDeletes = false;

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
            `f_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `f_u_id` bigint unsigned NOT NULL DEFAULT 0,
            `f_pagetype` varchar(2) NOT NULL,			/* b 게시판, d 일반문서, f 파일탐색기, t 임시 */
            `f_page` bigint unsigned NOT NULL DEFAULT 0,
            
            `f_path` varchar(255) NOT NULL DEFAULT '',
            `f_origname` varchar(100) NOT NULL DEFAULT '',
            
            `f_size` int unsigned NOT NULL DEFAULT 0,
            `f_width` mediumint unsigned NOT NULL DEFAULT 0,
            `f_height` mediumint unsigned NOT NULL DEFAULT 0,
            `f_type` varchar(2) NOT NULL,				/* g 일반, i 이미지 */
            
            `f_dt_uploaded` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`f_id`),
            KEY `f_pagetype_page` (`f_pagetype`, `f_page`)
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
			static::f_u_id, static::f_pagetype, static::f_page,
			static::f_path, static::f_origname,
			static::f_size, static::f_width, static::f_height, static::f_type,
			static::f_dt_uploaded
		];

		// 부모 생성자
		parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
        $this->validationAllRules = [
            static::f_id => [ 'rules' => 'is_natural_no_zero' ],
            static::f_u_id => [ 'rules' => 'is_natural' ],
			static::f_pagetype => [ 'rules' => 'required|in_list[b,d,f,t]' ],
			static::f_page => [ 'rules' => 'required|is_natural' ],

			static::f_path => [ 'rules' => 'required|min_length[5]|max_length[255]' ],
			static::f_origname => [ 'rules' => 'required|max_length[100]' ],

			static::f_size => [ 'rules' => 'required|is_natural' ],
			static::f_width => [ 'rules' => 'is_natural' ],
			static::f_height => [ 'rules' => 'is_natural' ],
			static::f_type => [ 'rules' => 'required|in_list[a,g,i,v]' ],

			static::f_dt_uploaded => [ 'rules' => 'required|valid_dt[' . Consts::DB_DATETIME_FORMAT . ']' ],
        ];
        foreach (array_keys($this->validationAllRules) as $key) {
            $this->validationAllRules[$key]['label'] = _g('l_' . $key);
        }

		// 삽입 전 이벤트 연결
		$this->beforeInsert[] = 'onBeforeInsert';
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Get (Select) (조회)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 현재 사용자가 파일 탐색기를 통해 업로드한 파일들의 목록을 가져옵니다.
	 *
	 * @param string $orderBy
	 * @return array
	 */
	public function getFilesByFileExplorer($orderBy = '')
	{
		if (empty($orderBy)) {
			$orderBy = static::f_dt_uploaded . ' DESC';
		}

		$this->orderBy($orderBy);
		return $this->getRowsWhere([ static::f_u_id => Services::auth()->getCurrentUserId(), static::f_pagetype => Consts::PAGETYPE_FILEEXPLORER ]);
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
		// 사용자 등록
		$datas['data'][static::f_u_id] = Services::auth()->getCurrentUserId();

		// 현재 시간 등록
		$datas['data'][static::f_dt_uploaded] = date(Consts::DB_DATETIME_FORMAT);

		return $datas;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Events (이벤트)
	/* -------------------------------------------------------------------------------- */

    /**
     * Refresh datas in this model.
     * 모델안의 데이터를 새로고침한다.
     */
    protected function _refresh() {

    }

}
