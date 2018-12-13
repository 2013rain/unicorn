<?php
defined('IN_PHPCMS') or exit('No permission resources.');

//$session_storage = 'session_'.pc_base::load_config('system','session_storage');
//pc_base::load_sys_class($session_storage);
pc_base::load_app_class('foreground','member');
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('global');

class index extends foreground {
	public $db, $goods_db, $category_db, $goods_attr_db;
    public static $store;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('member_express_model');
		$this->goods_db = pc_base::load_model('member_express_goods_model');
        $this->category_db = pc_base::load_model('goods_category_model');
        $this->goods_attr_db = pc_base::load_model('goods_category_attr_model');
        if (!self::$store) {
            $info = pc_base::load_config('express_store');
            foreach ($info as $val) {
                self::$store[$val['id']] = $val;
            }
        }
	}

	/**
	 * defalut
	 */
	function init() {
		$userid = $this->memberinfo['userid'];
        $where = "userid=$userid and status in (0,1,2,3)";
        $list_wait = [];
        $list_in_store = [];
        $list_out_store = [];
		$lists = $this->db->select($where, 'id,storeid,expressno,detail,createtime,status,in_store_time,out_store_time');
        foreach ($lists as $val) {
            if ($val['status'] == 0 || $val['status'] == 1) {
                $list_wait[] = $val;
            } elseif ($val['status']) {
                $list_in_store[] = $val;
            } else {
                $list_out_store[] = $val;
            }
        }
		include template('express','main');
	}
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
                $company = isset($_POST['company']) ? trim($_POST['company']) : '';
                $check = $this->checkStoreCompany($store, $company);
                if (!$check['suc']) {
                    showmessage($check['msg'], HTTP_REFERER);
                }
                $company = $check['data']['company_name'];
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
                $flag = true;
                foreach ($express as $val) {
                    $name = isset($val['name']) ? trim($val['name']) : '';
                    if (!$name) {
                        $flag = false;
                    }
                    $category = isset($val['category']) ? trim($val['category']) : '';
                    if (!$category) {
                        $flag = false;
                    }
                    $category_child = isset($val['category_child']) ? trim($val['category_child']) : '';
                    if (!$category_child) {
                        $flag = false;
                    }
                    $brand = isset($val['brand']) ? trim($val['brand']) : '';
                    if (!$brand) {
                        $flag = false;
                    }
                    $model = isset($val['model']) ? trim($val['model']) : '';
                    if (!$model) {
                        $flag = false;
                    }
                    $dollar = isset($val['dollar']) ? floatval($val['dollar']) : 0;
                    if ($dollar <= 0) {
                        $flag = false;
                    }
                    $num = isset($val['num']) ? intval($val['num']) : 0;
                    if ($num <= 0) {
                        $flag = false;
                    }
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
                if (!$flag) {
                    showmessage('物品栏不能留空', HTTP_REFERER);
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
                $stores = pc_base::load_config('express_store');
                $cat_where = [
                    'fid' => 0,
                    'status' => 1,
                ];
                $cats = $this->category_db->select($cat_where);
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
        $express = $this->db->get_one($where);
        if (!$express || $express['userid'] != $userid) {
            showmessage('非法操作', 'index.php?m=express&c=index');
        }
        $expressno = $express['expressno'];
        $where = [
            'userid' => $userid,
            'expressno' => $expressno
        ];
        $page = isset($_GET['page']) && trim($_GET['page']) ? intval($_GET['page']) : 1;
        //$goods = $this->goods_db->listinfo($where, '', $page, 1);
        $goods = $this->goods_db->select($where);
        $goods_num = 0;
        foreach ($goods as $val) {
            $goods_num += $val['num'];
        }
        $service = $express['service'];
        if ($service[0] == '1') {
            $express_mode = '货到即发';
        } else {
            $express_mode = '普通入库';
        }
        $service = $express['service'];
        $service_arr = $this->decodeService($service);
        $pages = $this->goods_db->pages;
        include template('express', 'detail');
    }
    
    /**
     * 反解service
     */
    private function decodeService($serviceStr) {
        $res = [];
        $len = strlen($serviceStr);
        $services = pc_base::load_config('express_service');
        for ($i = 0; $i < $len; $i++) {
            if ($serviceStr[$i] == '1' && isset($services[$i])) {
                $res[] = $services[$i];
            }
        }
        return $res;

    }

    function getcompany() {
        $storeid = isset($_GET['storeid']) ? intval($_GET['storeid']) : 0;
        if (!$storeid) {
            $this->outRes(0,'请输入仓库Id');
        }
        $info = pc_base::load_config('express_store');
        $res = [];
        foreach ($info as $val) {
            if ($val['id'] == $storeid) {
                $res = $val['company'];
                break;
            }
        }
        $this->outRes(1, '', $res);
    }

    private function checkStoreCompany($storeid, $companyid) {
        if (!$storeid) {
            return $this->outRes(0, '仓库不能为空', null, 'array');
        }
        if (!$companyid) {
            return $this->outRes(0, '快递公司不能为空', null, 'array');
        }
        
        $info = pc_base::load_config('express_store');
        $flag = true;
        $res = [];
        $company_name = '';
        foreach ($info as $val) {
            if ($val['id'] == $storeid) {
                foreach ($val['company'] as $v) {
                    if ($v['id'] == $companyid) {
                        $company_name = $v['name'];
                        break;
                    }
                }
                break;
            }
        }
        if (!$company_name) {
            return $this->outRes(0, '非法操作', null, 'array');
        }
        return $this->outRes(1, '', ['company_name'=>$company_name], 'array');
    }

    function getcatinfo()
    {
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
        if ($type != "cat" && $type != "attr") {
            $this->outRes(0, '类别不对');
        }
        if (!$cat_id) {
            $this->outRes(0, '分类Id不能为空');
        }
        if ($type == "cat") {
            $where = [
                'fid' => $cat_id,
                'status' => 1,
                ];
            $res = $this->category_db->select($where, 'id,cate_name,intro_model,intro_num');
            $this->outRes(1, '', $res);
        } else {
            $where = [
                'cate_id' => $cat_id,
                ];
            $res = $this->goods_attr_db->select($where, 'id,attr_name');
            $this->outRes(1, '', $res);
        }
    }

    function all_express() {
        $userid = $this->memberinfo['userid'];
        $page = $_GET['page'] ? intval($_GET['page']) : '1';
        $expressno = isset($_GET['expressno']) ? $_GET['expressno'] : '';
        $where = [];
        $where['userid'] = $userid;
        if ($expressno) {
            $where['expressno'] = $expressno;
        }
        if (isset($_GET['clear'])) {
            unset($where['expressno']);
            $expressno = '';
        }
        $store = self::$store;
        $list = $this->db->listinfo($where, '', $page, 10, '', 10, '', [], 'id,storeid,company,weight,expressno,detail,createtime,status');
        $pages = $this->db->pages;
        include template('express','all_express');
    }

    function delete() {
        $userid = $this->memberinfo['userid'];
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            showmessage('非法操作', HTTP_REFERER);
        }
        $where = [
            'id' => $id
        ];
        $res = $this->db->get_one($where, 'userid,status');
        if (!$res) {
            showmessage('查无此单', HTTP_REFERER);
        }
        if ($res['userid'] != $userid || !in_array($res['status'], [0,1])) {
            showmessage('非法操作', HTTP_REFERER);
        }
        //$res = $this->db->delete($where);
        showmessage('删除成功', HTTP_REFERER);
    }




	
	
}
?>
