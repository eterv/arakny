<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
use Arakny\Constants\Consts;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Admin Menu Config Controller Class
 * 관리자 메뉴설정 컨트롤러 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class L10n extends BaseController
{
    /**
     * @inheritdoc
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Description
     * 설명
	 *
	 * @param string $id
	 * @param string $context
	 * @return mixed
     */
    public function translate($id = '', $context = 'html')
	{
		if ($id === null || $id === '') {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}
		if (! in_array($context, ['html', 'js', 'css', 'url', 'attr'])) {
			return $this->fail(_g(Consts::E_INVALID_REQUEST));
		}

		$data = [];
		$data['value'] = _t($id, [], null, $context);

		return $this->succeed($data);
	}

}
