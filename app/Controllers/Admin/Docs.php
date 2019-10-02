<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Models\DocsModel as M;
use Arakny\Models\FilesModel;
use Arakny\Support\Format;
use CodeIgniter\Exceptions\PageNotFoundException;
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
class Docs extends BaseController
{
	protected $eventGroupName = 'Admin.Docs';

	/** @var M $model */
	protected $model = null;

    /**
     * @inheritdoc
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

		$this->model = Services::docs();
    }

    /**
     * Description
     * 설명
     */
    public function index()
	{
		// 작업중...

		$data = [
			'list' => $this->model->getAllSummary(),
		];

		return $this->theme->renderAdminPage('docs', $data);
	}

	/**
	 * Write (Add/Edit) (추가/수정 페이지)
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function write(int $id = 0)
	{
		if ($id === 0) {
			$data = [
				'mode' => 'a',
				'id' => $id,
				'tempid' => time() . _randomInt(10000, 99999),
				'data' => null,
				'urlSubmit' => adminUrl("docs/add"),
			];

		} else {
			$result = $this->model->getRowFromId($id);
			if ($result === null) {
				throw new PageNotFoundException();
			}

			$data = [
				'mode' => 'e',
				'id' => $id,
				'tempid' => $id,
				'data' => $result,
				'urlSubmit' => adminUrl("docs/edit/$id"),
			];
		}

		$data['urlList'] = adminUrl('docs');


		// 추가 및 수정을 같이 넣어야 함.

		return $this->theme->renderAdminPage('docs-write', $data);
	}

	/**
	 * Add (추가)
	 *
	 * @return mixed
	 */
	public function add()
	{
		$this->ajaxPOST();

		// 유효성 검사 (문서 DB 삽입에는 무관하지만 처리에 필요한 데이터)
		$result = $this->validate([
			'pageid' => 'required|is_natural_no_zero',
		]);
		if (! $result) return $this->failErrorInvalidRequest();
		$tempid = inputPost('pageid', 0);

		// 데이터, 사용할 필드
		$datas = [];
		$datas['data'] = inputPost();
		$datas['fields'] = null;

		/** 이벤트 -- 추가 전 */
		$datas = $this->triggerFilter('beforeAdd', $datas);
		if (! $datas) return $this->failError();

		// DB - 추가
		$datas['row'] = $this->model->addAndGetRow($datas['data'], $datas['fields']);
		if (! $datas['row']) return $this->failModel();

		// 새로 발급받은 id
		$id = $datas['row'][M::d_id];

		// 에디터 컨텐츠에서 img 태그의 src 를 분석하여 상대경로 목록을 만든다
		$content = $datas['row'][M::d_content];
		$dom = Services::html()->loadDOM($content);
		$imgs = [];
		foreach ($dom->find('img') as $img) {
			$imgs[] = [ 'url' => uploadsRelativeUrl($img->src) ];
		}

		// 임시 id 로 업로드된 이미지를 DB 에서 조회하여,
		$files = Services::files();
		$rows = $files->getRowsWhere([
			FilesModel::f_pagetype => Consts::PAGETYPE_DOC,
			FilesModel::f_page => $tempid,
		]);
		if ($rows) {
			foreach ($rows as $row) {
				// 이미지가 에디터 안에 없다면, 파일 삭제 시도 (DB 및 저장장치에서 삭제)
				$isExistInEditor = false;
				foreach ($imgs as $img) {
					if ($img['url'] === $row[FilesModel::f_path]) $isExistInEditor = true;
				}
				if (! $isExistInEditor) {
					$result = Services::file()->deleteFile($row[FilesModel::f_id], $row[FilesModel::f_path]);
					if (! $result) return $this->fail(_g(Consts::E_FILE_DELETE_FAILURE));
				}
			}
		}

		// 파일에 연결된 임시 id 를 모두 새로 발급받은 id 로 변경
		$files->editWhere([
			FilesModel::f_pagetype => Consts::PAGETYPE_DOC,
			FilesModel::f_page => $tempid,
		], [ FilesModel::f_page => $id ]);

		/** 이벤트 -- 추가 후 */
		$datas = $this->triggerFilter('afterAdd', $datas);
		if (! $datas) return $this->failError();

		$data = [
			'id' => $datas['row'][M::d_id],
			'row' => $datas['row'],
		];
		return $this->succeed($data);
	}

	/**
	 * Edit (수정)
	 *
	 * @return mixed
	 */
	public function edit()
	{
		$this->ajaxPOST();

		// 데이터, 사용할 필드
		$datas = [];
		$datas['data'] = inputPost();
		$datas['fields'] = null;

		// ID 체크
		$id = $datas['data'][M::d_id] ?? 0;
		if ($id === 0) $this->failValidation();

		/** 이벤트 -- 수정 전 */
		$datas = $this->triggerFilter('beforeEdit', $datas);
		if (! $datas) return $this->failError();

		// DB - 수정 & 결과행 가져오기
		if (! $this->model->editById($id, $datas['data'], $datas['fields'])) {
			return $this->failModel();
		}
		$datas['row'] = $this->model->getRowFromId($id);

		// 에디터 컨텐츠에서 img 태그의 src 를 분석하여 상대경로 목록을 만든다
		$content = $datas['row'][M::d_content];
		$dom = Services::html()->loadDOM($content);
		$imgs = [];
		foreach ($dom->find('img') as $img) {
			$imgs[] = [ 'url' => uploadsRelativeUrl($img->src) ];
		}

		// 현재 id 로 업로드된 이미지를 DB 에서 조회하여,
		$files = Services::files();
		$rows = $files->getRowsWhere([
			FilesModel::f_pagetype => Consts::PAGETYPE_DOC,
			FilesModel::f_page => $id,
		]);
		if ($rows) {
			foreach ($rows as $row) {
				// 이미지가 에디터 안에 없다면, 파일 삭제 시도 (DB 및 저장장치에서 삭제)
				$isExistInEditor = false;
				foreach ($imgs as $img) {
					if ($img['url'] === $row[FilesModel::f_path]) $isExistInEditor = true;
				}
				if (! $isExistInEditor) {
					$result = Services::file()->deleteFile($row[FilesModel::f_id], $row[FilesModel::f_path]);
					if (! $result) return $this->fail(_g(Consts::E_FILE_DELETE_FAILURE));
				}
			}
		}

		/** 이벤트 -- 수정 후 */
		$datas = $this->triggerFilter('afterEdit', $datas);
		if (! $datas) return $this->failError();

		$data = [
			'id' => $id,
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
		if (! Format::isNaturalNumber($id, false)) {
			return $this->failErrorInvalidRequest();
		}

		// 데이터, 사용할 필드
		$datas = [];
		$datas['data'] = inputPost();
		$datas['fields'] = [];

		/** 이벤트 -- 삭제 전 */
		$datas = $this->triggerFilter('beforeDelete', $datas);
		if (! $datas) return $this->failError();

		// DB - 삭제
		if (! $this->model->delete($id)) {
			return $this->failModel();
		}

		// 이 페이지에서 업로드된 파일들을 삭제한다
		if (! Services::file()->deleteFilesInPage(Consts::PAGETYPE_DOC, $id)) {
			return $this->fail(_g(Consts::E_FILE_DELETE_FAILURE));
		}

		/** 이벤트 -- 삭제 후 */
		$datas = $this->triggerFilter('afterDelete', $datas);
		if (! $datas) return $this->failError();

		$data = [];
		return $this->succeed($data);
	}

}
