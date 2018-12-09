<?php
defined('IN_PHPCMS') or exit('No permission resources.');

//$session_storage = 'session_'.pc_base::load_config('system','session_storage');
//pc_base::load_sys_class($session_storage);
pc_base::load_app_class('foreground','member');
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('global');

class index extends foreground {
	public $db, $goods_db;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('member_express_model');
		$this->goods_db = pc_base::load_model('member_express_goods_model');
	}

	/**
	 * defalut
	 */
	function init() {
		$userid = $this->memberinfo['userid'];
		$where = [
			'userid' => $userid,
			'status' => 0
		];
		$page = $_GET['page'] ? intval($_GET['page']) : '1';
		$info = $this->db->listinfo('', '', $page, 10);
		$pages = $this->db->pages;
		include template('express','main');
	}
	function site_notify() {}
	function pay() {}
	function in_storage() {
		$show_validator = true;
        $show_service = isset($_GET['show_service']) ? intval($_GET['show_service']) : 0;
        $userid = $this->memberinfo['userid'];
		if(isset($_POST['dosubmit'])) {
            if (isset($_POST['set_service']) && $_POST['set_service'] == 'service') {
                $service = isset($_POST['service']) ? $_POST['service'] : [];
                $service = $this->formatService($service);
                if (!$service) {
                    showmessage('服务非法操作', HTTP_REFERER);
                }
                $express_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                if (!$express_id) {
                    showmessage('非法订单', 'index.php?m=express&c=index');
                }
                $where = [
                    'id' => $express_id,
                ];
                $res = $this->db->get_one($where, 'userid,status');
                if (!$res || $res['userid'] != $userid || $res['status'] != 0) {
                    showmessage('非法操作', 'index.php?m=express&c=index');
                }
                $user_remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
                $data = [];
                $data['status'] = 1;
                $data['service'] = $service;
                if ($user_remark) {
                    $data['user_remark'] = $user_remark;
                }
                $res = $this->db->update($data, $where);
                showmessage('入库成功', 'index.php?m=express&c=index&a=detail&id='.$express_id.'&t=3');
            } else {
                $store = isset($_POST['store']) ? intval($_POST['store']) : 0;
                if (!$store) {
                    showmessage('入库仓库不能为空', HTTP_REFERER);
                }
                $company = isset($_POST['company']) ? trim($_POST['company']) : '';
                if (!$company) {
                    showmessage('快递公司不能为空', HTTP_REFERER);
                }
                $expressno = isset($_POST['expressno']) ? trim($_POST['expressno']) : '';
                if (!$expressno) {
                    showmessage('快递单号必填', HTTP_REFERER);
                }
                $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
                if (!$summary) {
                    showmessage('货物概述必填', HTTP_REFERER);
                }
                $express = isset($_POST['express']) ? $_POST['express'] : '';
                if (!is_array($express)) {
                    showmessage('非法操作', HTTP_REFERER);
                }
                $goods_sql = [];
                $flag = true;
                $time = time();
                foreach ($express as $val) {
                    $name = isset($val['name']) ? trim($val['name']) : '';
                    $category = isset($val['category']) ? trim($val['category']) : '';
                    $category_child = isset($val['category_child']) ? trim($val['category_child']) : '';
                    $brand = isset($val['brand']) ? trim($val['brand']) : '';
                    $model = isset($val['model']) ? trim($val['model']) : '';
                    $dollar = isset($val['dollar']) ? floatval($val['dollar']) : 0;
                    $num = isset($val['num']) ? intval($val['num']) : 0;
                    $goods_sql[] = [
                        'userid' => $userid,
                        'expressno' => $expressno,
                        'goodsname' => $name,
                        'pcategory' => $category,
                        'scategory' => $category_child,
                        'productname' => $brand,
                        'goodsmodel' => $model,
                        'uprice' => $dollar,
                        'num' => $num,
                        'createtime' => $time
                        ];
                }
                $express_sql = [
                    'userid' => $userid,
                    'storeid' => $store,
                    'company' => $company,
                    'expressno' => $expressno,
                    'detail' => $summary,
                    'createtime' => $time,
                    ];
                $express_insert_id = $this->db->insert($express_sql, true);
                if (!$express_insert_id) {
                    showmessage('快递单号已存在', HTTP_REFERER);
                }
                foreach ($goods_sql as $sql) {
                    $res = $this->goods_db->insert($sql);
                }
                showmessage('', 'index.php?m=express&c=index&a=in_storage&id='.$express_insert_id.'&show_service=1&t=3', 0);
            }
		} else {
            if ($show_service == 1) {
                $express_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                if (!$express_id) {
                    showmessage('非法订单', 'index.php?m=express&c=index');
                }
                $where = [
                    'id' => $express_id,
                ];
                $res = $this->db->get_one($where, 'userid,status');
                if (!$res || $res['userid'] != $userid || $res['status'] != 0) {
                    showmessage('非法操作', 'index.php?m=express&c=index');
                }
                $service_info = pc_base::load_config('express_service');
                include template('express', 'service');
            } else {
                include template('express','putin');
            }
		}
	}

    function formatService($service) {
        if (!$service) {
            return '00000000';
        }
        $origin = '00000000';
        $infos = pc_base::load_config('express_service');
        $can_not_merge_service = [];
        $can_merge_service = [];
        foreach ($infos as $val) {
            if ($val['can_merge'] == '1') {
                $can_merge_service[] = $val['value'];
            } else {
                $can_not_merge_service[] = $val['value'];
            }
        }
        $correct = true;
        $has_not_merge = false;
        foreach ($service as $val) {
            if (!in_array($val, $can_merge_service) && !in_array($val, $can_not_merge_service)) {
                $correct = false;
                break;
            } else {
                $index = intval($val) - 1;
                $origin[$index] = '1';
            }
            if (in_array($val, $can_not_merge_service)) {
                $has_not_merge = true;
            }
        }
        if (!$correct) {
            return false;
        }
        if ($has_not_merge && count($service) != 1) {
            return false;
        }
        return $origin;
    }

    function detail() {
        $userid = $this->memberinfo['userid'];
        $express_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$express_id) {
            showmessage('非法订单', 'index.php?m=express&c=index');
        }
        $where = [
            'id' => $express_id,
        ];
        $res = $this->db->get_one($where);
        if (!$res || $res['userid'] != $userid) {
            showmessage('非法操作', 'index.php?m=express&c=index');
        }
        $expressno = $res['expressno'];
        $where = [
            'userid' => $userid,
            'expressno' => $expressno
        ];
        $goods = $this->goods_db->listinfo($where, '', 1, 1);
        include template('express', 'detail');
    }


	
	
}
?>
