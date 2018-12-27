<?php
/**
 * 管理员后台会员操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class apay extends admin {
	

	
	function __construct() {
		parent::__construct();
		$this->express_trade_log = pc_base::load_model('express_trade_log_model');
		$this->member_express = pc_base::load_model('member_express_model');
		$this->member_model = pc_base::load_model('member_model');

		
	}
	
	/**
	 * defalut
	 */
	function apay_list() {
		
		$express_no = isset($_GET['express_no']) ? trim($_GET['express_no']) : '';
		$outer_order_no = isset($_GET['outer_order_no']) ? trim($_GET['outer_order_no']) : '';

		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$where = array();
		$where["status"] = "1";
		if($express_no !="" ) {
			$where["express_no"] = $express_no;
		}
		if($outer_order_no !="" ) {
			$where["outer_order_no"] = $outer_order_no;
		}
		
		$tradelist_arr = $this->express_trade_log->listinfo($where, 'finish_time DESC', $page, 15);
		$pages = $this->express_trade_log->pages;

		
		
		foreach($tradelist_arr as $k=>$v) {
			$user = $this->member_model->get_one(array('userid'=>$v['member_id']));
			$tradelist_arr[$k] = $v;
			$tradelist_arr[$k]['username'] = $user['username'];
			$tradelist_arr[$k]['enname'] = $user['enname'];
		}

		
		include $this->admin_tpl('apay_list');
	}
	
	
}
?>