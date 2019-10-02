<?php namespace Arakny\Controllers\Admin;

use Arakny\BaseController;
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
class Files extends BaseController
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
     */
    public function index()
	{
        return $this->theme->renderAdminPage('files');
	}

}
