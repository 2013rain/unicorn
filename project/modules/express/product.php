<?php

defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class product extends admin {
	
	private $db;
	
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('product_goods_model');
		
	}

	/**
	 * 商品列表
	 */
	function init() {
		$show_header = $show_scroll = true;

		
		//搜索框
		$barcode = isset($_GET['barcode']) ? $_GET['barcode'] : '';
		$jpname = isset($_GET['jpname']) ? $_GET['jpname'] : '';
		$zhname = isset($_GET['zhname']) ? $_GET['zhname'] : '';
		$where = " 1 ";
		if (!empty($barcode)) {
			$where .= " AND bar_code LIKE '".addslashes($barcode)."%' ";
		}
		if (!empty($jpname)) {
			$where .= " AND country_goods_name LIKE '".addslashes($jpname)."%' ";
		}
		if (!empty($zhname)) {
			$where .= " AND ch_name LIKE '".addslashes($zhname)."%' ";
		}
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$productlist = $this->db->listinfo($where, 'id asc', $page, 15);
		//查询会员头像
		
		$pages = $this->db->pages;
		
		include $this->admin_tpl('product_init');
	}
	
	/**
	 * edit member
	 */
	function edit() {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : '0';
		if(isset($_POST['dosubmit'])) {
				if (!isset($_POST['info']) || empty($_POST['info']) ||!is_array($_POST['info']) ) {
					showmessage("非法请求", HTTP_REFERER);
				}
				$info = $_POST['info'];
				$id = isset($info["id"])? (int)$info["id"] : 0;
				if ( ($id>0 && !empty($info["bar_code"])) || ($id==0 && empty($info["bar_code"])) ) {
					showmessage("非法请求", HTTP_REFERER);
				}
				if (!empty($info["country_goods_name"]) || !empty($info["uprice"])|| !empty($info["uweight"])) {
					showmessage("参数有误", HTTP_REFERER);
				}
				$goods_data = array();
				if ( $info["bar_code"]) {
					$one = $this->db->get_one(array("bar_code"=>$info["bar_code"] ));
					if (!empty($one)) {
						showmessage("重复录入条形码", HTTP_REFERER);
					}
					$goods_data["bar_code"] = $info["bar_code"];
				}

				$goods_data["status"] = (int)$info["status"];
				$goods_data["country_goods_name"] = $info["country_goods_name"];
				$goods_data["ch_name"] = $info["ch_name"];
				$goods_data["en_name"] = $info["en_name"];
				$goods_data["is_special"] = (int)$info["is_special"];
				$goods_data["pcategory"] = $info["pcategory"];
				$goods_data["scategory"] = $info["scategory"];
				$goods_data["goodsmodel"] = $info["goodsmodel"];
				$goods_data["productname"] = $info["productname"];
				$goods_data["measur_unit"] = $info["measur_unit"];
				$goods_data["uprice"] = floatval($info["uprice"]);
				$goods_data["uweight"] = (int)$info["uweight"];
				$goods_data["product_area"] = (int)$info["product_area"];

				if ($id>0) {
					$this->db->update($goods_data,array("id"=>$id));
				} else {
						$this->db->insert($goods_data);
				}

				showmessage(L('operation_success'), '?m=express&c=product&a=init', 1250, 'edit');
			
		} else {
			$info = $id>0? $this->db->get_one(array("id"=>$id )) :array();

			include $this->admin_tpl('product_edit');		
		}
	}
	
	/**
	 * delete member
	 */
	function delete() {
		$uidarr = isset($_POST['userid']) ? $_POST['userid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
		
		
		if(!empty($phpssouidarr)) {
			
				showmessage(L('operation_failure'), HTTP_REFERER);
			
		} else {
			
			showmessage(L('operation_success'), HTTP_REFERER);
			
		}
	}

	
	public function public_checkebarcode_ajax() {
		$bar_code = isset($_GET['bar_code']) && trim($_GET['bar_code']) ? trim($_GET['bar_code']) : exit(0);
		if(CHARSET != 'utf-8') {
			$bar_code = iconv('utf-8', CHARSET, $bar_code);
			$bar_code = addslashes($bar_code);
		}
		if (strlen($bar_code)>32) {
			exit('0');
		}
		$info = $this->db->get_one(array("bar_code"=>$bar_code ));
			
		if(!empty($info)) {
			exit('0');
		} else {
			exit('1');
		}
		
	}
	
	/**
	 * 检查邮箱
	 * @param string $email
	 * @return $status {-1:email已经存在 ;-5:邮箱禁止注册;1:成功}
	 */
	public function public_checkemail_ajax() {
		$email = isset($_GET['email']) && trim($_GET['email']) ? trim($_GET['email']) : exit(0);
		
		$status = $this->client->ps_checkemail($email);
		if($status == -5) {	//禁止注册
			exit('0');
		} elseif($status == -1) {	//用户名已存在，但是修改用户的时候需要判断邮箱是否是当前用户的
			if(isset($_GET['phpssouid'])) {	//修改用户传入phpssouid
				$status = $this->client->ps_get_member_info($email, 3);
				if($status) {
					$status = unserialize($status);	//接口返回序列化，进行判断
					if (isset($status['uid']) && $status['uid'] == intval($_GET['phpssouid'])) {
						exit('1');
					} else {
						exit('0');
					}
				} else {
					exit('0');
				}
			} else {
				exit('0');
			}
		} else {
			exit('1');
		}
	}

}
?>