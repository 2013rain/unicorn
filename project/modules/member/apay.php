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
		$this->member_charge_log_model = pc_base::load_model('member_charge_log_model');
		$this->admin_model = pc_base::load_model('admin_model');

		
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

	/**
	 * 
	 */
	function charge_list() {

		$memberid = isset($_GET['memberid']) ? trim($_GET['memberid']) : '';
		$start_time = isset($_GET['start_time']) ? trim($_GET['start_time']) : '';
        $end_time = isset($_GET['end_time']) ? trim($_GET['end_time']) : '';
        
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$where = " status = 1 ";

		if($memberid !="" ) {
			$where .= " AND  member_id =".(int)$memberid;
		}
		if($start_time !="" && $end_time !="" ) {
			$where .= " AND  finish_time >='".addslashes($start_time)."' ";
			$where .= " AND  finish_time <='".addslashes($end_time)."' ";
		}
		
		$tradelist_arr = $this->member_charge_log_model->listinfo($where, 'finish_time DESC', $page, 15);
		$pages = $this->member_charge_log_model->pages;

		
		
		foreach($tradelist_arr as $k=>$v) {
			$user = $this->member_model->get_one(array('userid'=>$v['member_id']));
			$adminInfo = $this->admin_model->get_one(array('userid'=>$v['admin_id']));
			$tradelist_arr[$k] = $v;
			$tradelist_arr[$k]['username'] = $user['username'];
			$tradelist_arr[$k]['enname'] = $user['enname'];
			$tradelist_arr[$k]['adminname'] = $adminInfo['username'];
		}

		
		include $this->admin_tpl('charge_list');
	}


	/**
	 * 
	 */
	function point_list() {

		$memberid = isset($_GET['memberid']) ? trim($_GET['memberid']) : '';
		$start_time = isset($_GET['start_time']) ? trim($_GET['start_time']) : '';
        $end_time = isset($_GET['end_time']) ? trim($_GET['end_time']) : '';
        
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$where = " status = 1 ";

		if($memberid !="" ) {
			$where .= " AND  member_id =".(int)$memberid;
		}
		if($start_time !="" && $end_time !="" ) {
			$where .= " AND  finish_time >='".addslashes($start_time)."' ";
			$where .= " AND  finish_time <='".addslashes($end_time)."' ";
		}
		//供货商，单独
		if ($_SESSION['roleid'] == 2) {
			$where .= ' AND admin_id='.(int)$this->userid . "  ";
		}
		
		
		$tradelist_arr = $this->express_trade_log->listinfo($where, 'finish_time DESC', $page, 15);
		$pages = $this->express_trade_log->pages;

		$sum_arr = $this->express_trade_log->get_one($where, 'sum(point) as point');

		
		foreach($tradelist_arr as $k=>$v) {
			$user = $this->member_model->get_one(array('userid'=>$v['member_id']));
			$tradelist_arr[$k] = $v;
			$tradelist_arr[$k]['username'] = $user['username'];
			$tradelist_arr[$k]['enname'] = $user['enname'];

		}

		
		include $this->admin_tpl('point_list');
	}

	/**
	 * defalut
	 */
	function user_recharge() {
		
		$sitelistarr = getcache('sitelist', 'commons');
		$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
		$type = isset($_GET['type']) ? $_GET['type'] : '';
		foreach ($sitelistarr as $k=>$v) {
			$sitelist[$k] = $v['name'];
		}
	
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		//如果是超级管理员角色，显示所有用户，否则显示当前站点用户
		if($keyword) {
			$keyword = addslashes($keyword);
			$where = "0";
				if ($type == '1') {
					$where = "`username` = '$keyword'";
				} elseif($type == '2') {
					$where = "`userid` = '$keyword'";
				} elseif($type == '3') {
					$where = "`email` = '$keyword'";
				} elseif($type == '6') {
					$where = "`enname` like '$keyword'";
				}
				
				$memberlist_arr = $this->member_model->listinfo($where, 'userid DESC', $page, 15);
				$pages = $this->member_model->pages;
		}
		
		
		include $this->admin_tpl('user_recharge');
	}

	/**
	 * defalut
	 */
	function get_recharge_no() {
		 
		$money = isset($_POST['money']) ? $_POST['money'] : '0';
		$member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '0';
		$member_id=(int)$member_id;
		$money= floatval($money);
		$userinfo = $this->member_model->get_one(array('userid'=>$member_id));
		$pay_no = '';
		if (!empty($userinfo) && $money>0) {
			$pay_no = $this->member_charge_log_model->getOrderNo($member_id);
			$insert_data = array(
				'member_id' =>$member_id,
				'admin_id' =>$this->userid,
				'pay_no' =>$pay_no,
				'create_time' =>date('Y-m-d H:i:s'),
				'order_name' => '管理员充值',
				'amout' => $money,
			);
			$this->member_charge_log_model->insert($insert_data);

		}
		$return =array('code'=>0, 'pay_no'=> $pay_no);
		echo json_encode($return);
		
	}

	/**
	 * defalut
	 */
	function do_recharge() {
		$money = isset($_POST['money']) ? $_POST['money'] : '0';
		$member_id = isset($_POST['member_id']) ? $_POST['member_id'] : '0';
		$pay_no = isset($_POST['pay_no']) ? $_POST['pay_no'] : '';
		$member_id=(int)$member_id;
		$pay_no=trim($pay_no);
		$where = array(
				'member_id' =>$member_id,
				'admin_id' =>$this->userid,
				'pay_no' =>$pay_no,
			);
		$chargeinfo =$this->member_charge_log_model->get_one($where);
		if (!empty($chargeinfo) && $chargeinfo['status']=='0' && $chargeinfo['amout']==$money) {
			$userinfo = $this->member_model->get_one(array('userid'=>$member_id));
			$this->member_model->update( array('amount'=>$userinfo['amount']+$money), array('userid'=>$member_id));
			$this->member_charge_log_model->update( array('status'=>1,'finish_time'=>date('Y-m-d H:i:s'),'balance'=>$userinfo['amount']+$money), array('id'=>$chargeinfo['id']));

			showmessage("充值成功",'/index.php?m=member&c=apay&a=user_recharge');
		}

		showmessage("充值失败");
		
	}
}
?>