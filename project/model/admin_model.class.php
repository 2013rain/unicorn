<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class admin_model extends model {
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'admin';
		parent::__construct();
	}
	public function get_one($where = '', $data = '*', $order = '', $group = '') {
		if (is_array($where) ) {
			$where["status"]="1";
			
		}else{
			$where .=!empty($where)? " AND  ":" ";
			$where .="status=1";
		}
		return  parent::get_one($where, $data,  $order, $group);		
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