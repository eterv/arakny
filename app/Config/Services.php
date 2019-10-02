<?php namespace Config;

use Arakny\Libraries;
use Arakny\Models;
use CodeIgniter\Config\Services as CoreServices;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Validation\Validation;

require_once SYSTEMPATH . 'Config/Services.php';

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{

	//    public static function example($getShared = true)
	//    {
	//        if ($getShared)
	//        {
	//            return static::getSharedInstance('example');
	//        }
	//
	//        return new \CodeIgniter\Example();
	//    }

	public static function adminpage($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('adminpage');

		return new Libraries\AdminPage();
	}

	public static function auth($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('auth');

		return new Libraries\Authentication();
	}

	public static function captcha($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('captcha');

		return new Libraries\Captcha();
	}

	public static function dbhelper($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('dbhelper');

		return new Libraries\DBHelper();
	}

	public static function error($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('error');

		return new Libraries\Error();
	}

	public static function file($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('file');

		return new Libraries\File();
	}

	public static function gate($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('gate');

		return new Libraries\Gate();
	}

	public static function l10n($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('l10n');

		return new Libraries\L10n();
	}

	public static function honeypot(BaseConfig $config = null, bool $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('honeypot', $config);

		if (is_null($config)) {
			$config = new Honeypot();
		}

		return new \CodeIgniter\Honeypot\Honeypot($config);
	}

	public static function html($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('html');

		return new Libraries\Html();
	}

	public static function page($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('page');

		return new Libraries\Page();
	}

	public static function settings(BaseConnection &$db = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('settings', $db);

		return new Libraries\Settings($db);
	}

	public static function stat($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('stat');

		return new Libraries\Stat();
	}

	public static function theme($getShared = true)
	{
		if ($getShared) return static::getSharedInstance('theme');

		return new Libraries\Theme();
	}

	// Model (모델) //

	/**
	 * @param BaseConnection|null $db
	 * @param Validation|null $validation
	 * @param bool $getShared
	 * @return Models\DocsModel|mixed
	 */
	public static function docs(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('docs', $db, $validation);

		return new Models\DocsModel($db, $validation);
	}

	public static function files(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('files', $db, $validation);

		return new Models\FilesModel($db, $validation);
	}

	public static function login(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('login', $db, $validation);

		return new Models\LoginModel($db, $validation);
	}

	public static function permissions(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('permissions', $db, $validation);

		return new Models\PermissionsModel($db, $validation);
	}

	public static function uauth(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('uauth', $db, $validation);

		return new Models\UAuthModel($db, $validation);
	}

	public static function userroles(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('userroles', $db, $validation);

		return new Models\UserRolesModel($db, $validation);
	}

	public static function users(BaseConnection &$db = null, Validation $validation = null, $getShared = true)
	{
		if ($getShared) return static::getSharedInstance('users', $db, $validation);

		return new Models\UsersModel($db, $validation);
	}

}
