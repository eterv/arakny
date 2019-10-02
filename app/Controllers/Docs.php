<?php namespace Arakny\Controllers;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use Arakny\Models\DocsModel as M;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * @todo 퍼미션을 살펴봐야!...
 */

/**
 * Home Controller Class
 * 홈 (홈페이지) 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Docs extends BaseController
{
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
	 * 문서 경로 자동 매핑
	 *
	 * @param $method
	 * @return string
	 */
	public function _remap($method)
	{
		$row = $this->model->getFirstRowWhere([ M::d_name => str_replace('_', '-', $method) ]);
		if ($row) {
			$content_type = $row[M::d_content_type];
			if ($content_type == 1) {	// 외부 파일
				$d_path = $row[M::d_path];
				$path = contentPath('docs/' . $d_path);

				if (! is_file($path)) {
					throw new FileNotFoundException(_g(Consts::E_FILE_NOTFOUND, [ $d_path ]));
				}

				$data = [
					'_path' => $path,
					'_useHeaderFooter' => $row[M::d_use_header_footer],
				];

			} else {					// 에디터 컨텐츠
				$html = $row[M::d_content];

				$data = [
					'_content' => $html,
					'_useHeaderFooter' => $row[M::d_use_header_footer],
				];
			}

			return $this->theme->render('', $data);

		} else {
			throw new PageNotFoundException();
		}
	}

	//--------------------------------------------------------------------

}
