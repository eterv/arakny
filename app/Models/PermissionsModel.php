<?php namespace Arakny\Models;

use Arakny\BaseModel;
use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;

// 작업 사항 ::
//

/**
 * Permission Management Model Class
 * 퍼미션(허가) 관리 모델 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class PermissionsModel extends BaseModel
{
    protected $table = Consts::DB_TABLE_PERMISSIONS;

    /* Fields - BEGIN */

    const p_id = 'p_id';
    const p_pagetype = 'p_pagetype';
    const p_page = 'p_page';
    const p_ur_id = 'p_ur_id';
    const p_u_id = 'p_u_id';
    const p_mode = 'p_mode';

    /* Fields - END */

    protected $primaryKey = 'p_id';
    protected $returnType = 'object';

    protected $useSoftDeletes = false;

    /* -------------------------------------------------------------------------------- */

    /**
     * @inheritdoc
     */
    public function initTable() {
		$table = $this->db->prefixTable($this->table);
		$ret = $this->db->query("
        CREATE TABLE IF NOT EXISTS `$table` (
            `p_id` smallint unsigned NOT NULL AUTO_INCREMENT,
            `p_pagetype` varchar(2) NOT NULL,			/* a 관리자, b 게시판, d 일반페이지, p 플러그인? */
            `p_page` varchar(100) NOT NULL,
            `p_ur_id` smallint unsigned NOT NULL DEFAULT 0,
            `p_mode` tinyint unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (`p_id`),
            KEY `p_pagetype_page` (`p_pagetype`, `p_page`)
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
        parent::__construct($db, $validation);

        // 필드값 유효성검증 규칙 정의
        $this->validationAllRules = [
            static::p_id => [ 'rules' => 'is_natural_no_zero' ],
			static::p_pagetype => [ 'rules' => 'required|in_list[a,b,d,p]' ],
			static::p_page => [ 'rules' => 'required|regex_match[/^[A-Za-z0-9\\\/_-]+$/]' ],
			static::p_ur_id => [ 'rules' => 'is_natural' ],
			static::p_u_id => [ 'rules' => 'is_natural' ],
			static::p_mode => [ 'rules' => 'required|in_list[0,1,2]' ],
        ];
        foreach (array_keys($this->validationAllRules) as $key) {
            $this->validationAllRules[$key]['label'] = _g('l_' . $key);
        }

        // 새로고침
        $this->_refresh();
	}

    /* -------------------------------------------------------------------------------- */

    /**
     * Adds a permission item.
     * 퍼미션(허가) 아이템을 추가한다.
     *
     * @param array $data
     * @param bool $returnID
     * @return int|bool
     */
	public function addItem(array $data = null, bool $returnID = false) {
        $this->allowedFields = [ static::p_pagetype, static::p_page, static::p_ur_id, static::p_u_id, static::p_mode ];
        $this->setValidationSomeRules($this->allowedFields);

        $result = $this->insert($data, $returnID);
        if ($result) {
            $this->_refresh();
        }
        return $result;
    }

    /**
     * Modifies a specified item.
     * 지정한 하나의 아이템을 수정한다.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function modifyItem($id, $data) {
		$this->allowedFields = [ static::p_pagetype, static::p_page, static::p_ur_id, static::p_u_id, static::p_mode ];
        $this->setValidationSomeRules($this->allowedFields);

        $data[$this->primaryKey] = $data[$this->primaryKey] ?? $id;

        $result = $this->update( $id, $data );
        if ($result) {
            $this->_refresh();
        }
        return $result;
    }

    /**
     * Removes a specified item.
     * 지정한 아이템을 제거한다.
     *
     * @param int|string $id
     * @return bool
     */
    public function removeItem($id) {
        if (!$this->isIdExists($id)) return false;

        $result = $this->delete($id);
        if ($result) {
            $this->_refresh();
        }
        return $result ? true : false;
    }

    /**
     * Removes the specified user-roles.
     * 지정한 사용자 역할들을 제거한다.
     *
     * @param array $arr_id
     * @return bool
     */
    public function removeRoles($arr_id) {
        $result = $this->delete($arr_id);
        if ($result) {
            $this->_refresh();
        }
        return $result ? true : false;
    }

    /* -------------------------------------------------------------------------------- */



    /* -------------------------------------------------------------------------------- */

    /**
     * Refresh datas in this model.
     * 모델안의 데이터를 새로고침한다.
     */
    protected function _refresh() {

    }

}
