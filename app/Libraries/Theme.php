<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use Config\Services;
use UnexpectedValueException;

/**
 * Theme Class
 * 테마 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Theme
{
    /* -------------------------------------------------------------------------------- */

    /** @var Settings $settings */
    protected $settings = null;

    protected $hasParent = false;

	protected $adminThemePath = null;
	protected $adminThemeUrl = null;

    protected $themePath = null;
    protected $themeUrl = null;
    protected $themeInfo = null;

    protected $parentPath = null;
    protected $parentUrl = null;
    protected $parentInfo = null;

    protected $baseThemePath = null;
    protected $baseThemeUrl = null;

    protected $fileExt = '.php';
    protected $oneFileExt = '.one.php';
    protected $headFile = '.head.php';
    protected $funcFile = '_functions.php';
    protected $headerFile = '_header.php';
    protected $footerFile = '_footer.php';

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {
        // Load App Settings
        $this->settings = Services::settings();

        // Set Admin Theme Path, URLs
		$this->adminThemePath = adminThemePath();
		$this->adminThemeUrl = adminThemeUrl();

        // Set Current Theme Path, URL
        $this->themePath = themesPath( $this->settings->get(Settings::theme) . DIRECTORY_SEPARATOR );
        $this->themeUrl = themesUrl( $this->settings->get(Settings::theme) . '/' );

        $this->themeInfo = require_once($this->themePath . 'settings.php');


        // 작업중 :: 이후에 버전 체크도 (아라크니 최소 버전) 해야함.


        // 부모 테마를 우선 BASE 로 기본 지정 후 진짜 부모가 존재한다면 변경한다.
        $this->parentPath = $this->baseThemePath = baseThemePath();
        $this->parentUrl = $this->baseThemeUrl = baseThemeUrl();

        if ( isset($this->themeInfo['parent']) ) {
            // Set Parent Theme Path, URL
            $this->parentPath = themesPath( $this->themeInfo['parent'] . DIRECTORY_SEPARATOR );
            $this->parentUrl = themesUrl( $this->themeInfo['parent'] . '/' );

            if ( ! is_file($this->parentPath . 'settings.php') ) {
                throw new FileNotFoundException(_g(Consts::E_FILE_NOTFOUND, [ 'settings.php' ]));
            }

            $this->parentInfo = require_once($this->parentPath . 'settings.php');

            if ( isset($this->parentInfo['parent']) ) {
                throw new UnexpectedValueException('상위 테마 "'.$this->themeInfo['parent'].'" 는 부모를 설정할 수 없습니다.');
            }

            $this->hasParent = true;
        }

        /*
        $this->twigLoader = new \Twig_Loader_Filesystem([ DIR_THEME, DIR_PARENT_THEME, DIR_BASE_THEME ]);
        $this->twig = new \Twig_Environment($this->twigLoader, [
            'debug' => (ENVIRONMENT !== 'production'),
            /*'cache' => WRITEPATH . 'cache/twig'
        ]);*/

    }

    /* -------------------------------------------------------------------------------- */

    /**
     * 부모 테마를 갖고있는지 여부를 반환한다.
     *
     * @return bool
     */
    public function hasParent()
    {
        return $this->hasParent;
    }

    /* -------------------------------------------------------------------------------- */

    /**
     * Renders the template.
     *
     * @param string $name
     * @param array $data
     * @return string
     */
    public function render($name, $data = [])
    {
        //var_dump( get_defined_vars() );

        /*var_dump( router_directory() );
        var_dump( router_controller(true, false) );
        var_dump( router_method() );*/

        $data['locale'] = $this->settings->get(Settings::locale);
        $data['_render_name'] = $name;
        $data['_theme'] = $this;

        $output = view('Renderer', $data);

		// 스크립트/스타일 자산 코드 출력
		$output = Services::html()->printAssets($output);

		return $output;
    }

    /**
     * Renders the admin page template.
     *
     * @param string $name
     * @param array $data
     * @return string
     */
    public function renderAdminPage($name, $data = [])
    {
		$data['locale'] = $this->settings->get(Settings::admin_locale);
        $data['_render_name'] = $name;
        $data['_theme'] = $this;

        $menu = _menu();

        $data['menu_main'] =& $menu['main'];
        $data['menu_sub'] =& $menu['sub'];

        $output = view('RendererAdmin', $data);
		
		// 스크립트/스타일 자산 코드 출력
		$output = Services::html()->printAssets($output);

        return $output;
    }


    /* -------------------------------------------------------------------------------- */

    /**
     * Return current theme directory path
     * 현재 테마 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return mixed
     */
    public function getCurrentThemePath($path = '')
    {
        return $this->themePath . $path;
    }

    /**
     * Return current theme directory url
     * 현재 테마 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return mixed
     */
    public function getCurrentThemeUrl($uri = '')
    {
        return $this->themeUrl . $uri;
    }

    /**
     * Return parent theme directory path
     * 부모 테마 디렉토리 경로를 반환한다.
     *
     * @param string $path
     * @return mixed
     */
    public function getParentThemePath($path = '')
    {
        return $this->parentPath . $path;
    }

    /**
     * Return parent theme directory url
     * 부모 테마 디렉토리의 URL 주소 경로를 반환한다.
     *
     * @param string $uri
     * @return mixed
     */
    public function getParentThemeUrl($uri = '')
    {
        return $this->parentUrl . $uri;
    }

    /* -------------------------------------------------------------------------------- */

    // 자식부터 상위로 파일을 찾아간다. 파일이 존재하는 경로 반환
    public function getThemeFilePath($file)
    {
        if ( is_file($this->themePath . $file) ) return $this->themePath . $file;
        if ( $this->hasParent && is_file($this->parentPath . $file) ) return $this->parentPath . $file;
        if ( is_file($this->baseThemePath . $file) ) return $this->baseThemePath . $file;
        // 파일이 존재하지 않으면 원래 테마 폴더 경로로 반환한다.
        return $this->themePath . $file;
    }

    // 자식부터 상위로 파일을 찾아간다. 파일이 존재하는 경로의 URL 주소를 반환한다.
    public function getThemeFileUrl($file)
    {
        if ( is_file($this->themePath . $file) ) return $this->themeUrl . $file;
        if ( $this->hasParent && is_file($this->parentPath . $file) ) return $this->parentUrl . $file;
        if ( is_file($this->baseThemePath . $file) ) return $this->baseThemeUrl . $file;
        // 파일이 존재하지 않으면 원래 테마 폴더 경로로 반환한다.
        return $this->themeUrl . $file;
    }

    // 자식부터 상위로 파일을 찾아간다.
    public function getThemeFunctionsPath()
    {
        $name = $this->funcFile;
        $files = [];

        if ( is_file($this->themePath . $name) ) $files[] = $this->themePath . $name;
        if ( $this->hasParent && is_file($this->parentPath . $name) ) $files[] = $this->parentPath . $name;
        if ( is_file($this->baseThemePath . $name) ) $files[] = $this->baseThemePath . $name;

        return $files;
    }

    // 자식부터 상위로 파일을 찾아간다. 파일이 존재하는 경로 반환
    public function getTemplatePath($name, $findOneFile = true, $ignoreNotFound = false)
    {
        $files[] = $this->themePath . $name . $this->fileExt;
        if ($findOneFile) $files[] = $this->themePath . $name . $this->oneFileExt;

        if ( $this->hasParent ) {
            $files[] = $this->parentPath . $name . $this->fileExt;
            if ($findOneFile) $files[] = $this->parentPath . $name . $this->oneFileExt;
        }

        $files[] = $this->baseThemePath . $name . $this->fileExt;
        if ($findOneFile) $files[] = $this->baseThemePath . $name . $this->oneFileExt;

        foreach ($files as $file) {
            if ( is_file($file) ) return $file;
        }

        if ($ignoreNotFound) {
            return false;
        } else {
            throw new \CodeIgniter\Files\Exceptions\FileNotFoundException( _g(Consts::E_FILE_NOTFOUND, [ $name . $this->fileExt ]) );
        }
    }

	// 자식부터 상위로 파일을 찾아간다. 파일이 존재하는 경로 반환
	public function getAdminTemplatePath($name, $findOneFile = true, $ignoreNotFound = false)
	{
		$files[] = $this->adminThemePath . $name . $this->fileExt;
		if ($findOneFile) $files[] = $this->adminThemePath . $name . $this->oneFileExt;

		foreach ($files as $file) {
			if ( is_file($file) ) return $file;
		}

		if ($ignoreNotFound) {
			return false;
		} else {
			throw new \CodeIgniter\Files\Exceptions\FileNotFoundException( _g(Consts::E_FILE_NOTFOUND, [ $name . $this->fileExt ]) );
		}
	}

    public function getTemplateHeadPath($name, $is_admin = false)
    {
        $name .= '.head';

		if ($is_admin) {
			return $this->getAdminTemplatePath($name, false, true);
		} else {
			return $this->getTemplatePath($name, false, true);
		}
    }

    public function getHeaderPath($name = null)
    {
        if ( empty($name) ) $name = '_header';
        else $name = '_header-' . $name;

		return $this->getTemplatePath($name, false);
    }

    public function getFooterPath($name = null)
    {
        if ( empty($name) ) $name = '_footer';
        else $name = '_footer-' . $name;

		return $this->getTemplatePath($name, false);
    }

    // 자식부터 상위로 파일을 찾아간다. 파일이 존재하는 경로 반환
    public function getIncludeTemplates($name)
    {
        $file = $this->getTemplatePath($name);
        $files = $this->getThemeFunctionsPath();

        if ( _endsWith($file, $this->oneFileExt) ) {
            $files[] = $file;

        } else {
            $headFile = $this->getTemplateHeadPath($name);
            if ($headFile !== false) {
                $files[] = $headFile;
            }

            $files[] = $this->getHeaderPath();
            $files[] = $file;
            $files[] = $this->getFooterPath();
        }
        return $files;
    }

    // 컨텐츠의 데이터를 바로 화면에 출력해 주는 용도의 함수

	/**
	 * HTML 에디터 컨텐츠 데이터를 바로 화면에 출력해 주기 위해 템플릿을 설정합니다.
	 * 공용 헤더와 푸터를 사용할 것인지의 여부를 지정할 수 있습니다.
	 *
	 * @param string $content
	 * @param bool $useHeaderFooter
	 * @return array
	 */
    public function getIncludeTemplatesByContent(string $content, bool $useHeaderFooter = true)
	{
		$files = $this->getThemeFunctionsPath();

		if ($useHeaderFooter) $files[] = $this->getHeaderPath();
		$files[] = [ 'content' => $content ];
		if ($useHeaderFooter) $files[] = $this->getFooterPath();

		return $files;
	}

	/**
	 * 임의로 지정한 경로의 데이터를 불러와 화면에 출력해 주기 위해 템플릿을 설정합니다.
	 * 공용 헤더와 푸터를 사용할 것인지의 여부를 지정할 수 있습니다.
	 *
	 * @param string $path
	 * @param bool $useHeaderFooter
	 * @return array
	 */
	public function getIncludeTemplatesByPath(string $path, bool $useHeaderFooter = true)
	{
		$files = $this->getThemeFunctionsPath();

		if ($useHeaderFooter) $files[] = $this->getHeaderPath();
		$files[] = $path;
		if ($useHeaderFooter) $files[] = $this->getFooterPath();

		return $files;
	}

	// 자식부터 상위로 파일을 찾아간다. 파일이 존재하는 경로 반환
	public function getIncludeAdminTemplates($name)
	{
		$file = $this->getAdminTemplatePath($name);

		// functions 파일
		$file_func = $this->adminThemePath . $this->funcFile;
		if (is_file($file_func)) {
			$files[] = $file_func;
		}

		// 단일 파일 여부에 따라,
		if ( _endsWith($file, $this->oneFileExt) ) {
			$files[] = $file;

		} else {
			// 헤드 파일
			$file_head = $file_func = $this->adminThemePath . $name . $this->headFile;
			if (is_file($file_head)) {
				$files[] = $file_head;
			}

			$files[] = $this->getAdminTemplatePath('_header', false);
			$files[] = $file;
			$files[] = $this->getAdminTemplatePath('_footer', false);
		}
		return $files;
	}

	/* -------------------------------------------------------------------------------- */
	/* 		Etc (기타 메소드)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Analyze the themes directory, retrieve the list of usable themes.
	 * 테마 폴더를 분석하여 사용 가능한 테마의 목록을 가져온다.
	 *
	 * @return array|null
	 */
	public function getThemes()
	{
		if (! is_dir(themesPath())) return null;
		$subdirs = glob(themesPath() . '*', GLOB_ONLYDIR);

		$arr = [];
		foreach ($subdirs as $subdir) {
			$infoFile = $subdir . '/' . 'settings.php';
			if (! is_file($infoFile)) continue;

			$tInfo = require($infoFile);

			$arr[] = [
				'id' => basename($subdir),
				'name' => $tInfo['name'] ?? '',
				'v' => pathinfo($subdir, PATHINFO_FILENAME),
			];
		}
		return $arr;
	}

    /**
     * 현재 페이지가 인덱스(Home) 페이지 인지 여부를 반환한다.
     *
     * @return bool
     */
    public function isHome()
    {
        return (routerController() === '\home' && routerMethod() === 'index');
    }

}