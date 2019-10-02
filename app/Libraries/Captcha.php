<?php namespace Arakny\Libraries;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

/**
 * Captcha Library Class
 * 캡챠 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Captcha
{

	const CAPTCHA_LENGTH = 5;

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {

    }

    /* -------------------------------------------------------------------------------- */
    /* Captcha methods */
    /* -------------------------------------------------------------------------------- */

    /**
     * Generate new captcha value and return the image data that is encoded with base64.
     * 새 캡챠 값을 생성하고, BASE64 인코딩된 이미지 데이터를 반환한다.
     *
	 * @param bool $onlyNumber
	 * @param int $height
     * @return array
     */
    public function generateCaptcha($onlyNumber = false, $height = 45)
    {
    	$charset = $onlyNumber ? '0123456789' : 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789'; // abcdefghijklmnpqrstuvwxyz

        $builder = new CaptchaBuilder(null, new PhraseBuilder(self::CAPTCHA_LENGTH, $charset));
        $builder->build(130, $height);

        return [
			'image' => $builder->inline(90),
			'string' => _encrypt($builder->getPhrase()),
		];
    }

    /**
     * Return captcha HTML code.
     * 캡챠 img 와 input 이 포함된 HTML 코드를 반환한다.
     *
	 * @param bool $onlyNumber
	 * @param int $height
	 * @param string $sizeClass
     * @return string
     */
    public function getCaptchaCode($onlyNumber = false, $height = 40, $sizeClass = '')
    {
    	$captcha = $this->generateCaptcha($onlyNumber, $height);

    	$captchaRule = 'required|exact_length[5]|regex_match[/[A-Za-z0-9]{5}/]';

        $html = '
        	<style>
                #captcha-code img { border: 1px solid #ddd; }
                #captcha-input { text-transform: uppercase; }
            </style>
            
        	<div id="captcha-code" class="field">
        		<label for="captcha-input">' . _g('captcha_input') . '</label>
				<div class="control input group ' . $sizeClass . '">
					<img class="captcha-img" src="' . $captcha['image'] . '" alt="CAPTCHA">
					<a class="captcha-regenerate ai-b icon" title="Refresh CAPTCHA" href="javascript:void(0)"><i class="fas fa-sync"></i></a>
					<input type="text" name="captcha" id="captcha-input" class="captcha-input ai text center" data-rules="'. $captchaRule .'" placeholder=" ">
				</div>
				<input type="hidden" name="captchadata" class="captcha-data" value="' . $captcha['string'] . '">
			</div>
            
          	<script>
          		(function () {
          			var elCaptcha = byId("captcha-code");
          			var onlyNumber = ' . ($onlyNumber ? "true" : "false") . ';
          			var height = ' . $height . ';
          			elCaptcha.querySelector(".captcha-regenerate").addEventListener("click", function () {
          				ajaxPost("' . _url('auth/regenerate-captcha') . '", { onlyNumber: onlyNumber, height: height }, null, function(data) { 
							elCaptcha.querySelector(".captcha-img").src = data.captcha.image;
							elCaptcha.querySelector(".captcha-data").value = data.captcha.string;
						});
          			});
          		})();
			</script>
        ';

        return $html;
    }

    /**
     * Return if the given value correspond with stored value.
     * 주어진 캡챠 값이 저장된 캡챠와 일치한지 여부를 반환한다.
     *
     * @param string $value
     * @param string $encValue
     * @return bool
     */
    public function testCaptcha($value, $encValue)
    {
    	$orig = _decrypt($encValue);
    	if (! $orig) return false;

        $builder = new CaptchaBuilder($orig);
        return $builder->testPhrase($value);
    }

    public function testCaptchaByPostData()
	{
		$captcha = inputPost('captcha');
		$captchaData = inputPost('captchadata');
		if ($captcha === null ||$captchaData === null) return false;

		return $this->testCaptcha($captcha, $captchaData);
	}

    /* -------------------------------------------------------------------------------- */
    /* Protected methods */
    /* -------------------------------------------------------------------------------- */



}
