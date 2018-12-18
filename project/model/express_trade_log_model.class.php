<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class express_trade_log_model extends model {
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'express_trade_log';
		parent::__construct();
	}
	public function getOrderNo($uid = '', $express_no) {
		$info = parent::get_one(array('member_id' => 'express_no'));
		$order_no = '';
		if (empty($info)) {
			//18位数
			$order_no = (microtime(true)*10000).rand(100, 999) .(uid%10);
			$insert = array(
					'member_id'=>$uid,
					'create_date'=>date('Y-m-d'),
					'order_no'=>$order_no,
					'status'=>0,
					'express_no'=>$express_no,
					'create_time'=>date('Y-m-d H:i:s'),
					'order_name'=>'支付快递运费',
			);
			parent::insert($insert);
		}else{
			$order_no = $info['order_no'];
		}
		return 		
	}

	public function listinfo($where = '', $order = '', $page = 1, $pagesize = 20, $key='', $setpages = 10,$urlrule = '',$array = array(), $data = '*') {
		if (is_array($where) ) {
			$where["status"]="1";
			
		}else{
			$where .=!empty($where)? " AND  ":" ";
			$where .="status=1";
		}
		return parent::listinfo($where, $order , $page , $pagesize , $key, $setpages ,$urlrule,$array , $data );
		
	}

	public function select($where = '', $data = '*', $limit = '', $order = '', $group = '', $key='') {
		if (is_array($where) ) {
			$where["status"]="1";
			
		}else{
			$where .=!empty($where)? " AND  ":" ";
			$where .="status=1";
		}
		return parent::select($where, $data, $limit, $order, $group, $key);
	}

	public function delete($where) {
		$data["status"]='-1';
		return  parent::update($data, $where);
	}
}
?>