<?php
/**
 * Arakny
 * 아라크니
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @copyright	Copyright (c) 2019~, Lucas Choi. (https://arakny.com/)
 * @link        https://arakny.com
 * @package     Arakny
 */

/*
 * --------------------------------------------------------------------
 *   This file is one of core file.  So, DO NOT EDIT BELOW THIS !!!
 *   이 파일은 핵심 코어 파일입니다.  절대 이 아래를 임의로 수정하지 마세요 !!!
 * --------------------------------------------------------------------
 */

// Valid PHP Version?
/**
 * ARAKNY EDIT <
 */
$minPHPVersion = '7.1'; // '7.2';
/**
 * ARAKNY EDIT >
 */
if (phpversion() < $minPHPVersion)
{
	die("Your PHP version must be {$minPHPVersion} or higher to run Arakny. Current version: " . phpversion());
}
unset($minPHPVersion);

/**
 * ARAKNY ADD <
 */
// 웹 상에서 연결 가능한 최상위 디렉토리의 폴더 이름 입니다.
$public_path = explode(DIRECTORY_SEPARATOR, __DIR__);
define( 'FOLDER_PUBLIC', end($public_path) );
unset($public_path);
/**
 * ARAKNY ADD >
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Location of the Paths config file.
// This is the line that might need to be changed, depending on your folder structure.
$pathsPath = FCPATH . '../app/Config/Paths.php';
// ^^^ Change this if you move your application folder

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

// Load our paths config file
require $pathsPath;
$paths = new Config\Paths();

// Location of the framework bootstrap file.
$app = require rtrim($paths->systemDirectory, '/ ') . '/bootstrap.php';

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */
$app->run();
