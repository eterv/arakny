<?php namespace Arakny;

/* @todo Meta 테이블 연동 연구...
 *
 * - Meta 테이블 연구
 * - Meta 테이블 데이터를 취급할 수 있는 Trait 또는 Interface 를 구축하여 필요한 Model 에 적용해야 함.
 *
 */

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;
use CodeIgniter\Validation\Validation;
use Config\Services;
use Exception;
use InvalidArgumentException;

/**
 * Arakny Base Model Class
 * 아라크니 기본 모델 클래스
 *
 * CI 모델을 확장함으로, 모든 하위 모델들의 기본 작업을 수행합니다.
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
abstract class BaseModel extends Model
{
	/**
	 * 현재 모델의 insert, update 작업시에 허용하는 기본 필드 배열
	 *
	 * @var array $defaultAllowedFields
	 */
	protected $defaultAllowedFields = [];

	/**
	 * 마지막 발생 오류 - 데이터 배열, 메시지
	 * 데이터 배열 키 중 where -- database, validation
	 */
	protected $lastErrorData = [];
	protected $lastErrorMessage = '';

    /**
     * 현재 모델의 모든 필드의 유효성 규칙을 여기에 저장한다.
     *
     * @var array $validationAllRules
     */
    protected $validationAllRules = [];

    // 이벤트

	protected $afterGetResult = [];
	protected $afterGetRow = [];
	protected $afterChanged = [];


    protected $settings = null;

    /* -------------------------------------------------------------------------------- */

    /**
     * Model constructor.
     *
     * @param BaseConnection $db
     * @param Validation $validation
     */
    public function __construct(BaseConnection &$db = null, Validation $validation = null)
    {
        parent::__construct($db, $validation);

        if ( ! $this->db->tableExists($this->table) && ! $this->initTable() ) {
            throw new DatabaseException(_g('e_db_init_failure'));
        }

        $this->settings = Services::settings($db);

        // 이벤트 등록
		$this->afterInsert[] = 'onAfterInsertUpdateDelete';
		$this->afterUpdate[] = 'onAfterInsertUpdateDelete';
		$this->afterDelete[] = 'onAfterInsertUpdateDelete';
    }

    /**
     * Initialize the table that be connected with this model.
     * 현재 모델과 연관된 테이블을 초기화 한다.
     *
     * @return bool
     */
    abstract public function initTable();

	/* -------------------------------------------------------------------------------- */
	/* 		Add (Insert) (추가)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Add an item.
	 * 하나의 아이템을 추가한다.
	 *
	 * @param mixed $data
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return bool
	 */
	public function add($data, $allowedFields = null, $validationFields = null)
	{
		return $this->_add($data, 'bool', $allowedFields, $validationFields);
	}

	/**
	 * Add an item, then get the id of this item.
	 * 하나의 아이템을 추가하고, 그 추가된 아이템의 id 를 가져온다.
	 *
	 * @param mixed $data
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return array|bool|int
	 */
	public function addAndGetId($data, $allowedFields = null, $validationFields = null)
	{
		return $this->_add($data, 'id', $allowedFields, $validationFields);
	}

	/**
	 * Add an item, then get row data of this item as array type.
	 * 하나의 아이템을 추가하고, 그 추가된 아이템의 전체 행 데이터를 배열 형식으로 가져온다.
	 *
	 * @param mixed $data
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return array
	 */
	public function addAndGetRow($data, $allowedFields = null, $validationFields = null)
	{
		return $this->_add($data, 'row', $allowedFields, $validationFields);
	}

	/**
	 * Add an item.
	 * 하나의 아이템을 삽입한다.
	 *
	 * @param mixed $data
	 * @param string $returnType
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return array|bool|int
	 * @throws
	 */
	protected function _add($data, $returnType = 'id', $allowedFields = null, $validationFields = null)
	{
		$this->allowedFields = $allowedFields ?? $this->defaultAllowedFields;
		$this->setValidationSomeRules($validationFields ?? $this->allowedFields);

		switch (strtolower($returnType)) {
			case 'id':		// 새로 삽입된 ID
				$result = $this->insert($data, false);
				return ($result === false) ? false : $this->getLastInsertID();

			case 'row':		// 새로 삽입된 행 데이터
				$result = $this->insert($data, false);
				return ($result === false) ? null : $this->getRowFromId($this->getLastInsertID());

			default:		// Boolean 데이터 (True / False)
				$result = $this->insert($data, false);
				return ($result === false || $result === 0) ? false : true;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function insert($data = null, bool $returnID = true)
	{
		// 안전하게 try 문으로 처리한 다음, 오류 데이터 가공 생성
		$result = false;
		try {
			$result = parent::insert($data, $returnID);
		} catch (Exception $ex) { }

		if ($result === false) {
			$this->_CRUD_Fail();
		}
		return $result;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Edit (Update) (수정)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Edit an/some item(s).
	 * 하나 또는 얼마의 아이템을 수정한다.
	 *
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return bool
	 * @throws
	 */
	public function edit($allowedFields = null, $validationFields = null)
	{
		$this->allowedFields = $allowedFields ?? $this->defaultAllowedFields;
		$this->setValidationSomeRules($validationFields ?? $this->allowedFields);

		return $this->update();
	}

	/**
	 * Edit an item that matches id.
	 * id 가 일치한 하나의 아이템을 수정한다.
	 *
	 * @param int $id
	 * @param array $data
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return bool
	 * @throws
	 */
	public function editById($id, $data, $allowedFields = null, $validationFields = null)
	{
		$this->where($this->primaryKey, $id)->set($data);
		return $this->edit($allowedFields, $validationFields);
	}

	/**
	 * Edit items that match where clause.
	 * where 절에 일치한 아이템들을 수정한다.
	 *
	 * @param mixed $where
	 * @param array $data
	 * @param array $allowedFields
	 * @param array $validationFields
	 * @return bool
	 */
	public function editWhere($where, $data, $allowedFields = null, $validationFields = null)
	{
		$this->where($where)->set($data);
		return $this->edit($allowedFields, $validationFields);
	}

	/**
	 * @inheritdoc
	 */
	public function update($id = null, $data = null): bool
	{
		// 안전하게 try 문으로 처리한 다음, 오류 데이터 가공 생성
		$result = false;
		try {
			$result = parent::update($id, $data);
		} catch (Exception $ex) { }

		if ($result === false) {
			$this->_CRUD_Fail();
		}
		return $result;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Delete (삭제)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Delete items that match where clause.
	 * where 절에 일치한 아이템들을 삭제한다.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @param bool $purge
	 * @return mixed
	 */
	public function deleteWhere($key, $value = null, $purge = false)
	{
		$this->where($key, $value);
		return $this->delete(null, $purge);
	}

	/**
	 * @inheritdoc
	 */
	public function delete($id = null, bool $purge = false)
	{
		// 안전하게 try 문으로 처리한 다음, 오류 데이터 가공 생성
		$result = false;
		try {
			$result = parent::delete($id, $purge);
		} catch (Exception $ex) { }

		if ($result === false) {
			$this->_CRUD_Fail();
		}
		return $result;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Get (Select) (조회)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Retrieve all datas of this table. But, you can select the data to get.
	 * 이 테이블의 모든 데이터를 가져온다. 단, 가져올 데이터(열)를 선택할 수 있다.
	 *
	 * 가져오기에 성공하면 array 배열을, 실패하면 null 을 반환한다.
	 *
	 * @param array|string $select
	 * @param int $limit
	 * @param int $offset
	 * @return array|null
	 */
	public function getAll($select = '*', int $limit = 0, int $offset = 0) {
		return $this->select($select)->findAll($limit, $offset);
	}

	/**
	 * Returns id value from a specified field(column) value.
	 * 지정한 필드(열)의 값으로부터 id 값을 가져온다.
	 *
	 * 가져오기에 성공하면 id 값을, 실패하면 0 을 반환한다.
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return int
	 */
	public function getIdFromField($field, $value) {
		$id = $this->getValueWhere($this->primaryKey, [ $field => $value ]);
		return $id ?? 0;
	}

	/**
	 * Retrieve a specified row array from id (primary key).
	 * 지정한 id (주 키) 값에 해당하는 하나의 행을 가져온다.
	 *
	 * 가져오기에 성공하면 array 배열을, 실패하면 null 을 반환한다.
	 *
	 * @param mixed $id
	 * @param array|string $select
	 * @return array|null
	 */
	public function getRowFromId($id, $select = '*') {
		return $this->select($select)->find($id);
	}

	/**
	 * Retrieve the first row that match given where clause.
	 * 지정한 where 절에 일치하는 첫번째 행을 가져온다.
	 *
	 * 가져오기에 성공하면 array 배열을, 실패하면 null 을 반환한다.
	 *
	 * @param mixed $where
	 * @param array|string $select
	 * @return array|null
	 */
	public function getFirstRowWhere($where, $select = '*') {
		return $this->select($select)->where($where)->first();
	}

	/**
	 * Retrieve the first value of first row that match given where clause.
	 * 지정한 where 절에 일치하는 첫번째 항의 첫번째 값을 가져온다.
	 *
	 * 가져오기에 성공하면 값을, 실패하면 null 을 반환한다.
	 *
	 * @param mixed $where
	 * @param array|string $select
	 * @return mixed
	 */
	public function getFirstValueWhere($where, $select = '*') {
		$row = $this->getFirstRowWhere($where, $select);
		if ($row === null) return null;

		foreach ($row as $item) {
			return $item;
		}
		return null;
	}

	/**
	 * Retrieve rows that match given where clause.
	 * 지정한 where 절에 일치하는 행 배열을 가져온다.
	 *
	 * 가져오기에 성공하면 array 배열을, 실패하면 null 을 반환한다.
	 *
	 * @param mixed $where
	 * @param array|string $select
	 * @param int $limit
	 * @param int $offset
	 * @return array|null
	 */
	public function getRowsWhere($where, $select = '*', int $limit = 0, int $offset = 0) {
		return $this->select($select)->where($where)->findAll($limit, $offset);
	}

	/**
	 * Retrieve fieldname value from a specified where clause.
	 * 지정한 where 절을 통해, fieldname 의 값을 가져온다.
	 *
	 * 주로, 조사할 where 부분의 값은 unique 한 값을 지정하게 된다.
	 * 성공하면 해당 값을, 실패하면 null 을 반환한다.
	 *
	 * @param string $fieldname
	 * @param mixed $where
	 * @return mixed
	 */
	public function getValueWhere($fieldname, $where) {
		$row = $this->getFirstRowWhere($where, $fieldname);
		return ($row === null) ? null : $row[$fieldname];
	}

	/**
	 * @inheritdoc
	 *
	 * 참고) 이 메소드는 findAll, first 에 비해 덜 명시적인지라, 가능한 이 메소드보다는
	 * 		 더욱 명시적인 findAll, first 메소드를 사용하도록 한다.
	 */
	public function find($id = null)
	{
		// 안전하게 try 문으로 처리한 다음, 오류 데이터 가공 생성
		$result = false;
		try {
			$result = parent::find($id);
		} catch (Exception $ex) { }

		if ($result === false) {
			$this->_CRUD_Fail();
		} else {
			if ($id === null) {
				$datas = $this->trigger('afterGetResult', [ 'data' => $result ]);
			} else {
				$datas = $this->trigger('afterGetRow', [ 'data' => $result ]);
			}
			$result = $datas['data'];
		}
		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function findAll(int $limit = 0, int $offset = 0)
	{
		// 안전하게 try 문으로 처리한 다음, 오류 데이터 가공 생성
		$result = false;
		try {
			$result = parent::findAll($limit, $offset);
		} catch (Exception $ex) { }

		if ($result === false) {
			$this->_CRUD_Fail();
		} else {
			$datas = $this->trigger('afterGetResult', [ 'data' => $result ]);
			$result = $datas['data'];
		}
		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function first()
	{
		// 안전하게 try 문으로 처리한 다음, 오류 데이터 가공 생성
		$result = false;
		try {
			$result = parent::first();
		} catch (Exception $ex) { }

		if ($result === false) {
			$this->_CRUD_Fail();
		} else {
			$datas = $this->trigger('afterGetRow', [ 'data' => $result ]);
			$result = $datas['data'];
		}
		return $result;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Check Methods (검사/확인 메소드)
	/* -------------------------------------------------------------------------------- */

    /**
     * Returns whether the specified field value already exists in the table.
     * 테이블에서 지정한 필드의 값이 이미 존재하는지 여부를 반환한다.
     *
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public function isFieldValueExists($field, $value) {
        if ( ! is_string($field) || empty($field) || is_null($value) ) throw new InvalidArgumentException();

        $query = $this->builder()->select($field)->getWhere([ $field => $value ]);
        return !empty($query->getResultArray());
    }

    /**
     * Returns whether the specified id value already exists in the table.
     * 테이블에서 지정한 id 값이 이미 존재하는지 여부를 반환한다.
     *
     * @param mixed $id
     * @return bool
     */
    public function isIdExists($id) {
        if ( ! is_numeric($id) ) throw new InvalidArgumentException();

        return $this->isFieldValueExists($this->primaryKey, $id);
    }


	/* -------------------------------------------------------------------------------- */
	/* 		Errors (오류)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Return the data of last error(s) that occurred.
	 * 발생한 마지막 오류의 데이터를 반환한다.
	 *
	 * @return array
	 */
	public function lastErrorData()
	{
		return $this->lastErrorData;
	}

	/**
	 * Return the message string of last error(s) that occurred.
	 * 발생한 마지막 오류의 문자열 메시지를 반환한다.
	 *
	 * @return string
	 */
	public function lastErrorMessage()
	{
		return $this->lastErrorMessage;
	}

	/**
	 * Create, Read, Update, Delete 4가지 DB 작업에서 실패할 경우 실행되는 작업
	 */
	protected function _CRUD_Fail() {
		$errors = $this->errors();
		$where = '';
		$message = '';

		if (is_array($errors)) {
			foreach ($errors as $field => $error) {
				$where = 'validation';
				$message = $error;
				break;
			}
		} else if (is_string($errors)) {
			$where = 'database';
			$message = $errors;
		} else {
			$where = 'database';
			$errors = $this->db->error();
			$message = $errors['message'];
		}

		$this->lastErrorData = [
			'where' => $where,
			'errors' => $errors,
		];
		$this->lastErrorMessage = $message;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Events (이벤트)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 모델 외부에서 모델에 이벤트에 대한 사용자 정의 핸들러를 등록합니다.
	 *
	 * @param string $event
	 * @param callable $cb
	 */
	public function addEventHandler(string $event, callable $cb)
	{
		if (is_array($this->{$event})) {
			$this->{$event}[] = $cb;
		}
	}

	/** @noinspection PhpUnusedPrivateMethodInspection */
	/**
	 * When the insert, update or delete operation succeeds, this event occurs.
	 * insert, update, delete 작업에 성공할 때, 이 이벤트가 발생한다.
	 *
	 * @param array $param
	 */
	protected function onAfterInsertUpdateDelete(array $param)
	{
		if ($param['result'] !== false) {
			$this->trigger('afterChanged', $param);
		}
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Util Methods
	/* -------------------------------------------------------------------------------- */

	/**
	 * insert 작업 후에, 삽입된 행의 id 값을 가져온다.
	 *
	 * MySQL (MariaDB) 에서 적용됨.
	 */
	public function getLastInsertID()
	{
		return $this->db->query('SELECT LAST_INSERT_ID()')->getRowArray()['LAST_INSERT_ID()'];
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Validation (유효성 검증)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 현재 모델의 insert, update 작업시에 허용하는 기본 필드 배열을 가져온다.
	 *
	 * @return array
	 */
	public function getDefaultAllowedFields()
	{
		return $this->defaultAllowedFields;
	}

	/**
	 * insert, update 작업시에 허용하는 필드 배열을 만든다.
	 * 기존 필드 배열이 null 이라면, 기본 필드 배열이 사용된다.
	 *
	 * @param array $newFields
	 * @param array $existFields
	 * @return array
	 */
	public function makeAllowedFields(array $newFields = null, array $existFields = null)
	{
		if ( empty($existFields) ) $existFields = $this->defaultAllowedFields;
		foreach ($newFields as $field) {
			// 필드가 중복으로 추가되지 않도록 처리
			if (! in_array($field, $existFields, true)) array_push($existFields, $field);
		}
		return $existFields;
	}

	/**
	 * 지정한 필드의 유효성 규칙을 반환한다.
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function getValidationRule(string $field)
	{
		if ( array_key_exists($field, $this->validationAllRules)) {
			return $this->validationAllRules[$field];
		}
		return null;
	}

	/**
	 * insert or update 작업 전에, 유효성을 검사할 필드를 선정하여 유효성 규칙 배열을 반환한다.
	 *
	 * @param array $fields
	 * @return array
	 */
	public function getValidationSomeRules(array $fields = null)
	{
		if ( empty($fields) ) $fields = $this->allowedFields;
		$rules = array_intersect_key( $this->validationAllRules, array_flip($fields) );
		return $rules;
	}

	/**
	 * insert or update 작업 전에, 유효성을 검사할 필드를 선정한다.
	 *
	 * @param array $fields
	 */
	public function setValidationSomeRules(array $fields = null)
	{
		$this->validationRules = $this->getValidationSomeRules($fields);
	}

}
