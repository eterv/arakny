<?php namespace Arakny\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Throttle implements FilterInterface
{
	/**
	 * @inheritDoc
	 */
	public function before(RequestInterface $request)
	{
		$throttler = Services::throttler();

		// Restrict an IP address to no more
		// than 1 request per second across the
		// entire site.
		if ($throttler->check($request->getIPAddress(), MINUTE * 5, MINUTE) === false)
		{
			return Services::response()->setStatusCode(429);
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function after(RequestInterface $request, ResponseInterface $response) { }
}
