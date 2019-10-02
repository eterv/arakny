<?php namespace Arakny\Controllers;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Models\FilesModel;
use Arakny\Support\Format;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Mimes;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Home Controller Class
 * 홈 (홈페이지) 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class File extends BaseController
{

	/** @var FilesModel $files */
	protected $files;
	/** @var \Arakny\Libraries\File $file */
	protected $file;

	/**
	 * @inheritDoc
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->files = Services::files();
		$this->file = Services::file();
	}

	public function index()
	{
        //$data = [];

        //return $this->theme->render('index', $data);
	}

	/**
	 * 파일 삭제 (File Delete)
	 *
	 * @return mixed
	 */
	public function delete()
	{
		$this->ajaxPOST();

		// 유효성 검사
		$result = $this->validate([
			'id' => 'required|is_natural_no_zero',
			'url' => 'required',
		]);
		if (! $result) return $this->fail(_g(Consts::E_INVALID_REQUEST));

		$id = inputPost('id');
		$url = inputPost('url');

		// url 이 path 부분만 있을 수 있으므로, 검사한다.
		if (_startsWith($url, '/')) {
			$url = baseUrl($url);
			if (! Format::isURL($url)) {
				return $this->fail(_g(Consts::E_INVALID_REQUEST));
			}
		}

		// 파일 삭제 시도 (DB 및 저장장치에서 삭제)
		$result = $this->file->deleteFile($id, $url);
		if (! $result) return $this->fail(_g(Consts::E_FILE_DELETE_FAILURE));

		$data = [];

		return $this->succeed($data);
	}

	/**
	 * 파일 목록 가져오기
	 *
	 * @param string $pageType
	 * @return mixed
	 */
	public function list($pageType = Consts::PAGETYPE_FILEEXPLORER)
	{
		$this->ajaxPOST();

		/** 추후에 다시 생각해야 하지만...
		 *		파일탐색기 외에는 list 할 이유가 없을 것 같아보임.
		 */

		if ($pageType === Consts::PAGETYPE_BOARD) {
			//$rows = $files->getFilesByBoards();
		} else if ($pageType === Consts::PAGETYPE_DOC) {
			//$rows = $files->getFilesByDocs();
		} else if ($pageType === Consts::PAGETYPE_FILEEXPLORER) {
			$rows = $this->files->getFilesByFileExplorer();
		} else {
			return $this->fail(_g(Consts::E_FILE_UPLOAD_FAILURE));
		}

		// 바로 DB 데이터를 내보내면 보안에 좋지 않으므로, 가공하여 출력
		$list = [];
		foreach ($rows as $row) {
			$list[] = [
				'id' => $row[FilesModel::f_id],
				'pageid' => $row[FilesModel::f_page],
				'url' => uploadsUrlOnlyPath($row[FilesModel::f_path]),
				'urlThumb' => uploadsUrlOnlyPath($this->file->getThumbUrl($row[FilesModel::f_path])),
				'filename' => basename($row[FilesModel::f_path]),
				'fileext' => pathinfo($row[FilesModel::f_path], PATHINFO_EXTENSION),
				'origname' => $row[FilesModel::f_origname],
				'size' => $row[FilesModel::f_size],
				'w' => $row[FilesModel::f_width],
				'h' => $row[FilesModel::f_height],
				'type' => $row[FilesModel::f_type],
				'dt_uploaded' => $row[FilesModel::f_dt_uploaded],
			];
		}

		$data = [
			'list' => $list,
		];

		return $this->succeed($data);
	}

	/**
	 * 파일 업로드
	 *
	 * @param string $pageType
	 * @return mixed
	 */
	public function upload($pageType = Consts::PAGETYPE_FILEEXPLORER)
    {
    	$this->ajaxPOST();

		$fileField = 'file';
		$file = $this->request->getFile($fileField);
		if (! $file->isValid()) {
			// 실패 - 파일 크기 제한 초과
			if ($file->getError() === UPLOAD_ERR_INI_SIZE) {
				return $this->fail(_g(Consts::E_FILE_UPLOAD_EXCEED_SIZE));
			}
		}

		// Page ID 가져오기
		$pageId = 0;
		if ($pageType === Consts::PAGETYPE_BOARD || $pageType === Consts::PAGETYPE_DOC) {
			$result = $this->validate([
				'pageid' => 'is_natural',
			]);
			if (!$result) {
				return $this->fail(_g(Consts::E_FILE_UPLOAD_FAILURE));
			}

			$pageId = inputPost('pageid');
		}

		// 파일 확장자, MIME
		$mime = $file->getMimeType();
		$ext = Mimes::guessExtensionFromType($mime);				// 내부 컨텐츠 MIME 을 통한 결과적 확장자
		$ext2 = pathinfo($file->getName(), PATHINFO_EXTENSION);		// 실제 파일 확장자

		// 보안 처리 (php, html 계열 파일 제외)
		$ext_blacklist = [ 'asp', 'htm', 'html', 'inc', 'js', 'jsp', 'php', 'php3', 'php4', 'php5', 'htaccess', 'sql' ];
		if (in_array($ext, $ext_blacklist) || in_array($ext2, $ext_blacklist)) {
			// 실패 - 부적절한 파일 형식 업로드 시도
			return $this->fail(_g(Consts::E_FILE_UPLOAD_WRONG_TYPE));
		}

		// 파일 업로드 시도
		$row = Services::file()->uploadFile($file, $pageType, $pageId);
		if (! $row) {
			return $this->fail(_g(Consts::E_FILE_UPLOAD_FAILURE));
		}

		// 업로드 데이터 가공
		$item = [
			'id' => $row[FilesModel::f_id],
			'pageid' => $row[FilesModel::f_page],
			'url' => uploadsUrlOnlyPath($row[FilesModel::f_path]),
			'urlThumb' => uploadsUrlOnlyPath($this->file->getThumbUrl($row[FilesModel::f_path])),
			'filename' => basename($row[FilesModel::f_path]),
			'fileext' => pathinfo($row[FilesModel::f_path], PATHINFO_EXTENSION),
			'origname' => $row[FilesModel::f_origname],
			'size' => $row[FilesModel::f_size],
			'w' => $row[FilesModel::f_width],
			'h' => $row[FilesModel::f_height],
			'type' => $row[FilesModel::f_type],
			'dt_uploaded' => $row[FilesModel::f_dt_uploaded],
		];

		//log_message('critical', 'p : ' . $file->getTempName() . ' / ' . $file->getName() );
		//log_message('critical', 'p : ' . (0777 & ~umask()) . ' / ' . 0777 );

		$data = [
			'item' => $item,
		];

		return $this->succeed($data);
    }

	//--------------------------------------------------------------------

}
