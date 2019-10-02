<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Services;

/**
 * Database Helper Library Class
 * 데이터베이스 관리 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class DBHelper
{
    protected $db = null;

    /* -------------------------------------------------------------------------------- */

    /**
     * Constructor
     * 생성자
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /* -------------------------------------------------------------------------------- */

    /**
     * Initialize Database.
     * Create database tables and set some of settings.
     *
     * @param BaseConnection $db    db 가 null 값이 아니면, 이 연결 db 를 사용
     * @return  bool
     */
    public function initializeDatabase($db = null)
    {
        if ( isInstalled() ) return false;


        echo getInstallCode();
        if ( getInstallCode() === 0 ) {
            return false;
        }

        $db =& $db ?? $this->db;

        $charset_collate = 'DEFAULT CHARACTER SET ' . Consts::DB_CHARSET . ' COLLATE ' . Consts::DB_COLLATE ;

        $tbl_actions = $db->prefixTable(Consts::DB_TABLE_ACTIONS);
        $tbl_users_meta = $db->prefixTable(Consts::DB_TABLE_USERS_META);

        $settings = Services::settings($db, false);
        $userroles = Services::userroles($db, null, false);

        $ret = $db->query("
        CREATE TABLE IF NOT EXISTS `$tbl_actions` (
            `act_id` int unsigned NOT NULL AUTO_INCREMENT,
            `act_name` varchar(64) NOT NULL,
            `act_text` varchar(100) NOT NULL DEFAULT '',
            `act_parent_id` int unsigned NOT NULL DEFAULT '0',
            `act_auth_roles` text NOT NULL,
            PRIMARY KEY (`act_id`),
            UNIQUE KEY `act_name` (`act_name`)
        ) $charset_collate;
        ");
        if (!$ret) return false;

        $users = Services::users($db, null, false);

        $ret = $db->query("
        CREATE TABLE IF NOT EXISTS `$tbl_users_meta` (
            `um_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `um_u_id` bigint unsigned NOT NULL DEFAULT 0,
            `um_key` varchar(100) NOT NULL,
            `um_value` text NOT NULL DEFAULT '',
            PRIMARY KEY (`um_id`),
            KEY `um_u_id` (`um_u_id`),
            KEY `um_key` (`um_key`)
        ) $charset_collate;
        ");
        if (!$ret) return false;

        $uauth = Services::uauth($db, null, false);

        // 생성해야할 테이블
		// --- Page 게시판
        // --- Login 로그인 이력, Visit 방문 이력
        // --- Board 게시판

        // 작업중...

        // 생성할 설정값
        // - 회원 인증코드 후 인증 가능 시간.
        // - 회원가입폼에 들어갈 데이터

        return true;


        /* // 이메일을 보내자! Linux 계열에서는 sendmail 을 쓰면 엄청 편함. smtp 계정따위 필요없음. (윈도우 ㅠㅠ) 가능한 리눅스에 설치를 해야할 이유.
        $self->load->library('email');

        $email_cfg['mailtype'] = 'html';
        $email_cfg['newline'] = "\r\n";
        if ( trim(ini_get('sendmail_path')) == '' ) {   // 리눅스 계열의 OS 가 아니거나 sendmail 이 설치/설정이 되어있지 않은 경우
            $email_cfg['protocol'] = 'smtp';
            //$email_cfg['smtp_host'] = 'ssl://smtp.gmail.com';
            //$email_cfg['smtp_user'] = 'choi9081@gmail.com';
            //$email_cfg['smtp_pass'] = 'gwangwonc1!';
            $email_cfg['smtp_host'] = 'ssl://smtp.naver.com';
            $email_cfg['smtp_user'] = 'eterv';
            $email_cfg['smtp_pass'] = 'eternity7&';
            $email_cfg['smtp_port'] = 465;
        } else {
            $email_cfg['protocol'] = 'mail';
        }
        $self->email->initialize($email_cfg);

        $self->email->from('eterv@naver.com', '웹사이트3');
        $self->email->to('eterv@naver.com, choi9081@gmail.com');

        $self->email->subject('신개념 제목을 바꿔봅니다. TEST');
        $self->email->message('<body><h1>제목 1</h1><p>Testing the email class.<br>이 <a href="' . a_guess_base_url() . 'phpinfo" target="_blank">링크</a>를 눌러 인증을 완료하세요!</p></body>');

        var_dump( $self->email->send() );
        //echo ( $self->email->print_debugger() );
        //_echo( $email_cfg['protocol'] );
        */





        //$bb = (int) null;

        $b1 = [];
        $b1[] = 'a123';
        $b1[] = 'A23B';
        $b1[] = 'A aa';
        $b1[] = '하1s23';



        /*
        // 성능 비교
        $t1 = microtime(true);
        for ($i = 0; $i < 100000; $i++) {
            //
        }
        $t2 = microtime(true);

        $t4 = microtime(true);
        for ($i = 0; $i < 100000; $i++) {
            //
        }
        $t5 = microtime(true);

        $t3 = $t2 - $t1;
        $t6 = $t5 - $t4;

        echo $t3 . '<br>';
        echo $t6 . '<br>';*/

        /*$result = $self->validator->validate([ 'data' => 'ABC', 'rules' => [ 'method::dbhelper,u_check' ] ]);
        if (!$result) _echo( $self->validator->errorLast()['error'] );
        $result = $self->validator->validate([ 'data' => 'ABCD', 'rules' => [ 'method::dbhelper,u_check' ] ]);
        if (!$result) _echo( $self->validator->errorLast()['error'] );

        $result = $self->validator->validate([ 'data' => 'ABC', 'rules' => [ 'function::u_check2' ] ]);
        if (!$result) _echo( $self->validator->errorLast()['error'] );
        $result = $self->validator->validate([ 'data' => 'test', 'rules' => [ 'function::u_check2' ] ]);
        if (!$result) _echo( $self->validator->errorLast()['error'] );*/

        // 테스트 구문.,.,.
        $a1 = [];
        //$a1[] = $self->userroles->checkCanModify('3', 'member', 'ass', 3);

        //$a1[] = isInteger('-1023');


        foreach ($a1 as $item) {
            var_dump( $item );
            //var_dump( $self->userroles->errorLast()['error'] );
        }

        //$self->userroles->removeItem( $this->userroles->getIDfromName('admin') );

        return true;
    }

}


/* -------------------------------------------------------------------------------- */

/**
 * 함수 설명
 *
 * @param   string  $method     Optional. Requested method. Default POST.
 * @return  bool
 */
function a_db_test()
{

}

/* -------------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------------- */