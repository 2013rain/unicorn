<?php
/**
 * 管理员后台会员组操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class member_address extends admin {
	
	private $db;
	
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('member_address_model');
	}

	/**
	 * 首页
	 */
	function init() {
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$member_address_list = $this->db->listinfo('auditstatus=0', '', $page, 15);
		$this->member_db = pc_base::load_model('member_model');

		foreach ($member_address_list as $k=>$v) {
			$memberinfo = $this->member_db->get_one(array('userid'=>$v['member_id']));
			$member_address_list[$k]['username'] = $memberinfo["username"];
			$member_address_list[$k]['enname'] = $memberinfo["enname"];
		}
		$pages = $this->db->pages;

		
		include $this->admin_tpl('member_address_init');

	}
	
	
	function audit_access() {		
		if(isset($_POST['addressid'])) {
			$audit =1;
			foreach($_POST['addressid'] as $k=>$v) {
				$this->db->update(array('auditstatus'=>$audit), array('id'=>(int)$v));
			}

			showmessage(L('operation_success'), HTTP_REFERER);
		} 
		showmessage(L('operation_failure'), HTTP_REFERER);
	}
	function audit_refuse() {		
		if(isset($_POST['addressid'])) {
			$audit =-1;
			foreach($_POST['addressid'] as $k=>$v) {
				$this->db->update(array('auditstatus'=>$audit), array('id'=>(int)$v));
			}
			
			showmessage(L('operation_success'), HTTP_REFERER);
		}
		showmessage(L('operation_failure'), HTTP_REFERER);
	}

}
?>