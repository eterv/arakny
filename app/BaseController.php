<?php namespace Arakny;

use Arakny\Constants\Consts;
use Arakny\Libraries;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Arakny Base Controller Class
 * 아라크니 기본 컨트롤러 클래스
 *
 * CI 컨트롤러를 확장함으로, 모든 하위 컨트롤러들의 기본 작업을 수행합니다.
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
abstract class BaseController extends Controller
{
    use Libraries\APITrait;

    protected $helpers = [ 'filesystem', 'text' ];

    /** @var string $controllerName 컨트롤러 이름 */
    protected $controllerName = null;

	/** @var string $eventGroupName 이벤트 그룹 이름 (마침표(.)로 구분하고, 마침표(.)로 끝내야 한다) */
    protected $eventGroupName = null;

	/**
	 * 마지막 유효성 오류 - 데이터 배열, 메시지
	 * 데이터 배열 키 중 where -- validation
	 */
	protected $validationErrorData = [];
	protected $validationErrorMessage = '';

	/**
	 * 이 컨트롤러에 연결되는 기본 모델 Model
	 *
	 * @var BaseModel $model
	 */
	protected $model = null;

    /** @var Libraries\Authentication */
    protected $auth = null;
	/** @var Libraries\Settings */
	protected $settings = null;
    /** @var Libraries\Theme */
    protected $theme = null;

    /** @var array $data */
    protected $data = [];

    /**
     * Constructor / 생성자
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->controllerName = routerController();

        // 완전히 설치되었다면, 얼마의 공용 라이브러리를 변수에 할당한다.
        if (isInstalled()) {
            $this->auth = Services::auth();
            $this->settings = Services::settings();
            $this->theme = Services::theme();
        }

        /*
        $statData = [
        	'uri' => current_url(),
        	'referer' => service_useragent()->getReferrer(),
		];
        Services::stat()->addLog($statData);*/

        // 임시 Routes 테스트
        //_echo( routerDirectory() . ' || ' . routerController() . ' || ' . routerMethod() );
    }

    /**
     * View installation page
     * 설치 페이지를 화면에 보여준다.
     *
     * @param string $name
     * @param array $data
     */
    protected function _view($name = null, $data = null)
    {
        $data = $data ?? $this->data;

        if ( isAdminPage() ) {

            if ( routerController() === 'admin\install' ) {
                echo view('admin/install', $this->data);

            } else {
                echo view('admin/header', $data);
                echo view($name, $data);
                echo view('admin/footer', $data);
            }

        } else {
            throw new RuntimeException('Require to override _view function.');

        }
    }

	/* -------------------------------------------------------------------------------- */
	/* 		Response Helper (응답 헬퍼)
	/* -------------------------------------------------------------------------------- */

	/**
	 * 사용자 오류 메시지와 데이터를 반환한다.
	 *
	 * @return mixed
	 */
	protected function failError()
	{
		return $this->fail(errorMessage(), errorData());
	}

	/**
	 * [올바르지 않은 요청] 오류 메시지를 반환한다.
	 *
	 * @return mixed
	 */
	protected function failErrorInvalidRequest()
	{
		return $this->fail(_g(Consts::E_INVALID_REQUEST));
	}

	/**
	 * 모델 작업에 실패했다는 메시지와 데이터를 반환한다.
	 *
	 * @param BaseModel $model
	 * @return mixed
	 */
	protected function failModel($model = null)
	{
		if ($model === null) $model = $this->model;
		return $this->fail($model->lastErrorMessage(), $model->lastErrorData());
	}

	/**
	 * 유효성 검사에 실패했다는 메시지와 데이터를 반환한다.
	 *
	 * @return mixed
	 */
	protected function failValidation()
	{
		return $this->fail($this->validationErrorMessage, $this->validationErrorData);
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Validation (유효성)
	/* -------------------------------------------------------------------------------- */

	/**
	 * @inheritdoc
	 */
	public function validate($rules, array $messages = []): bool
	{
		$result = parent::validate($rules, $messages);

		if (! $result) {
			$errors = $this->validator->getErrors();
			$message = '';

			foreach ($errors as $field => $error) {
				$message = $error;
				break;
			}

			// 에러 데이터 만들기
			$this->validationErrorData = [
				'where' => 'validation',
				'errors' => $errors,
			];
			$this->validationErrorMessage = $message;
		}

		return $result;
	}

	/**
	 * Return the data of last validation error(s) that occurred.
	 * 발생한 마지막 유효성 오류의 데이터를 반환한다.
	 *
	 * @return array
	 */
	protected function validationErrorData()
	{
		return $this->validationErrorData;
	}

	/**
	 * Return the message string of last validation error(s) that occurred.
	 * 발생한 마지막 유효성 오류의 문자열 메시지를 반환한다.
	 *
	 * @return string
	 */
	protected function validationErrorMessage()
	{
		return $this->validationErrorMessage;
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Events (이벤트)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Trigger given filter event.
	 * 주어진 필터 이벤트를 트리거한다.
	 *
	 * @param string $eventName
	 * @param mixed $value
	 * @param array $arguments
	 * @return mixed
	 */
	protected function triggerFilter($eventName, $value, ...$arguments)
	{
		return triggerFilter($this->eventGroupName . '.' . $eventName, $value, $arguments);
	}

}