<?php namespace Arakny\Libraries;

use Arakny\Constants\Consts;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\URI;
use Config\Database;
use Config\Services;

/**
 * Statistic (Stat) Library Class
 * 통계 라이브러리 클래스
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */
class Stat
{
    protected $table = Consts::DB_TABLE_STAT;

    /* Fields - BEGIN */

    const st_id = 'st_id';
	const st_u_id = 'st_u_id';
	const st_ip = 'st_ip';

	const st_date = 'st_date';
	const st_time = 'st_time';
	const st_weekday = 'st_weekday';

	const st_agent_name = 'st_agent_name';
	const st_agent_ver = 'st_agent_ver';
	const st_os = 'st_os';
	const st_mobile = 'st_mobile';

	const st_uri = 'st_uri';
	const st_inflow_uri = 'st_inflow_uri';
	const st_inflow_host = 'st_inflow_host';
	const st_inflow_keyword = 'st_inflow_keyword';

    /* Fields - END */

	protected $db = null;
	protected $qb = null;

	/* -------------------------------------------------------------------------------- */
	/* 		Initialization (초기화)
	/* -------------------------------------------------------------------------------- */

	/**
	 * Initialize the table that be connected with this library.
	 * 현재 라이브러리와 연관된 테이블을 초기화 한다.
	 *
	 * @return bool
	 */
    protected function initTable() {
        $table = $this->db->prefixTable($this->table);
        $ret = $this->db->query("
        CREATE TABLE IF NOT EXISTS `$table` (
            `st_id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `st_u_id` bigint unsigned NOT NULL,
            `st_ip` varchar(45) NOT NULL,
            
            `st_date` date NOT NULL,
            `st_time` time NOT NULL,
            `st_weekday` tinyint unsigned NOT NULL,
            
            `st_agent_name` varchar(32) NOT NULL,
            `st_agent_ver` varchar(32) NOT NULL,
            `st_os` varchar(32) NOT NULL,
            `st_mobile` varchar(32) NOT NULL,
            
            `st_uri` text NOT NULL,
            `st_inflow_uri` text NOT NULL,
            `st_inflow_host` varchar(255) NOT NULL,
            `st_inflow_keyword` varchar(255) NOT NULL,
            
            PRIMARY KEY (`st_id`)
        ) " . Consts::SQL_CHARSET_COLLATE
        );
        if (! $ret) {
            return false;
        }

        return true;
    }

    /**
     * Constructor / 생성자
     */
    public function __construct()
    {
		$this->db = Database::connect();
		$this->qb = $this->db->table($this->table);

		if ( ! $this->db->tableExists($this->table) && ! $this->initTable() ) {
			throw new DatabaseException(_g('e_db_init_failure'));
		}
	}

	public function addLog($data)
	{
		if (empty($data) || ! is_array($data)) return false;
		if (! isset($data['uri'])) return false;
		if (! isset($data['referer'])) return false;

		// 미 테스트...

    	$u_id = Services::auth()->getCurrentUserId();

    	$agent = service_useragent();

    	$uriObj = new URI($data['uri']);
    	$uriPath = $uriObj->getPath();
    	$uriQuery = $uriObj->getQuery();
    	$uri = $uriPath . (empty($uriQuery) ? '' : '?' . $uriQuery);

    	list($inf_uri, $inf_host, $inf_keyword) = $this->getInflowDatas($data['referer']);

    	$data = [
			self::st_u_id => $u_id,
			self::st_ip => _ipAddress(),

			self::st_date => date('Y-m-d'),
			self::st_time => date('H:i:s'),
			self::st_weekday => date('N'),

			self::st_agent_name => $agent->getBrowser(),
			self::st_agent_ver => $agent->getVersion(),
			self::st_os => $agent->getPlatform(),
			self::st_mobile => $agent->getMobile(),

			self::st_uri => $uri,
			self::st_inflow_uri => $inf_uri,
			self::st_inflow_host => $inf_host,
			self::st_inflow_keyword => $inf_keyword,
		];
		$result = $this->qb->insert($data);
		if (! $result) return false;

		return $this->db->insertID();
	}

	protected function getInflowDatas($referer)
	{
		$search = [
			"bing.com" => "q",
			"google." => "q",
			"aol." => "q",
			"ask." => "q",
			"naver.com" => "query",
			"yahoo.cn" => "q",
			"yahoo." => "p",
			"daum.net" => "q",
			"zum.com" => "query",
			"nate.com" => "q",
			"gajai.com" => "keyword",
			"chol.com" => "q",
			"dreamwiz.com" => "sword",
			"simmani.com" => "q",
			"joinsmsn.com" => "q",
			"korea.com" => "q",
			"lycos.com.cn" => "searchkey",
			"lycos." => "query",
			"unitel.co.kr" => "key",
			"twitter.com" => "q",
			"incruit.com" => "kw",

			"baidu.com" => "wd",
			"altavista.com" => "p",
			"soso.com" => "w",
			"etao.com" => "q",
			"excite.com" => "q",
			"blekko.com" => "q",
			"mamma.com" => "q",

			"nifty.com" => "q",
			"sina.com.cn" => "key",
			"hotbot.com" => "q",
			"cnet.com" => "query",
			"dmoz.org" => "q",
			"sogou.com" => "query",

			"naver.jp" => "q",
			"baidu.jp" => "wd",
			"biglobe.ne.jp" => "q",
			"allabout.co.jp" => "q",
			"goo.ne.jp" => "MT",

			"infospace.com" => "q",
			"about.com" => "q",
			"godado.co.uk" => "q",
			"mosaicfx.com" => "search",
			"optuszoo.com" => "q",
		];

		$output = parse_url($referer);
		if (! isset($output['host'])) {
			return [ $referer, '', '' ];
		}
		$host = $output['host'];

		if (! isset($output['query'])) {
			return [ $referer, $host, '' ];
		}
		$query = $output['query'];
		parse_str($query, $queryArr);
		$keyword = '';

		foreach ($search as $keys => $values) {
			$pos = strstr( $host, preg_replace(["/\.$/"], [".*"], $keys) );
			if ($pos) {
				if (isset($queryArr[$values])) $keyword = $queryArr[$values];
				break;
			}
		}

		return [ $referer, $host, $keyword ];
	}


	/* -------------------------------------------------------------------------------- */
	/* 		Events (이벤트)
	/* -------------------------------------------------------------------------------- */

}
