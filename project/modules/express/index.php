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
    private static $service;
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
        if (!self::$service) {
            $info = pc_base::load_config('express_service');
            foreach ($info as $val) {
                self::$service[$val['value']] = $val;
            }
        }
	}

	/**
	 * defalut
	 */
	function init() {
        $SEO = seo(1);
		$userid = $this->memberinfo['userid'];
        $where = "userid=$userid and status in (0,1,2,3,4,5,6)";
        $list_wait = [];
        $list_in_store = [];
        $list_out_store = [];
		$lists = $this->db->select($where, 'id,storeid,expressno,detail,createtime,status,in_store_time,out_store_time,weight,pay_status,pay_money');
        $store = self::$store;
        foreach ($lists as $val) {
            if ($val['status'] == 0 || $val['status'] == 1) {
                $list_wait[] = $val;
            } elseif ($val['status'] == 2) {
                $list_in_store[] = $val;
            } else {
                $list_out_store[] = $val;
            }
        }
		include template('express','main');
	}
	function in_storage() {
        $SEO = seo(1);
		$show_validator = true;
        $show_service = isset($_GET['show_service']) ? intval($_GET['show_service']) : 0;
        $userid = $this->memberinfo['userid'];
        $admin_userid = (int)$this->memberinfo['admin_userid'];
   
		if(isset($_POST['dosubmit'])) {
			$service = isset($_POST['service']) ? $_POST['service'] : [];
			
			$service = $this->formatService($service);
			if (!$service) {
				showmessage('服务非法操作', HTTP_REFERER);
			}
			$user_remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
			if (mb_strlen($user_remark, "utf-8") > 100) {
				showmessage('备注需控制在100字以内', 'index.php?m=express&c=index');
			}
			
			$store = isset($_POST['store']) ? intval($_POST['store']) : 0;
			$company = isset($_POST['company']) ? trim($_POST['company']) : '';
			//暂时注释，数据源头找一下ldm
			$check = $this->checkStoreCompany($store, $company);
			if (!$check['suc']) {
				showmessage($check['msg'], HTTP_REFERER);
			}
			$company = $check['data']['company_name'];
			$expressno = isset($_POST['expressno']) ? trim($_POST['expressno']) : '';
			if (!$expressno) {
				showmessage('快递单号必填', HTTP_REFERER);
			}
			if (strlen($expressno) > 50) {
				showmessage('快递单号限长50以内', HTTP_REFERER);
			}
			$row = $this->db->get_one(array('expressno'=>$expressno));
			if (!empty($row)) {
				showmessage('快递单号已被使用', HTTP_REFERER);
			}

			$summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
			if (!$summary) {
				showmessage('货物概述必填', HTTP_REFERER);
			}
			if (mb_strlen($summary, "utf-8") > 100) {
				showmessage('货物概述不能超过100字', HTTP_REFERER);
			}
			// $express = isset($_POST['express']) ? $_POST['express'] : '';
			//使用新界面，前端不太好弄

			$express = isset($_POST['inbound']) ? $_POST['inbound'] : '';
			if (!is_array($express)) {

				showmessage('非法操作', HTTP_REFERER);
			}
			$express=$express['inbound_items_attributes'];

			$goods_sql = [];
			$flag = true;
			$time = time();
			if (count($express) > 100) {
				showmessage('物品添加不能超过100', HTTP_REFERER);
			}
			$this->goods_category_model = pc_base::load_model('goods_category_model');
			foreach ($express as $val) {
				$lu_category_id = isset($val['lu_category_id']) ? intval($val['lu_category_id']) : '0';
				if ($lu_category_id>0) {
					$clild_cat = $this->goods_category_model->get_one(array('id'=>$lu_category_id));
					$cate_cat = $this->goods_category_model->get_one(array('id'=>$clild_cat['fid']));
					$val['category']=$cate_cat['cate_name'];
					$val['category_child']=$clild_cat['cate_name'];
				}

				$name = isset($val['name']) ? trim($val['name']) : '';
				if (!$name || mb_strlen($name, "utf-8") > 20) {
					$flag = false;
				}
				$category = isset($val['category']) ? trim($val['category']) : '';
				if (!$category || mb_strlen($category, "utf-8") > 20) {
					$flag = false;
				}
				$category_child = isset($val['category_child']) ? trim($val['category_child']) : '';
				if (!$category_child || mb_strlen($category_child, "utf-8") > 20) {
					$flag = false;
				}
				$brand = isset($val['brand']) ? trim($val['brand']) : '';
				if (!$brand || mb_strlen($brand, "utf-8") > 20) {
					$flag = false;
				}
				$model = isset($val['model']) ? trim($val['model']) : '';
				if (!$model || mb_strlen($model ,"utf-8") > 20) {
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
			if (!$goods_sql) {
				showmessage('物品不能为空', HTTP_REFERER);
			}

			$express_sql = [
				'userid' => $userid,
				'storeid' => $store,
				'company' => $company,
				'expressno' => $expressno,
				'detail' => $summary,
				'createtime' => $time,
				'status' => 1,
				'service' => $service,
			];
            if ($admin_userid > 0) {
                $this->admin_model = pc_base::load_model('admin_model');
                $rebate_one = $this->admin_model->get_one(array('userid'=>$admin_userid,'roleid'=>2), 'rebate');
                if (isset($rebate_one['rebate']) && $rebate_one['rebate']>0 && $rebate_one['rebate']<100) {
                   $express_sql['rebate']=$rebate_one['rebate'];
                }
            }

			if ($user_remark) {
				$express_sql['user_remark'] = $user_remark;
			}
			$express_insert_id = $this->db->insert($express_sql, true);
			if (!$express_insert_id) {
				showmessage('快递单号已存在', HTTP_REFERER);
			}
			foreach ($goods_sql as $sql) {
				$res = $this->goods_db->insert($sql);
			}
			showmessage('入库成功', 'index.php?m=express&c=index&a=init');
		} else {                  
			$stores = pc_base::load_config('express_store');
			if (count($stores)>4) {
				$stores = array_slice($stores, 0,4);
			}
			$express = array();
			foreach ($stores as $key => $va) {
				$express[$va['id']]=$va['company'];

			}             
			$expresses = json_encode($express);

			//尺码
			$this->goods_category_model = pc_base::load_model('goods_category_model');
			$this->goods_category_attr_model = pc_base::load_model('goods_category_attr_model');
			$goods_category = $this->goods_category_model->select(array('fid'=>0));
			foreach($goods_category as $k=>&$val) {
				$child_category = $this->goods_category_model->select(array('fid'=>$val['id']));
				$val['name']=$val['cate_name'];
				$sub_categories=array();
				foreach($child_category as $k2=>$v2) {
					$child_attr = $this->goods_category_attr_model->select(array('cate_id'=>$v2['id']));
					$v2['name']=$v2['cate_name'];
					$attr_name = array_column($child_attr, 'attr_name');
					$model_selection_string = "型号||". implode(",,", $attr_name);
					if (count($attr_name)==0) {
						$model_selection_string="";
					}

					$v2['force_insurance_amount']='0.0';
					$v2['require_brand']=false;
					$v2['require_model']=false;
					$v2['model_selection_string']=$model_selection_string;
					$sub_categories[]=$v2;
				}
				$val['sub_categories']=$sub_categories;
			}
			unset($val);
			$goods_category = json_encode($goods_category);
			$cat_where = [
				'fid' => 0,
				'status' => 1,
			];
			$cats = $this->category_db->select($cat_where);
			$service_info = self::$service;
			include template('express','putin');         
		}
	}

    function formatService($service) {
        if (!$service) {
            return '00000000';
        }
        $origin = '00000000';
        $infos = self::$service;
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
        $SEO = seo(1);
        $userid = $this->memberinfo['userid'];
        $express_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$express_id) {
            showmessage('非法订单', 'index.php?m=express&c=index');
        }
        $store = self::$store;
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
        $this->product_model = pc_base::load_model('product_goods_model');
        $goods_num = 0;
        foreach ($goods as $k=> $val) {
            $country_goods_name = '';
            if (!empty( $val['bar_code'])) {
                 $product_info = $this->product_model->get_one(array('bar_code'=>$val['bar_code']));
                 $country_goods_name = $product_info['country_goods_name'];
            }
            $goods[$k]['country_goods_name'] = $country_goods_name;
           
            $goods_num += $val['num'];
        }
        $service = $express['service'];
        if ($service[0] == '1') {
            $express_mode = '货到即发';
        } else {
            $express_mode = '普通入库';
        }

        $time_node = [];
        $time_node[] = [
            'name' => '快递单创建',
            'value' => 1,
            'time' => $express['createtime']
            ];
        if ($express['status'] >=2) {
            $time_node[] = [
                'name' => '快递入库',
                'value' => 2,
                'time' => $express['in_store_time']
            ];
            if ($express['pay_status'] == 1) {
                $time_node[] = [
                    'name' => '支付运费',
                    'value' => 2,
                    'time' => $express['pay_time']
                ];
            }
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
        for ($i = 0; $i < $len; $i++) {
            $index = $i + 1;
            if ($serviceStr[$i] == '1' && isset(self::$service[$index])) {
                $res[] = self::$service[$index];
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
        $SEO = seo(1);
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
    function all_out_express() {
        $SEO = seo(1);
        $userid = $this->memberinfo['userid'];
        $page = $_GET['page'] ? intval($_GET['page']) : '1';
        $expressno = isset($_GET['expressno']) ? $_GET['expressno'] : '';
        $where = "`userid`=$userid";
        if (!isset($_GET['clear']) && $expressno) {
            $where .= " and `expressno`=$expressno";
        } else {
            $where .= " and `status` in (3,4,5,6,7)";
        }
        $store = self::$store;
        $list = $this->db->listinfo($where, '', $page, 10, '', 10, '', [], 'id,company,storeid,expressno,weight,detail,out_store_time,status');
        $pages = $this->db->pages;
        include template('express','all_out_express');
    }

    function delete() {
        $SEO = seo(1);
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
        $res = $this->db->delete($where);
        showmessage('删除成功', HTTP_REFERER);
    }

    function edit() {
		exit;
        $userid = $this->memberinfo['userid'];
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            showmessage('非法操作', HTTP_REFERER);
        }
        $where = [
            'id' => $id
        ];
        $info = $this->db->get_one($where, 'id,userid,storeid,expressno,company,detail,status');
        if (!$info) {
            showmessage('非法操作', HTTP_REFERER);
        }
        if ($info['userid'] != $userid || $info['status'] != 0) {
            showmessage('非法操作', HTTP_REFERER);
        }
		if(isset($_POST['dosubmit'])) {
            if (isset($_POST['set_service']) && $_POST['set_service'] == 'service') {
                $service = isset($_POST['service']) ? $_POST['service'] : [];
                $service = $this->formatService($service);
                if (!$service) {
                    showmessage('非法操作', 'index.php?m=express&c=index&a=init');
                }
                $where = [
                    'id' => $id,
                ];
                $user_remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
                $data = [];
                $data['status'] = 1;
                $data['service'] = $service;
                if ($user_remark) {
                    $data['user_remark'] = $user_remark;
                }
                $res = $this->db->update($data, $where);
                showmessage('入库成功', 'index.php?m=express&c=index&a=detail&id='.$id.'&t=3');
            } else {
                $expressno = isset($_POST['expressno']) ? trim($_POST['expressno']) : '';
                if (!$expressno) {
                    showmessage('快递单号必填', HTTP_REFERER);
                }
                $company = isset($_POST['company']) ? intval($_POST['company']) : 0;
                $check = $this->checkStoreCompany($info['storeid'], $company);
                if (!$check['suc']) {
                    showmessage($check['msg'], HTTP_REFERER);
                }
                $company = $check['data']['company_name'];
                $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
                if (!$summary) {
                    showmessage('货物概述必填', HTTP_REFERER);
                }
                $del_goods = isset($_POST['del_goods']) ? $_POST['del_goods'] : [];
                if (!is_array($del_goods)) {
                    showmessage('非法操作', HTTP_REFERER);
                }
                $del_goods_id = [];
                $del_flag = true;
                foreach ($del_goods as $val) {
                    $goodid = intval($val);
                    if (!$goodid) {
                        $del_flag = false;
                        break;
                    } else {
                        $del_goods_id[] = $goodid;
                    }
                }
                if (!$del_flag) {
                    showmessage('非法操作', HTTP_REFERER);
                }
                $express_data = [];
                if ($expressno != $info['expressno']) {
                    $express_data['expressno'] = $expressno;
                }
                if ($company != $info['company']) {
                    $express_data['company'] = $company;
                }
                if ($summary != $info['detail']) {
                    $express_data['detail'] = $summary;
                }
                // $express = isset($_POST['express']) ? $_POST['express'] : '';

                $express = isset($_POST['inbound']) ? $_POST['inbound'] : array();
                if (!is_array($express)) {

                    showmessage('非法操作1', HTTP_REFERER);
                }
                $express=$express['inbound_items_attributes'];

                $goods_sql = [];
				$this->goods_category_model = pc_base::load_model('goods_category_model');
                if (is_array($express)) {
                    $flag = true;
                    $time = time();
                    foreach ($express as $val) {
                        
                        $lu_category_id = isset($val['lu_category_id']) ? intval($val['lu_category_id']) : '0';
                        if ($lu_category_id>0) {
                            $clild_cat = $this->goods_category_model->get_one(array('id'=>$lu_category_id));
                            $cate_cat = $this->goods_category_model->get_one(array('id'=>$clild_cat['fid']));
                            $val['category']=$cate_cat['cate_name'];
                            $val['category_child']=$clild_cat['cate_name'];
                        }

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
                }
                $check_del_flag = true;
                $remain_goods_ids = [];
                if ($del_goods_id || isset($express_data['expressno'])) {
                    $where = [
                        'userid' => $userid,
                        'expressno' => $info['expressno'],
                        ];
                    $goods = $this->goods_db->select($where,'id');
                    $goods_ids = [];
                    foreach ($goods as $val) {
                        $goods_ids[] = $val['id'];
                    }
                    $remain_goods_ids = $goods_ids;
                    if ($del_goods_id) {
                        $diff = array_diff($del_goods_id, $goods_ids);
                        $remain_goods_ids = array_diff($goods_ids, $del_goods_id);
                        if ($diff) {
                            $check_del_flag = false;
                        }
                    }
                }
                if (!$check_del_flag) {
                    showmessage('非法操作2', HTTP_REFERER);
                }
                if ($del_goods_id && empty($remain_goods_ids) && empty($goods_sql)) {
                    showmessage('物品不能为空', HTTP_REFERER);
                }
                if ($express_data) {
                    $express_data['updatetime'] = time();
                    $res = $this->db->update($express_data,['id'=>$id]);
                    if (!$res) {
                        showmessage('快递单号被占用', HTTP_REFERER);
                    }
                }
                if ($del_goods_id) {
                    $ids = implode(',', $del_goods_id);
                    $where = "id in ($ids)";
                    $res = $this->goods_db->delete($where);
                }
                if (isset($express_data['expressno']) && $remain_goods_ids) {
                    $ids = implode(',', $remain_goods_ids);
                    $where = "id in ($ids)";
                    $data = [
                        'expressno' => $express_data['expressno']
                        ];
                    $res = $this->goods_db->update($data,$where);
                }
                foreach ($goods_sql as $sql) {
                    $res = $this->goods_db->insert($sql);
                }
                showmessage('保存成功', 'index.php?m=express&c=index&a=edit&id='.$id.'&show_service=1');
            }
        } else {
            $show_service = isset($_GET['show_service']) ? intval($_GET['show_service']) : 0;
            if ($show_service == 1) {
                $service_info = self::$service;
                include template('express', 'edit_service');
            } else {
                $store = self::$store[$info['storeid']];
                $where = [
                    'userid' => $userid,
                    'expressno' => $info['expressno'],
                    ];
                $goods = $this->goods_db->select($where);

                $stores = pc_base::load_config('express_store');
                if (count($stores)>4) {
                    $stores = array_slice($stores, 0,4);
                }
                $express = array();
                foreach ($stores as $key => $va) {
                    $express[$va['id']]=$va['company'];

                }
              
                $expresses = json_encode($express);
                //尺码
                $this->goods_category_model = pc_base::load_model('goods_category_model');
                $this->goods_category_attr_model = pc_base::load_model('goods_category_attr_model');
                $categorys = $this->goods_category_model->select(array('fid'=>0));
                

                foreach($categorys as $k=>&$val) {
                    $child_category = $this->goods_category_model->select(array('fid'=>$val['id']));
                    $val['name']=$val['cate_name'];
                    $sub_categories=array();
                    foreach($child_category as $k2=>$v2) {
                        $child_attr = $this->goods_category_attr_model->select(array('cate_id'=>$v2['id']));
                        $v2['name']=$v2['cate_name'];
                        $attr_name = array_column($child_attr, 'attr_name');
                        $model_selection_string = "型号||". implode(",,", $attr_name);
                        if (count($attr_name)==0) {
                            $model_selection_string="";
                        }

                        $v2['force_insurance_amount']='0.0';
                        $v2['require_brand']=false;
                        $v2['require_model']=false;
                        $v2['model_selection_string']=$model_selection_string;
                        $sub_categories[]=$v2;
                    }
                    $val['sub_categories']=$sub_categories;
                }
                unset($val);
                
                $goods_category = json_encode($categorys);
                foreach ($goods as $key => $v) {
                    $one_cate = $this->goods_category_model->get_one(array('cate_name'=>$v['scategory']));
                    $goods[$ke]['lu_category_id']=@$one_cate['id'];
                }

                include template('express','edit');
            }
        }

    }




	public  function pay()
    {
        $SEO = seo(1);
        $memberinfo = $this->memberinfo;
        $id = isset($_GET['id']) ? (int)$_GET['id']:0;

        if($id<0) {
            showmessage('访问不合法');
        }
        $info = $this->db->get_one(array('userid'=>$memberinfo['userid'],'id'=>(int)$id));
        if (empty($info)) {
            showmessage('访问不合法');
        }
        if ($info['status']!=2 ) {
            showmessage('运单状态不支持支付','index.php?m=express&c=index&a=init');
        }
        if ($info['pay_status']==1 ) {
            showmessage('运单已支付','index.php?m=express&c=index&a=init');
        }
        
        $base_price = weightcost($info['weight'], 0);

        $service = $info['service'];
        $service_arr = $this->decodeService($service);
        $rebate = $info['rebate']>0&& $info['rebate']<100? $info['rebate']/10 :0;

        $this->member_address_model = pc_base::load_model('member_address_model');
        $address_list = $this->member_address_model->select(array('auditstatus' =>'1','member_id'=>$memberinfo['userid']));

        include template('express','pay');

    }

    function out_detail() {
        $SEO = seo(1);
        $userid = $this->memberinfo['userid'];
        $expressno = isset($_GET['expressno']) ? trim($_GET['expressno']) : 0;
        if (!$expressno) {
            showmessage('非法订单', 'index.php?m=express&c=index');
        }
        $store = self::$store;
        $where = [
            'expressno' => $expressno,
            'userid' =>$userid
        ];
        $express = $this->db->get_one($where);
        if (!$express || $express['userid'] != $userid || !in_array($express['status'],[3,4,5,6,7])) {
            showmessage('非法操作', 'index.php?m=express&c=index');
        }
        $express['flower_weight_cost'] = weightcost($express['weight'], 0) - weightcost(0, 0) ;
        $express['flower_weight'] = ($express['weight']-1)>0 ?($express['weight']-1)*1000 : 0;

        $expressno = $express['expressno'];
        $where = [
            'userid' => $userid,
            'expressno' => $expressno
        ];
        $goods = $this->goods_db->select($where);
        $this->product_model = pc_base::load_model('product_goods_model');
        $goods_num = 0;
        foreach ($goods as $k=> $val) {
            $country_goods_name = '';
            if (!empty( $val['bar_code'])) {
                 $product_info = $this->product_model->get_one(array('bar_code'=>$val['bar_code']));
                 $country_goods_name = $product_info['country_goods_name'];
            }
            $goods[$k]['country_goods_name'] = $country_goods_name;
            $goods_num += $val['num'];
        }
        $service = $express['service'];
        if ($service[0] == '1') {
            $express_mode = '货到即发';
        } else {
            $express_mode = '普通入库';
        }
        $hold_days = format::holdday($express['in_store_time'],$express['out_store_time']);
        $vip = isset($express['pay_vip']) ? $express['pay_vip'] : 0;
        $service = $express['service'];
        $service_arr = $this->decodeService($service);
        $time_node = [];
        $time_node[] = [
            'name' => '快递单创建',
            'value' => 1,
            'time' => $express['createtime']
        ];
        $time_node[] = [
            'name' => '快递入库',
            'value' => 2,
            'time' => $express['in_store_time']
        ];
        $time_node[] = [
            'name' => '支付运费',
            'value' => 2,
            'time' => isset($express['pay_time']) ? $express['pay_time'] : 0,
        ];
        if ($express['status'] >= 3) {
            $time_node[] = [
                'name' => '仓库出库',
                'value' => 3,
                'time' => $express['out_store_time'],
            ];
        }
        if ($express['status'] >= 4) {
            $time_node[] = [
                'name' => '快递登机',
                'value' => 4,
                'time' => $express['plane_time'],
            ];
        }
        $show_send_no = false;
        if ($express['status'] >= 5) {
            $time_node[] = [
                'name' => '快递清关',
                'value' => 5,
                'time' => $express['clearance_time'],
            ];
            $show_send_no = true;
        }
        if ($express['status'] >= 6) {
            $time_node[] = [
                'name' => '快递派送',
                'value' => 6,
                'time' => $express['distribute_time'],
            ];
            $show_send_no = true;
        }
        if ($express['status'] == 7) {
            $time_node[] = [
                'name' => '订单完成',
                'value' => 7,
                'time' => $express['complete_time'],
            ];
            $show_send_no = true;
        }

        $addr_id = $express['address_id'];
        $addr_info = json_decode($express['address_info'],true);
        
        include template('express', 'out_detail');
    }

    function confirm() {
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
        $set_data = [
            'status' => 7
        ];
        $this->db->update($set_data, $where);
        showmessage('收货成功', 'index.php?m=express&c=index');
    }

    /**
     * 检查快递单是否存在
     * 0 已存在 1:成果
     */
    function public_checkexpressno_ajax() {
        $expressno = isset($_GET['expressno']) ? trim($_GET['expressno']) : exit('0');
        $userid = $this->memberinfo['userid'];
        $row = $this->db->get_one(array('expressno'=>$expressno));
        if (!$row) {
            exit('1');
        }
        exit('0');
    }
	
    function fastinstore()
    {
        $SEO = seo(1);
        $show_validator = true;
        $show_service = isset($_GET['show_service']) ? intval($_GET['show_service']) : 0;
        $userid = $this->memberinfo['userid'];
        $admin_userid = (int)$this->memberinfo['admin_userid'];
            if(isset($_POST['dosubmit'])) {
            $service = isset($_POST['service']) ? $_POST['service'] : [];
            
            $service = $this->formatService($service);
            if (!$service) {
                showmessage('服务非法操作', HTTP_REFERER);
            }
            $user_remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
            if (mb_strlen($user_remark, "utf-8") > 100) {
                showmessage('备注需控制在100字以内', 'index.php?m=express&c=index');
            }
            
            $store = isset($_POST['store']) ? intval($_POST['store']) : 0;
            $company = isset($_POST['company']) ? trim($_POST['company']) : '';
            //暂时注释，数据源头找一下ldm
            $check = $this->checkStoreCompany($store, $company);
            if (!$check['suc']) {
                showmessage($check['msg'], HTTP_REFERER);
            }
            $company = $check['data']['company_name'];
            $expressno = isset($_POST['expressno']) ? trim($_POST['expressno']) : '';
            if (!$expressno) {
                showmessage('快递单号必填', HTTP_REFERER);
            }
            if (strlen($expressno) > 50) {
                showmessage('快递单号限长50以内', HTTP_REFERER);
            }
            $row = $this->db->get_one(array('expressno'=>$expressno));
            if (!empty($row)) {
                showmessage('快递单号已被使用', HTTP_REFERER);
            }

            $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
            if (!$summary) {
                showmessage('货物概述必填', HTTP_REFERER);
            }
            if (mb_strlen($summary, "utf-8") > 100) {
                showmessage('货物概述不能超过100字', HTTP_REFERER);
            }
            // $express = isset($_POST['express']) ? $_POST['express'] : '';
            //使用新界面，前端不太好弄

            $express = isset($_POST['inbound']) ? $_POST['inbound'] : '';
            if (!is_array($express)) {

                showmessage('非法操作', HTTP_REFERER);
            }
            $express=$express['inbound_items_attributes'];

            $goods_sql = [];
            $flag = true;
            $time = time();
            if (count($express) > 100) {
                showmessage('物品添加不能超过100', HTTP_REFERER);
            }
            $this->goods_model = pc_base::load_model('product_goods_model');
            foreach ($express as $val) {
                $bar_code = isset($val['bar_code']) ? trim($val['bar_code']) : '';
                
                $num = isset($val['num']) ? intval($val['num']) : 0;
                if ($num <= 0||empty($bar_code)) {
                    $flag = false;
                    break;
                }
                $goods_info = $this->goods_model->get_one(array('bar_code'=>$bar_code));
                if (empty($goods_info)) {
                    $flag = false;
                    break;
                }
                $goods_sql[] = [
                    'userid' => $userid,
                    'expressno' => $expressno,
                    'goodsname' => $goods_info["ch_name"],
                    'pcategory' => '',
                    'scategory' => '',
                    'productname' => '',
                    'goodsmodel' => $goods_info["goodsmodel"],
                    'uprice' => $goods_info["uprice"],
                    'num' => $num,
                    'createtime' => $time,
                    'bar_code'=>$bar_code,
                    ];
            }
            if (!$flag) {
                showmessage('物品栏不能留空', HTTP_REFERER);
            }
            if (!$goods_sql) {
                showmessage('物品不能为空', HTTP_REFERER);
            }

            $express_sql = [
                'userid' => $userid,
                'storeid' => $store,
                'company' => $company,
                'expressno' => $expressno,
                'detail' => $summary,
                'createtime' => $time,
                'status' => 1,
                'service' => $service,
                'is_barcode'=>1
            ];
            if ($admin_userid > 0) {
                $this->admin_model = pc_base::load_model('admin_model');
                $rebate_one = $this->admin_model->get_one(array('userid'=>$admin_userid,'roleid'=>2), 'rebate');
                if (isset($rebate_one['rebate']) && $rebate_one['rebate']>0 && $rebate_one['rebate']<100) {
                   $express_sql['rebate']=$rebate_one['rebate'];
                }
            }

            if ($user_remark) {
                $express_sql['user_remark'] = $user_remark;
            }
            $express_insert_id = $this->db->insert($express_sql, true);
            if (!$express_insert_id) {
                showmessage('快递单号已存在', HTTP_REFERER);
            }
            foreach ($goods_sql as $sql) {
                $res = $this->goods_db->insert($sql);
            }
            showmessage('入库成功', 'index.php?m=express&c=index&a=init');
        } else {                  
            $stores = pc_base::load_config('express_store');
            if (count($stores)>4) {
                $stores = array_slice($stores, 0,4);
            }
            $express = array();
            foreach ($stores as $key => $va) {
                $express[$va['id']]=$va['company'];

            }             
            $expresses = json_encode($express);

            $goods_category = json_encode(array());
            
            $service_info = self::$service;
            include template('express','fastinstore');
        }
    }

    function public_getbarcode_ajax()
    {
        $bar_code = isset($_GET['bar_code']) ? trim($_GET['bar_code']) : '';
        $return = array("code"=>1,'info'=>array(),'msg'=>'');
        if (empty($bar_code)) {
            $return['msg'] = '参数错误';
            echo json_encode($return);
            exit();
        }
        $userid = $this->memberinfo['userid'];
        $admin_userid = (int)$this->memberinfo['admin_userid'];
        $this->goods_model = pc_base::load_model('product_goods_model');
        $goods_info = $this->goods_model->get_one(array('bar_code'=>$bar_code));
        if (!empty($goods_info)) {
            $return['code']='0';
            $return['info']=$goods_info;
        } else {
             $return['msg'] = '无此商品条码'.$bar_code;
        }
        
        echo json_encode($return);
        exit();
    }

}
?>
