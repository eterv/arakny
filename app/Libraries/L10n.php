<?php namespace Arakny\Libraries;

use Config\Services;

/**
 * L10n (Localization) Library Class
 * 지역화 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class L10n
{
    /** @var Settings $settings */
    protected $settings = null;

    protected $locale;
    protected $locale_admin;

    protected $language = null;

    public function __construct()
    {
        // Load App Settings
        $this->settings = Services::settings();

        if ( isInstalled() ) {
            $this->locale = $this->settings->get(Settings::locale);
            $this->locale_admin = $this->settings->get(Settings::admin_locale);
        } else {
            $this->locale = $_POST['locale'] ?? 'en';
            $this->locale_admin = $this->locale;
        }

        $this->language = Services::language();

        // 현재 페이지가 관리자 페이지라면,
        //
        // ***** directory admin 부분은 아직 검증되지 않음 ... 이후 테스트 필요
        //
		// locale 지정
        if ( isAdminPage() ) {
            $this->language->setLocale($this->locale_admin);
            setlocale(LC_ALL, $this->locale_admin . '.UTF-8');
        } else {
            $this->language->setLocale($this->locale);
			setlocale(LC_ALL, $this->locale . '.UTF-8');
        }
    }

    /**
     * Return translated text.
     * 번역된 문자열을 반환한다.
     *
     * @param string $line
     * @param array $args
     * @param string $locale
     * @return string
     */
    public function translate(string $line, array $args = [], string $locale = null)
    {
        if (empty($locale)) return $this->language->getLine($line, $args);
        return Services::language($locale)->getLine($line, $args);
    }

    /**
     * Return translated text. (Globals Translation File)
     * 번역된 문자열을 반환한다. (Globals 번역 파일)
     *
     * @param string $line
     * @param array $args
     * @param string $locale
     * @return string
     */
    public function translateGlobals(string $line, array $args = [], string $locale = null)
    {
        if (empty($locale)) return $this->language->getLine('Globals.' . $line, $args);
        return Services::language($locale)->getLine($line, $args);
    }

    public function getAcceptLanguages() {
        if ( ! isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return [ 'en' => 1 ];

        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
        if (count($lang_parse[1]) == 0) return [ 'en' => 1 ];

        $langs = array_combine($lang_parse[1], $lang_parse[4]);
        foreach ($langs as $lang => $val) {
            if ($val === '') $langs[$lang] = 1;
        }
        arsort($langs, SORT_NUMERIC);
        return $langs;
    }

    public function getPreferredLanguage($fullcode = true) {
        $lcode = array_keys(self::getAcceptLanguages())[0];
        return ( $fullcode ? $lcode : explode('-', $lcode)[0] );
    }

	/**
	 * Retrieve the list of supported languages.
	 * 지원하는 언어 목록을 가져온다.
	 *
	 * @return array
	 */
    public function getSupportedLocales()
	{
		$list = [];
		$list[] = [ 'v' => 'en-US', 'label' => 'English (United States)' ];
		$list[] = [ 'v' => 'ko-KR', 'label' => 'Korean (한국어)' ];

		// @todo 추후에, 언어 폴더와 파일을 실제로 조회하여 목록을 동적으로 만든다.

		return $list;
	}

}

