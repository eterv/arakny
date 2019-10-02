<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use Arakny\Models\FilesModel;
use Claviska\SimpleImage;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Mimes;
use Config\Services;
use Exception;

/**
 * File Management Library Class
 * 파일 관리 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class File
{
	protected $files;


    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {
		$this->files = Services::files();
    }

	/* -------------------------------------------------------------------------------- */
	/* 		??
	/* -------------------------------------------------------------------------------- */

	/**
	 * 파일을 삭제한다.
	 *
	 * @param $id
	 * @param $path
	 * @return bool
	 */
	public function deleteFile($id, $path)
	{
		if ( _startsWith($path, uploadsUrl()) ) {
			$path = substr( $path, strlen(uploadsUrl()) );
		}

		// DB 에서 사용자, 파일ID, 경로가 모두 일치하는 아이템을 찾는다. 없으면 삭제 실패
		$n = $this->files->getFirstValueWhere([ FilesModel::f_id => $id, FilesModel::f_u_id => Services::auth()->getCurrentUserId(), FilesModel::f_path => $path ], 'COUNT(*)');
		if (! $n) {
			return false;
		}

		// 파일 삭제 (썸네일 포함)
		$path = uploadsPath() . $path;
		$dirname = pathinfo($path, PATHINFO_DIRNAME);
		$filename = pathinfo($path, PATHINFO_FILENAME);
		$ext = pathinfo($path, PATHINFO_EXTENSION);

		$postfixes = [ '', '-fhd', '-hd', '-md', '-thumb' ];
		foreach ($postfixes as $postfix) {
			$name = $dirname . DIRECTORY_SEPARATOR . $filename . $postfix . '.' . $ext;
			if (file_exists($name) && is_writable($name)) {
				@unlink($name);
			}
		}

		// DB 삭제
		$result = $this->files->delete($id);
		if (! $result) {
			return false;
		}

		return true;
	}

	/**
	 * 지정한 페이지에서 업로드한 파일들을 모두 삭제한다.
	 *
	 * @param $pagetype
	 * @param $pageid
	 * @return bool
	 */
	public function deleteFilesInPage($pagetype, $pageid)
	{
		// 현재 id 로 업로드된 이미지를 DB 에서 조회하여,
		// 각각의 이미지를 DB 및 저장장치에서 삭제한다.
		$files = Services::files();
		$rows = $files->getRowsWhere([
			FilesModel::f_pagetype => $pagetype,
			FilesModel::f_page => $pageid,
		]);
		if ($rows) {
			foreach ($rows as $row) {
				$result = Services::file()->deleteFile($row[FilesModel::f_id], $row[FilesModel::f_path]);
				if (! $result) return false;
			}
		}
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Upload (업로드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * @param UploadedFile $file
	 * @param string $pagetype
	 * @param int $pageId
	 * @return bool|array
	 */
	public function uploadFile($file, $pagetype = Consts::PAGETYPE_FILEEXPLORER, $pageId = 0)
	{
		$folder = null;

		// 이미지의 경우 너비와 높이값
		$width = 0;	$height = 0;

		/* ------------------------------------------------------------------- */

		// 함수 호출의 근원지에 따라...
		if ($pagetype === Consts::PAGETYPE_BOARD) $folder = 'boards';
		else if ($pagetype === Consts::PAGETYPE_DOC) $folder = 'docs';
		else if ($pagetype === Consts::PAGETYPE_FILEEXPLORER) $folder = 'files';
		else {
			return false;
		}

		if ($file->hasMoved()) {	// 이럴리가 없는데 이런일이 발생하면 - 오류
			return false;
		}

		// 파일 형식별로 처리 다중화
		if ($this->isImage($file)) {			// 이미지 파일
			$fileType = Consts::FILETYPE_IMAGE;

			// 이미지 크기 가져오기
			try {
				list($width, $height) = getimagesize($file->getTempName());
			} catch (Exception $ex) { }

		} else if ($this->isAudio($file)) {		// 오디오 파일
			$fileType = Consts::FILETYPE_AUDIO;

		} else if ($this->isVideo($file)) {		// 비디오 파일
			$fileType = Consts::FILETYPE_VIDEO;

		} else {								// 일반 파일
			$fileType = Consts::FILETYPE_GENERAL;

		}

		//$pageid = _randomInt(10000000, 99999999);
		$move_path = $this->getUploadPath($pagetype);
		$new_name = $file->getRandomName();
		$orig_name = $file->getName();
		$result = false;

		try {
			$result = $file->move($move_path, $new_name, false);
		} catch (Exception $ex) { }

		if (! $result) {			// 파일 이동 실패 - 오류
			log_message('critical', '파일 이동 실패! - 파일명 :: ' . $orig_name);
			return false;
		}

		// 새 경로 가져오기
		$destPath = $file->getTempName();
		$srcFile = $destPath . $file->getName();
		$srcFileRelativePath = $folder . '/' . date('Y') . '/' . date('m') . '/' . $file->getName();

		// DB - files 테이블에 연결
		$result = $this->files->addAndGetRow([
			FilesModel::f_pagetype => $pagetype,
			FilesModel::f_page => $pageId,

			FilesModel::f_path => $srcFileRelativePath,
			FilesModel::f_origname => $orig_name,

			FilesModel::f_size => $file->getSize(),
			FilesModel::f_width => $width,
			FilesModel::f_height => $height,
			FilesModel::f_type => $fileType,
		]);
		if (! $result) {	// DB 추가 실패 - 오류
			log_message('critical', 'DB 추가 실패! - 파일명 :: ' . $srcFile);
			return false;
		}
		$db_result = $result;

		// 이미지 - 썸네일 생성
		if ($fileType === Consts::FILETYPE_IMAGE) {
			$result = $this->generateThumbnails($srcFile, $destPath);
			if (! $result) {	// 썸네일 생성 실패 - 오류
				log_message('critical', '썸네일 생성 실패! - 파일명 :: ' . $srcFile);
				return false;
			}
		}

		/* Secure upload way
			1) 업로드 파일이 저장되는 경로에 실행권한을 제거 합니다.
			2) 업로드 된 파일명을 DB에 저장하고 이름과 매핑되는 난수를 생성하여 파일명을 대체 합니다.
			3) 업로드 된 파일 확장자를 서버에서 실행되지 않은 형태로 변경하거나 확장자를 제거한다.
			4) 업로드 된 파일을 URL요청으로 접근할 수 없는 곳으로 이동합니다.
		 */

		return $db_result;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Thumbnail (썸네일)
	/* -------------------------------------------------------------------------------- */

	public function getThumbUrl($srcUrl)
	{
		$n = mb_strrpos($srcUrl, '.');
		$ext = '';
		if ($n) {
			$ext = mb_substr($srcUrl, $n);
			$srcUrl = mb_substr($srcUrl, 0, $n);
		}
		$srcUrl .= '-thumb' . $ext;
		return $srcUrl;
	}

	/**
	 * 썸네일을 생성한다.
	 *
	 * @param string $srcFile
	 * @param string $destPath
	 * @return bool
	 */
	protected function generateThumbnails($srcFile, $destPath)
	{
		$filename = pathinfo($srcFile, PATHINFO_FILENAME);
		$ext = pathinfo($srcFile, PATHINFO_EXTENSION);

		/*$image = Services::image();
		$image->withFile($srcFile)->resize(1200, 1200, true);
		$image->save($destPath . $filename . '-lg.' . $ext);

		$image->withFile($srcFile)->resize(500, 500, true);
		$image->save($destPath . $filename . '-md.' . $ext);

		$image->withFile($srcFile)->resize(150, 150, true)->fit(150, 150);
		$image->save($destPath . $filename . '-sm.' . $ext, 80);*/

		try {
			$image = new SimpleImage();

			$image->fromFile($srcFile)
				->bestFit(1920, 1920)
				->toFile($destPath . $filename . '-fhd.' . $ext, null, 90);

			$image->fromFile($srcFile)
				->bestFit(1280, 1280)
				->toFile($destPath . $filename . '-hd.' . $ext, null, 90);

			$image->fromFile($srcFile)
				->bestFit(640, 640)
				->toFile($destPath . $filename . '-md.' . $ext, null, 90);

			$image->fromFile($srcFile)
				->thumbnail(200, 200)
				->toFile($destPath . $filename . '-thumb.' . $ext, null, 80);

		} catch (Exception $ex) {
			return false;
		}

		return true;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Utility Methods (유틸리티 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 업로드할 경로를 생성하여 반환한다.
	 *
	 * @param $pagetype
	 * @return string
	 */
	protected function getUploadPath($pagetype)
	{
		// 업로드 폴더 기본 구조
		// uploads - boards / docs / files - Y - m

		$folder = 'files';
		if ($pagetype === Consts::PAGETYPE_BOARD) $folder = 'boards';
		else if ($pagetype === Consts::PAGETYPE_DOC) $folder = 'docs';
		else if ($pagetype === Consts::PAGETYPE_FILEEXPLORER) $folder = 'files';

		$uploadsPath = uploadsPath();
		$finalPath = $uploadsPath . $folder . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
		$paths = [
			$uploadsPath . 'boards',
			$uploadsPath . 'docs',
			$uploadsPath . 'files',
			$uploadsPath . $folder . DIRECTORY_SEPARATOR . date('Y'),
			$finalPath,
		];

		foreach ($paths as $path) {
			// 폴더가 없으면, 폴더 생성
			if (! is_dir($path)) {
				mkdir($path, 0707);
			}

			// index.html 파일 생성
			if (! is_file($path . '/index.html')) {
				$file = fopen($path . '/index.html', 'x+');
				fclose($file);
			}
		}

		return $finalPath . DIRECTORY_SEPARATOR;
	}

	/**
	 * @param \CodeIgniter\Files\File $file
	 * @return bool
	 */
	public function isAudio($file)
	{
		$mime = $file->getMimeType();

		if (mb_strpos($mime, 'audio') !== 0) {
			return false;
		}

		$arr = [ 'aac', 'ac3', 'flac', 'm4a', 'mid', 'mp3', 'mpga', 'ogg', 'rm', 'wav' ];
		$ext = Mimes::guessExtensionFromType($mime);

		if (! in_array($ext, $arr)) {
			return false;
		}

		return true;
	}

	/**
	 * @param \CodeIgniter\Files\File $file
	 * @return bool
	 */
	public function isImage($file)
	{
		$mime = $file->getMimeType();

		if (mb_strpos($mime, 'image') !== 0) {
			return false;
		}

		$arr = [ 'bmp', 'gif', 'jpg', 'jpeg', 'png' ];
		$ext = Mimes::guessExtensionFromType($mime);
		if (! in_array($ext, $arr)) {
			return false;
		}

		return true;
	}

	/**
	 * @param \CodeIgniter\Files\File $file
	 * @return bool
	 */
	public function isVideo($file)
	{
		$mime = $file->getMimeType();

		if (mb_strpos($mime, 'video') !== 0) {
			return false;
		}

		$arr = [ '3gp', 'avi', 'flv', 'mp4', 'mkv', 'mov', 'mpeg', 'mpg', 'qt', 'webm' ];
		$ext = Mimes::guessExtensionFromType($mime);
		if (! in_array($ext, $arr)) {
			return false;
		}

		return true;
	}


}
