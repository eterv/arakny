<?php

/**
 * Security Functions
 * 보안 관련 함수
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

/**
 * Generates random password string.
 * 랜덤한 암호 문자열을 생성하여 반환한다.
 *
 * @param int  $length          Optional. The length of password to generate. Default 12.
 * @param bool $spechars		Optional. Whether to include other special characters. Used when generating secret keys and salts. Default false.
 * @param bool $extra_spechars  Optional. Whether to include other extra special characters. Used when generating secret keys and salts. Default false.
 * @return	mixed
 */
function _generatePassword($length = 12, $spechars = false, $extra_spechars = false)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ($spechars) $chars .= '!@#$%^&*()';
    if ($extra_spechars) $chars .= '-_[]{}<>~`+=,.;:/?|';

    $val = '';
    for ($i = 0; $i < $length; $i++) {
        $val .= substr($chars, _randomInt(0, strlen($chars) - 1), 1);
    }

    return $val;
}

/**
 * Generates secure random bytes.
 * 지정한 길이의 랜덤한 바이트를 생성한다.
 *
 * @param int $length
 * @return string
 */
function _randomBytes(int $length)
{
	try {
		return random_bytes($length);
	} catch (Exception $ex) { return null; }
}

/**
 * Generates a random integer number
 * 랜덤한 정수(난수)를 생성한다.
 *
 * @param int $min  Lower limit for the generated number
 * @param int $max  Upper limit for the generated number
 * @return  mixed
 */
function _randomInt($min, $max)
{
    $min = (int) $min;
    $max = (int) $max;

    $_min = min($min, $max);
    $_max = max($min, $max);
    $min = $_min; $max = $_max;

    if ( function_exists('random_int') ) {
        try {
            $val = random_int($min, $max);
            if ( false !== $val ) return $val;
        }
        catch ( Error $e ) { }
        catch ( Exception $e ) { }
    }

    // mt_rand 로 값을 n + 1 개를 임의로 가져오고 후 그 중 한개를 다시 랜덤으로 가져온다.
    $n = 10;
    $v = [];
    for ($i = 0; $i <= $n; $i++) {
        $v[] = mt_rand($min, $max);
    }
    $val = $v[mt_rand(0, $n)];

    return $val;
}

/* -------------------------------------------------------------------------------- */

if ( ! function_exists('_generateEncryptionKey') )
{
	/**
	 * Generate a encryption key.
	 * 암/복호화에 사용할 키를 생성합니다.
	 *
	 * @return string
	 */
	function _generateEncryptionKey()
	{
		return hash('sha256', _randomBytes(40));
	}
}

if (! function_exists('_encrypt'))
{
	/**
	 * Return encrypted string.
	 * 데이터를 암호화 한 문자열을 반환한다.
	 *
	 * @param string $data
	 * @return string
	 */
	function _encrypt($data)
	{
		$method = 'aes-256-cbc';
		$ivlen = openssl_cipher_iv_length($method);
		$iv = openssl_random_pseudo_bytes($ivlen);

		$encrypted = openssl_encrypt($data, $method, getSetting(Arakny\Libraries\Settings::enc_key), 0, $iv);
		$hash = hash('sha256', $encrypted);

		return str_replace('=', '', base64_encode($iv . $hash . $encrypted) );
	}
}

if (! function_exists('_decrypt'))
{
	/**
	 * Return decrypted string.
	 * 암호화 된 데이터를 복호화하여 반환한다.
	 *
	 * @param string $encData
	 * @return string|bool
	 */
	function _decrypt($encData)
	{
		$raw = base64_decode($encData);
		if ($raw === false) return false;

		$method = 'aes-256-cbc';
		$ivlen = openssl_cipher_iv_length($method);
		$iv = substr($raw, 0, $ivlen);

		if (strlen($raw) <= $ivlen + 64) return false;

		$hash = substr($raw, $ivlen, 64);
		$encrypted = substr($raw, $ivlen + 64);
		$data = openssl_decrypt($encrypted, $method, getSetting(Arakny\Libraries\Settings::enc_key), 0, $iv);

		if (! hash_equals($hash, hash('sha256', $encrypted))) return false;

		return $data;
	}
}

/* -------------------------------------------------------------------------------- */