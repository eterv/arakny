<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Models\UserRolesModel as M;
use Arakny\Support\Format;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Admin General Documents Config Controller Class
 * 관리자 일반 문서 설정 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Userroles extends BaseController
{
	protected $eventGroupName = 'Admin.Userroles';

	/** @var M $model */
	protected $model = null;

	/* -------------------------------------------------------------------------------- */

    /**
     * @inheritdoc
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->model = Services::userroles();
    }

    /**
     * Index page
     * 인덱스 페이지
     */
    public function index()
	{
		// 작업중...

		$data = [
			'list' => $this->model->getAll(),
		];
		return $this->theme->renderAdminPage('userroles', $data);
	}

	/* -------------------------------------------------------------------------------- */

	/**
	 * Add (추가)
	 *
	 * @return mixed
	 */
	public function add()
	{
		$this->ajaxPOST();

		// 데이터, 사용할 필드
		$datas = [];
		$datas['data'] = inputPost();
		$datas['fields'] = [ M::ur_name, M::ur_text ];

		/** 이벤트 -- 추가 전 */
		$datas = $this->triggerFilter('beforeAdd', $datas);
		if (! $datas) return $this->failError();

		// DB - 추가
		$datas['row'] = $this->model->addAndGetRow($datas['data'], $datas['fields']);
		if (! $datas['row']) {
			return $this->failModel();
		}

		/** 이벤트 -- 추가 후 */
		$datas = $this->triggerFilter('afterAdd', $datas);
		if (! $datas) return $this->failError();

		$data = [
			'id' => $datas['row'][M::ur_id],
			'row' => $datas['row'],
		];
		return $this->succeed($data);
	}

	/**
	 * Edit (수정)
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function edit($id = null)
	{
		$this->ajaxPOST();

		// id -- 0을 포함하지 않는 자연수만 허용
		// 슈퍼관리자, 관리자는 기본값이므로 수정 불가
		if (! Format::isNaturalNumber($id, false) || $id == 1 || $id == 2) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		// 데이터, 사용할 필드
		$datas = [];
		$datas['data'] = inputPost();
		$datas['fields'] = [ M::ur_name, M::ur_text ];

		/** 이벤트 -- 수정 전 */
		$datas = $this->triggerFilter('beforeEdit', $datas);
		if (! $datas) return $this->failError();

		// DB - 수정 & 결과행 가져오기
		if (! $this->model->editById($id, $datas['data'], $datas['fields'])) {
			return $this->failModel();
		}
		$datas['row'] = $this->model->getRowFromId($id);

		/** 이벤트 -- 수정 후 */
		$datas = $this->triggerFilter('afterEdit', $datas);
		if (! $datas) return $this->failError();

		$data = [
			'text' => $datas['row']['text'],
		];
		return $this->succeed($data);
	}

	/**
	 * Delete (삭제)
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function delete(int $id = null)
	{
		$this->ajaxPOST();

		// id -- 0을 포함하지 않는 자연수만 허용
		// 슈퍼관리자, 관리자, 기본 회원은 기본값이므로 삭제 불가
		if (! Format::isNaturalNumber($id, false) || $id <= 3) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		// 데이터, 사용할 필드
		$datas = [];
		$datas['data'] = inputPost();
		$datas['fields'] = [];

		/** 이벤트 -- 삭제 전 */
		$datas = $this->triggerFilter('beforeDelete', $datas);
		if (! $datas) return $this->failError();
		

		// @todo 삭제 전에 모든 사용자를 다른 역할로 옮기기.
		// UsersModel 사용자 부분을 작업해야함
		// 삭제는 삭제 전에 모든 사용자를 다른 역할 (아마도 가입할때 적용되는 기본역할)으로 옮겨놓아야 한다!!! 매우 중요.
		

		// DB - 삭제
		if (! $this->model->delete($id)) {
			return $this->failModel();
		}

		/** 이벤트 -- 삭제 후 */
		$datas = $this->triggerFilter('afterDelete', $datas);
		if (! $datas) return $this->failError();

		$data = [];
		return $this->succeed($data);
	}


	public function get_all()
	{
		$this->ajaxGET();

		$data = [
			'list' => $this->model->getAll(),
		];
		return $this->succeed($data);
	}

	/**
	 * Get a row from id
	 * ID 값으로부터 한 행을 가져온다.
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function fromId($id = null)
	{
		$this->ajaxGET();

		// id -- 0을 포함하지 않는 자연수만 허용
		if (! Format::isNaturalNumber($id, false)) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		// id 에 해당하는 하나의 전체 행을 가져온다.
		$row = $this->model->getRowFromId($id);
		if ($row === null) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		$data = [
			'row' => $row,
		];
		return $this->succeed($data);
	}

	/**
	 * Save (저장 / 확인)
	 *
	 * @return mixed
	 */
	public function save()
	{
		$this->ajaxPOST();

		// 데이터, 유효성 검증 규칙
		$datas = [];
		$datas['data'] = inputPost();
		$datas['rules'] = [
			'list' => 'required|valid_json',
		];

		/** 이벤트 -- 저장 전 */
		$datas = $this->triggerFilter('beforeSave', $datas);
		if (! $datas) {
			return $this->failError();
		}

		// 유효성 검사
		if (! $this->validate($datas['rules'])) {
			return $this->failValidation();
		}

		// 사용자 역할 순서 저장
		$list = json_decode($datas['data']['list']);
		foreach ($list as $i => $item) {
			$result = $this->model->editById($item, [ M::ur_order => ($i + 1) ]);
			if (! $result) {
				return $this->failModel();
			}
		}

		/** 이벤트 -- 저장 후 */
		$result = $this->triggerFilter('afterSave', $datas);
		if (! $result) {
			return $this->failError();
		}

		$data = [];
		return $this->succeed($data);
	}

}