<?php namespace Config;

use Arakny;
use Arakny\Filters as A_Filters;
use CodeIgniter\Filters as CI_Filters;
use CodeIgniter\Config\BaseConfig;

/**
 * Filters Class
 */
class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => A_Filters\CSRF::class,
		'throttle' => A_Filters\Throttle::class,
		'toolbar'  => CI_Filters\DebugToolbar::class,
		'honeypot' => CI_Filters\Honeypot::class,

		// Arakny Add
		'loader'   => Arakny\Loader::class,
		'guard'    => A_Filters\Guard::class,
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			'loader',
			'guard',
			'csrf',
		],
		'after'  => [
			//'toolbar',
			//'honeypot',
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [
		'post' => [ 'throttle' ],
	];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [];
}
