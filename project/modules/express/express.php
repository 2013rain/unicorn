<?php
/**
 * 管理员后台会员操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class index extends admin {
	
	private $db, $goods, $verify_db;
	
	function __construct() {
		parent::__construct();
		//$this->db = pc_base::load_model('member_model');
		$this->db = pc_base::load_model('member_express');
		$this->goods = pc_base::load_model('member_express_goods');
	}

	/**
	 * defalut
	 */
	function init() {
		$userid = $_SESSION['userid'];
		$where = [
			'userid' => $userid,
		];
		$infos = $this->db->listinfo($where, '', $page, 20);
		$pages = $this->db->pages;
		include $this->admin_tpl('express_init');
/*
		$userid = $_SESSION['userid'];
		$admin_username = param::get_cookie('admin_username');
		$page = $_GET['page'] ? intval($_GET['page']) : '1';
		$infos = $this->db->listinfo('', '', $page, 20);
		$pages = $this->db->pages;
		$roles = getcache('role','commons');
*/
		//include $this->admin_tpl('admin_list');
		
		/*
		$show_header = $show_scroll = true;
		pc_base::load_sys_class('form', '', 0);
		$this->verify_db = pc_base::load_model('member_verify_model');
		include $this->admin_tpl('express_init');
		
		//搜索框
		$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
		$type = isset($_GET['type']) ? $_GET['type'] : '';
		$groupid = isset($_GET['groupid']) ? $_GET['groupid'] : '';
		$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : date('Y-m-d', SYS_TIME-date('t', SYS_TIME)*86400);
		$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
		$grouplist = getcache('grouplist');
		foreach($grouplist as $k=>$v) {
			$grouplist[$k] = $v['name'];
		}

		$memberinfo['totalnum'] = $this->db->count();
		$memberinfo['vipnum'] = $this->db->count(array('vip'=>1));
		$memberinfo['verifynum'] = $this->verify_db->count(array('status'=>0));

		$todaytime = strtotime(date('Y-m-d', SYS_TIME));
		$memberinfo['today_member'] = $this->db->count("`regdate` > '$todaytime'");
		
		include $this->admin_tpl('express_init');
		*/
	}
	
}
?>
