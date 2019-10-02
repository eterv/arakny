<?php namespace Arakny\Libraries;

use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Format;

/**
 * API Trait
 * API 제공 관련 트레이트
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 *
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 * @property \CodeIgniter\HTTP\Response        $response
 */
trait APITrait
{

	/* -------------------------------------------------------------------------------- */
	/* 		AJAX
	/* -------------------------------------------------------------------------------- */

	/**
	 * Check if request is AJAX and the request method is GET.
	 * If there is a problem, 404 (Page Not Found) error is generated.
	 * AJAX 요청이고, 요청 메소드가 GET 인지 여부를 확인한다.
	 * 문제가 있으면 404 (페이지 찾을 수 없음) 오류를 발생시킨다.
	 *
	 * @return bool
	 */
	protected function ajaxGET()
	{
		return $this->checkAjaxMethodWith404('GET');
	}

	/**
	 * Check if request is AJAX and the request method is POST.
	 * If there is a problem, 404 (Page Not Found) error is generated.
	 * AJAX 요청이고, 요청 메소드가 POST 인지 여부를 확인한다.
	 * 문제가 있으면 404 (페이지 찾을 수 없음) 오류를 발생시킨다.
	 *
	 * @return bool
	 */
	protected function ajaxPOST()
	{
		return $this->checkAjaxMethodWith404('POST');
	}

    /**
     * AJAX 요청이고, 요청 메소드가 일치한지 여부를 반환한다.
     *
     * @param string $method    Optional. Requested method. Default POST.
     * @return bool
     */
	protected function checkAjaxMethod($method = 'POST')
	{
        if ( ! $this->request->isAJAX() ||
			 $this->request->getMethod(true) !== strtoupper( $method ) ) {
            return false;
        }
        return true;
    }

    /**
     * AJAX 요청이고, 요청 메소드가 일치한지 여부를 확인한 후, 문제가 있으면 404 페이지 없음 오류를 발생시킨다.
     *
     * @param string $method    Optional. Requested method. Default POST.
     * @return bool
     */
	protected function checkAjaxMethodWith404($method = 'POST')
	{
        if ( ! $this->request->isAJAX() ||
			 $this->request->getMethod(true) !== strtoupper( $method ) ) {
            ob_clean();
            throw PageNotFoundException::forPageNotFound(null);
        }
        return true;
    }

	/* -------------------------------------------------------------------------------- */
	/* 		Response
	/* -------------------------------------------------------------------------------- */

    /**
     * Used for generic failures that no custom methods exist for.
	 * 실패 메시지와 데이터를 반환한다.
     *
     * @param string $message  an string message
     * @param mixed $data
     * @param int|null     $status HTTP status code
     * @param string|null  $code   Custom, API-specific, error code
     * @param string       $customMessage
     *
     * @return mixed
     */
    protected function fail(string $message, $data = null, int $status = 400, string $code = null, string $customMessage = '')
    {
        ob_clean();

        $response = [
            'result'    => 'failure',
            'status'    => $status,
            'error'	    => $code === null ? $status : $code,
            'message'   => $message,
            'data'	    => $data ?? [],
        ];

        $response['where'] = $response['data']['where'] ?? 'general';

        return $this->respond($response, $status, $customMessage);
        //$this->fail($message, $status, $code, $customMessage);
    }

    /**
     * Provides a single, simple method to return an API response, formatted
     * to match the requested format, with proper content-type and status code.
	 * 성공 메시지와 데이터를 반환한다.
     *
     * @param null   $data
     * @param int    $status
     * @param string $customMessage
     *
     * @return mixed
     */
    protected function succeed($data = null, int $status = 200, string $customMessage = '')
    {
        ob_clean();

        $response = [
            'result'    => 'success',
            'status'    => $status,
            'message'   => $customMessage,
            'data'	    => $data ?? [],
        ];

        return $this->respond($response, $status, $customMessage);
    }

	/* -------------------------------------------------------------------------------- */
	/* 		ResponseTrait 에서 가져온 필수 메소드
	/* -------------------------------------------------------------------------------- */

	/**
	 * Provides a single, simple method to return an API response, formatted
	 * to match the requested format, with proper content-type and status code.
	 *
	 * @param null    $data
	 * @param integer $status
	 * @param string  $message
	 *
	 * @return mixed
	 */
	protected function respond($data = null, int $status = null, string $message = '')
	{
		// If data is null and status code not provided, exit and bail
		if ($data === null && $status === null)
		{
			$status = 404;

			// Create the output var here in case of $this->response([]);
			$output = null;
		} // If data is null but status provided, keep the output empty.
		elseif ($data === null && is_numeric($status))
		{
			$output = null;
		}
		else
		{
			$status = empty($status) ? 200 : $status;
			$output = $this->format($data);
		}

		return $this->response->setBody($output)
			->setStatusCode($status, $message);
	}

	/**
	 * Handles formatting a response. Currently makes some heavy assumptions
	 * and needs updating! :)
	 *
	 * @param null $data
	 *
	 * @return null|string
	 */
	protected function format($data = null)
	{
		// If the data is a string, there's not much we can do to it...
		if (is_string($data))
		{
			// The content type should be text/... and not application/...
			$contentType = $this->response->getHeaderLine('Content-Type');
			$contentType = str_replace('application/json', 'text/html', $contentType);
			$contentType = str_replace('application/', 'text/', $contentType);
			$this->response->setContentType($contentType);

			return $data;
		}

		// Determine correct response type through content negotiation
		$config = new Format();
		$format = $this->request->negotiate('media', $config->supportedResponseFormats, false);

		$this->response->setContentType($format);

		// if we don't have a formatter, make one
		if (! isset($this->formatter))
		{
			// if no formatter, use the default
			$this->formatter = $config->getFormatter($format);
		}

		if ($format !== 'application/json')
		{
			// Recursively convert objects into associative arrays
			// Conversion not required for JSONFormatter
			$data = json_decode(json_encode($data), true);
		}

		return $this->formatter->format($data);
	}

}

/* -------------------------------------------------------------------------------- */