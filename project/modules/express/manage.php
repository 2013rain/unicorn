<?php

defined('IN_PHPCMS') or exit('No permission resources.');

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class manage extends admin {
	private $db, $goods_db;
    private static $store;
    private static $services;
    private static $nodes;
	function __construct() {
		parent::__construct();
		//$this->db = pc_base::load_model('member_model');
		$this->db = pc_base::load_model('member_express_model');
        $this->goods_db = pc_base::load_model('member_express_goods_model');
        if (!self::$store) {
            $info = pc_base::load_config('express_store');
            foreach ($info as $val) {
                self::$store[$val['id']] = $val;
            }
        }
        if (!self::$services) {
            self::$services = pc_base::load_config('express_service');
        }
        if (!self::$nodes) {
            $info = pc_base::load_config('express_node');
            foreach ($info as $val) {
                self::$nodes[$val['value']] = $val;
            }
        }
	}

	/**
	 * defalut
	 */
	function init() {
        $show_header = $show_scroll = true;

        //搜索框
        $expressno = isset($_GET['expressno']) ? trim($_GET['expressno']) : '';
        $major_no = isset($_GET['major_no']) ? trim($_GET['major_no']) : '';
        $express_list = array();
        if (empty($expressno)&&empty($major_no)) {
            include $this->admin_tpl('manage_init');

        } else {
            $down = isset($_GET['down']) ? intval($_GET['down']) : '';

        $where = " 1 ";
        if (!empty($expressno)) {
            $where .= " AND expressno LIKE '".addslashes($expressno)."' ";
        }
        if (!empty($major_no)) {
            $where .= " AND major_no LIKE '".addslashes($major_no)."' ";
        }

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $express_list = $this->db->listinfo($where, 'id DESC', $page, 15);

        $pages = $this->db->pages;
        $express_status = array(
            '7'=>'订单完成',
            '6'=>'快递派送',
            '5'=>'快递清关',
            '4'=>'快递登机',
            '3'=>'仓库出库',
            '2'=>'快递入库',
            '1'=>'快递单创建',
            '-1'=>'问题订单',
        );
        if ($down==1 ) {

            //按条件导出订单详情
            $head = array('主运单号','快递单号','物品名称','英文物品名称','净重(g)','毛重(g)','规格/型号','申报数量','申报总价（￥）','申报总价(日元)','申报计量单位','收件人','收件人城市','收件人地址','收件人电话','收件人身份证');
            $row = array();
            $express_list = $this->db->listinfo($where, 'id DESC');
            $this->product_model = pc_base::load_model('product_goods_model');
            foreach($express_list as $k=>$v) {
                $goods_list = $this->goods_db->listinfo(array('userid'=>$v['userid'], 'expressno'=>$v['expressno'] ));
                $address_info = json_decode($v['address_info'],true);
                foreach($goods_list as $goods) {
                    $product_info = array();
                    if (!empty($goods["bar_code"])) {
                        $product_info = $this->product_model->get_one(array('bar_code'=>$goods["bar_code"]));
                    }

                    $line = array();
                    $line[]=$v['major_no'];
                    $line[]=$v['expressno'];
                    $line[]=$goods['goodsname'];
                    $line[]= isset($product_info['en_name'])?$product_info['en_name']:'';

                    $line[]= isset($product_info['nweight'])?$product_info['nweight']:'';
                    $line[]= isset($product_info['uweight'])?$product_info['uweight']:'';

                    $line[]=isset($product_info['goodsname'])?$product_info['goodsname']:$goods['goodsname'];
                    $line[]=$goods['num'];
                    $line[]=$goods['num'] * $product_info['zhprice'];
                    $line[]=$goods['num'] * $goods['uprice'];
                    $line[]=$product_info['measur_unit'];
                    $line[]=$address_info['province'];
                    $line[]=$address_info['province'].'/'.$address_info['city'].'/'.$address_info['area'].$address_info['addressinfo'];
                    $line[]=$address_info['mobile'];
                    $line[]=$address_info['idcard'];
                    $row[]=$line;
                }
                
            }
            $filename = "detail_".date("YmdHis");
            $excel= pc_base::load_sys_class('excel','',1);
            $excel->export($head, $row, $filename);
            exit;

        } else {
            include $this->admin_tpl('manage_init');
        }
        }
        
        
	}
    //扫描入库
    function scan_in_store()
    {
        if(isset($_POST['dosubmit'])) {
            $expressno = isset($_POST['expressno']) ? trim($_POST['expressno']) : '';
            $opt = isset($_POST['opt']) ? trim($_POST['opt']) : '';
            $cause = isset($_POST['cause']) ? trim($_POST['cause']) : '';
            if (!in_array($opt, array('-1','1')) || empty($expressno)) {
                showmessage('非法操作',HTTP_REFERER);
            }
            $get_one = $this->db->get_one(array('expressno'=>$expressno));
            if (empty($get_one)|| $get_one['status'] != 1) {
                showmessage('无效快递单',HTTP_REFERER);
            }
            if ($opt=='-1') {
                if (empty($cause)) {
                    showmessage('请填写问题订单备注',HTTP_REFERER);
                }
                //直接转给问题订单
                $set_where = [
                'id' => $get_one['id']
                ];
                $set_data = [
                    'status' => -1,
                    'cause'=>$cause
                    ];
                $this->db->update($set_data, $set_where);
                showmessage('操作完成',HTTP_REFERER);
            }
            $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : '';
            $goods = isset($_POST['goods']) ? $_POST['goods'] : array();
            $product = isset($_POST['product']) ? $_POST['product'] : array();
            if ($weight<=0) {
                showmessage('无效操作',HTTP_REFERER);
            }
            $store_service = $get_one['service'];
            $service_price = $this->getServicePrice($store_service);
            $price = weightcost($weight, $get_one['rebate'] , $service_price);
            $this->product_model = pc_base::load_model('product_goods_model');
            $new_goods = array();
            $time = time();
            foreach((array)$product as $val) {
                if (!empty($val['bar_code'])) {
                        $product_info = $this->product_model->get_one(array('bar_code'=>$bar_code));
                        if (empty($product_info)) {
                            showmessage('含有不存在商品',HTTP_REFERER);
                        }
                        $new_goods[] = array(
                            'userid' => $get_one['userid'],
                            'expressno' => $get_one['expressno'],
                            'goodsname' => $product_info['goodsname'],
                            'pcategory' => '',
                            'scategory' => '',
                            'productname' => '',
                            'goodsmodel' => $product_info['goodsmodel'],
                            'uprice' => $product_info['uprice'],
                            'num' => $val['num'],
                            'bar_code' => $product_info['bar_code'],
                            'createtime' => $time
                        );

                }
            }
            $goods_list = $this->goods_db->listinfo(array('userid'=>$get_one['userid'], 'expressno'=>$get_one['expressno'] ));
            $exitGoodsNum = array();
            foreach ($goods_list as $g) {
                 $exitGoodsNum[$g['id']]=$g["num"];
            }
            foreach((array)$goods as $val) {
                if (!empty($val['id']) && isset($exitGoodsNum[$val['id']]) && $exitGoodsNum[$val['id']]!=$val['num']) {
                     $set_where = array(
                        'userid'=>$get_one['userid'],
                        'expressno'=>$get_one['expressno'],
                        'id'=>$val['id']
                    );
                    $this->goods_db->update(array('num'=>$val['num']), $set_where);
                }
               
            }
            foreach ($new_goods as $sql) {
               $this->goods_db->insert($sql);
            }
            
            $set_where = [
                'id' => $get_one['id']
                ];
            $set_data = [
                'weight' => $weight,
                'status' => 2,
                'in_store_time' => $time,
                'price' => $price
                ];
            $this->db->update($set_data, $set_where);
            showmessage('入库完成',HTTP_REFERER);

        }else{
            include $this->admin_tpl('scan_in_store');
        }
        
    }
    function public_getexpress_ajax()
    {
        $return = array('code'=>1,'info'=>array(),'msg'=>'');
        $expressno = isset($_GET['expressno']) ? trim($_GET['expressno']) : '';
        if (!empty($expressno)) {
           $express_info = $this->db->get_one(array('expressno'=>$expressno));
           if (!empty($express_info)){
                if ($express_info['status']=='1') {
                    $goods_list = $this->goods_db->listinfo(array('userid'=>$express_info['userid'], 'expressno'=>$express_info['expressno'] ));
                    $return['code']='0';
                    $express_info['goods_list']= $goods_list;
                    $return['info']=$express_info;
                } else {
                    $return['msg']='订单状态不支持入库操作';
                }
                
           } else {
            $return['msg']='不存在该订单';
           }
        } else {
            $return['msg']='参数错误';
        }
        echo json_encode($return);
        exit();
    }
    function public_getproduct_ajax()
    {
        $return = array('code'=>1,'info'=>array(),'msg'=>'');
        $bar_code = isset($_GET['bar_code']) ? trim($_GET['bar_code']) : '';
        if (!empty($bar_code)) {
            $this->product_model = pc_base::load_model('product_goods_model');
            $product_info = $this->product_model->get_one(array('bar_code'=>$bar_code));
           if (!empty($product_info)){
                $return['code']='0';
                $return['info']=$product_info;
           } else {
               $return['msg']='不存在该商品';
           }
        } else {
            $return['msg']='参数错误';
        }
        echo json_encode($return);
        exit();
    }

    function in_storage() {
        if(isset($_POST['dosubmit'])) {
        } else {
            $where = [
                'status' => 1,
            ];
            $lists = $this->db->listinfo($where, '', $page, 20);
            $pages = $this->db->pages;
            $store = self::$store;
            include $this->admin_tpl('in_storage');
        }

    }

    function batch_in_store() {
        if(isset($_POST['dosubmit'])) {
            if (isset($_POST['export_express_goods_detail'])) {
                $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
                $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';
                if (!$start_time || !$end_time) {
                    showmessage('请选择起始时间',HTTP_REFERER);
                }
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time);
                $end_time += 86399;
                if (($end_time-$start_time)>31104000) {
                    showmessage('起止时间不能超过360天',HTTP_REFERER);
                }
                $where = "`status`=1 and `createtime` between $start_time and $end_time";
                $count = $this->db->count($where);
                $pagesize = 10;
                $num = ceil($count/$pagesize);
                $filename = "待入库列表商品详情_".$_POST['start_time'] .'_' .$_POST['end_time'];
                $head = array('快递单号', '中文名称', '类别', '子类别', '英文品牌', '型号', '单价', '数量');
                $row = [];
                for ($i=0;$i<$num;$i++) {
                    $page_start = $i * $pagesize;
                    $res = $this->db->select($where, 'expressno', "$page_start, $pagesize");
                    foreach ($res as $val) {
                        $expressno = $val['expressno'];
                        $goods_lists = $this->goods_db->select("`expressno`='".$expressno."'");
                        foreach ($goods_lists as $v) {
                            $row[] = array($v['expressno'], $v['goodsname'], $v['pcategory'], $v['scategory'], $v['productname'], $v['goodsmodel'], $v['uprice'], $v['num']);
                        }
                    }
                }
                $excel= pc_base::load_sys_class('excel','',1);
                $excel->export($head, $row, $filename);
                exit;
            }
            if (isset($_POST['export'])) {
                $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
                $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';
                if (!$start_time || !$end_time) {
                    showmessage('请选择起始时间',HTTP_REFERER);
                }
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time);
                $end_time += 86399;
                if (($end_time-$start_time)>31104000) {
                    showmessage('起止时间不能超过360天',HTTP_REFERER);
                }
                $where = "`status`=1 and `createtime` between $start_time and $end_time";
                $count = $this->db->count($where);
                $pagesize = 100;
                $num = ceil($count/$pagesize);
                $filename = "待入库列表_".$_POST['start_time'] .'_' .$_POST['end_time'];
                $head = array('仓库', '快递公司', '快递单号', '创建时间', '重量');
                $row = [];
                for ($i=0;$i<$num;$i++) {
                    $page_start = $i * $pagesize;
                    $res = $this->db->select($where, 'storeid,company,expressno,createtime', "$page_start, $pagesize");
                    foreach ($res as $val) {
                        $row[] = array(self::$store[$val['storeid']]['name'], $val['company'], $val['expressno'], date('Y-m-d H:i:s', $val['createtime']));
                    }
                }
                $excel= pc_base::load_sys_class('excel','',1);
                $excel->export($head, $row, $filename);
                exit;
            }
            if (isset($_POST['import'])) {
                $filename = $_FILES['express_data']['name'];
                $explode = explode(".",$filename);
                $length = count($explode);
                $ext = $explode[$length-1];
                if (!in_array($ext, ['xls','xlsx'])) {
                    showmessage('上传格式必须为xls或者xlsx的excel文件',HTTP_REFERER);
                }

                if ($_FILES['express_data']['size'] > 3145728) {
                    showmessage('上传文件大小不能超过3M',HTTP_REFERER);
                }
                if ($_FILES['express_data']['error'] != 0) {
                    showmessage('上传失败',HTTP_REFERER);
                }
                $row = 0;
                $suc_row = 0;
                $time = time();
                if($_FILES['express_data']['tmp_name']) {
                    $excel= pc_base::load_sys_class('excel','',1);
                    $data = $excel->import($_FILES['express_data']['tmp_name'], $ext);
                    if (!$data) {
                        showmessage('没检测到数据,请检查是否excel或者是否有内容',HTTP_REFERER);
                    }
                    $row = count($data) - 1;
                    foreach ($data as $k=>$v) {
                        if ($k != 1) {
                            if (count($v) < 5) {
                                continue;
                            }
                            $store = $v['A'];
                            $company = $v['B'];
                            $expressno = trim($v['C']);
                            $create_time = trim($v['D']);
                            $weight = self::formatWeight($v['E']);
                            if (!$store || !$company || !$expressno || !$create_time || !$weight) {
                                continue;
                            }
                            $where = [
                                'expressno' => $expressno,
                            ];
                            $get_one = $this->db->get_one($where, 'id,rebate,status,service');
                            if ($get_one && $get_one['status'] == 1) {
                                $store_service = $get_one['service'];
                                $service_price = $this->getServicePrice($store_service);
                                $price = weightcost($weight, $get_one['rebate'] , $service_price);

                                $set_where = [
                                    'id' => $get_one['id']
                                    ];
                                $set_data = [
                                    'weight' => $weight,
                                    'status' => 2,
                                    'in_store_time' => $time,
                                    'price' => $price
                                    ];
                                $this->db->update($set_data, $set_where);
                                $suc_row++;
                            }
                        }
                    }
                } else {
                    showmessage('上传失败',HTTP_REFERER);
                }
                showmessage('文件中含有'.$row.'条,成功导入'.$suc_row.'条',HTTP_REFERER);
            }
        } else {
            $start_time = date("Y-m-d", strtotime("-3 month"));
            $end_time = date("Y-m-d", time());
            include $this->admin_tpl('batch_in_store');
        }
    }

    function batch_out_store() {
        if(isset($_POST['dosubmit'])) {
            if (isset($_POST['export_express_goods_detail'])) {
                $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
                $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';
                if (!$start_time || !$end_time) {
                    showmessage('请选择起始时间',HTTP_REFERER);
                }
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time);
                $end_time += 86399;
                if (($end_time-$start_time)>31104000) {
                    showmessage('起止时间不能超过360天',HTTP_REFERER);
                }
                $where = "`status`=2 and `pay_status`=1 and `createtime` between $start_time and $end_time";
                $count = $this->db->count($where);
                $pagesize = 10;
                $num = ceil($count/$pagesize);
                $filename = '待出库商品详情_' . $_POST['start_time'] .'_' .$_POST['end_time'];
                $head = array('快递单号', '中文名称', '类别', '子类别', '英文品牌', '型号', '单价', '数量');
                $row = [];
                for ($i=0;$i<$num;$i++) {
                    $page_start = $i * $limit;
                    $res = $this->db->select($where, 'expressno', "$page_start, $pagesize");
                    foreach ($res as $val) {
                        $expressno = $val['expressno'];
                        $goods_lists = $this->goods_db->select("`expressno`='".$expressno."'");
                        foreach ($goods_lists as $v) {
                            $row[] = array($v['expressno'], $v['goodsname'], $v['pcategory'], $v['scategory'], $v['productname'], $v['goodsmodel'], $v['uprice'], $v['num']);
                        }
                    }
                }
                $excel= pc_base::load_sys_class('excel','',1);
                $excel->export($head, $row, $filename);
                exit;
            }
            if (isset($_POST['export'])) {
                $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
                $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';
                if (!$start_time || !$end_time) {
                    showmessage('请选择起始时间',HTTP_REFERER);
                }
                $start_time = strtotime($start_time);
                $end_time = strtotime($end_time);
                $end_time += 86399;
                if (($end_time-$start_time)>31104000) {
                    showmessage('起止时间不能超过360天',HTTP_REFERER);
                }
                $where = "`status`=2 and `pay_status`=1 and `createtime` between $start_time and $end_time";
                $count = $this->db->count($where);
                $pagesize = 100;
                $num = ceil($count/$pagesize);
                $filename = '待出库列表_'.$_POST['start_time'] .'_' .$_POST['end_time'];
                $head = array('仓库', '快递公司', '快递单号', '入库时间', '重量', '支付金额', '折扣', '增值服务', '发货公司' , '发货单号','主运单号(批次)');
                $row = [];
                for ($i=0;$i<$num;$i++) {
                    $page_start = $i * $limit;
                    $res = $this->db->select($where, 'storeid,company,expressno,in_store_time,weight,pay_money,rebate,service', "$page_start, $pagesize");
                    foreach ($res as $val) {
                        $in_store_time = format::date($val['in_store_time'], 1);
                        $service = $this->getServiceName($val['service']);
                        $rebate = $val['rebate'] ? $val['rebate'] : '无';
                        $row[] = array(self::$store[$val['storeid']]['name'], $val['company'], $val['expressno']."\t", $in_store_time, $val['weight'], $val['pay_money'], $rebate, $service);
                    }
                }
                $excel= pc_base::load_sys_class('excel','',1);
                $excel->export($head, $row, $filename);
                exit;
            }
            if (isset($_POST['import'])) {
                $filename = $_FILES['express_data']['name'];
                $explode = explode(".",$filename);
                $length = count($explode);
                $ext = $explode[$length-1];
                if (!in_array($ext, ['xls','xlsx'])) {
                    showmessage('上传格式必须为xls或者xlsx的excel文件',HTTP_REFERER);
                }
                if ($_FILES['express_data']['size'] > 5242880) {
                    showmessage('上传文件大小不能超过5M',HTTP_REFERER);
                }
                if ($_FILES['express_data']['error'] != 0) {
                    showmessage('上传失败',HTTP_REFERER);
                }
                $row = 0;
                $suc_row = 0;
                $time = time();
                if($_FILES['express_data']['tmp_name']) {
                    $excel= pc_base::load_sys_class('excel','',1);
                    $data = $excel->import($_FILES['express_data']['tmp_name'], $ext);
                    if (!$data) {
                        showmessage('没检测到数据,请检查是否excel或者是否有内容',HTTP_REFERER);
                    }
                    $row = count($data) - 1;
                    foreach ($data as $k=>$v) {
                        if ($k != 1) {
                            if (count($v) != 11) {
                                continue;
                            }
                            $store = $v['A'];
                            $company = $v['B'];
                            $expressno = trim($v['C']);
                            $in_store_time = $v['D'];
                            $weight = floatval($v['E']);
                            $pay = floatval($v['F']);
                            $rebat = $v['G'];
                            $service = $v['H'];
                            $send_company = $v['I'];
                            $send_no = $v['J'];
                            $major_no = trim($v['K']);
                            if (!$company || !$expressno || !$in_store_time || !$weight || !$pay || !$rebat || !$service || !$send_company || !$send_no) {
                                continue;
                            }
                            $where = [
                                'expressno' => $expressno,
                                'status' => 2,
                                'pay_status' => 1
                                ];
                            if (mb_strlen($send_company, "utf-8") > 20 || strlen($send_no) > 50) {
                                continue;
                            }
                            $set_data = [
                                'send_company' => $send_company,
                                    'send_no' => $send_no,
                                    'out_store_time' => $time,
                                    'status' => 3,
                                    'major_no'=>$major_no
                                    ];
                            $this->db->update($set_data, $where);
                            $suc_row++;
                        }
                    }
                }
                showmessage('文件中含有'.$row.'条,成功导入'.$suc_row.'条',HTTP_REFERER);
            }
        } else {
            $start_time = date("Y-m-d", strtotime("-3 month"));
            $end_time = date("Y-m-d", time());
            include $this->admin_tpl('batch_out_store');
        }
    }
    /** 
     * 反解service
     */
    private function getServiceName($serviceStr) {
        $name = ''; 
        $len = strlen($serviceStr);
        for ($i = 0; $i < $len; $i++) {
            if ($serviceStr[$i] == '1' && isset(self::$services[$i+1])) {
                $name .= self::$services[$i+1]['name'] . ';';
            }   
        }
        if (!$name) {
            $name = '无';
        }
        return $name;
    }   

    private static function formatWeight($weight) {
        if (!is_numeric($weight)) {
            return false;
        }
        $weight = floatval($weight);
        if ($weight <= 0) {
            return false;
        }
        return $weight;
    }

    function sendno_status_update() {
        $where = [
            'status' => 3,
        ];
		$lists = $this->db->listinfo($where, '', $page, 10);
		$pages = $this->db->pages;
        $nodes = self::$nodes;
        include $this->admin_tpl('set_status');
    }

    function set_status() {
        $expressno = isset($_GET['expressno']) ? trim($_GET['expressno']) : '';
        $get_node = isset($_GET['node']) ? trim($_GET['node']) : '';
        if (!$expressno || !self::$nodes[$get_node]) {
            echo 0;
            exit;
        }
        $where = [
            'expressno' => $expressno,
        ];
        $get_one = $this->db->get_one($where, 'pay_status,status,time_node');
        if (!$get_one) {
            echo 0;
            exit;
        }
        if ($get_one['pay_status'] != 1 || $get_one['status'] != 3) {
            echo 0;
            exit;
        }
        $store_node = unserialize($get_one['time_node']);
        $format_node = self::checkFormatNode($get_node, $store_node);
        if (!$format_node) {
            echo 0;
            exit;
        }
        $save_node = serialize($format_node);
        $set_data = [
            'time_node' => $save_node
        ];
        $res = $this->db->update($set_data, $where);
        if ($res) {
            echo 1;
        } else {
            echo 0;
        }
    }

    private static function checkFormatNode($get_node, $store_node) {
        if (!isset(self::$nodes[$get_node])) {
            return false;
        }
        if (!is_array($store_node)) {
            $store_node = [];
        }
        if (isset($store_node[$get_node])) {
            return false;
        }
        $flag = true;
        for ($i = 1; $i < intval($get_node); $i++) {
            if (!isset($store_node[$i])) {
                $flag =false;
                break;
            }
        }
        if (!$flag) {
            return false;
        }
        $data = self::$nodes[$get_node];
        $data['time'] = time();
        $store_node[$get_node] = $data;
        return $store_node;
    }
    private function getServicePrice($serviceStr) {
        $price = 0; 
        $len = strlen($serviceStr);
        for ($i = 0; $i < $len; $i++) {
            if ($serviceStr[$i] == '1' && isset(self::$services[$i+1])) {
                $price += self::$services[$i+1]['price'];
            }   
        }
        return $price;
    }   

    function express_list() {
        if(isset($_POST['dosubmit'])) {
            $ids_arr = isset($_POST['expressid']) ? $_POST['expressid'] : [];
            $status = isset($_POST['s']) ? intval($_POST['s']) : 0;
            if (!is_array($ids_arr) || !$status) {
                showmessage('非法操作', HTTP_REFERER);
            }
            if (count($ids_arr)>10) {
                showmessage('每次选择不能超过10个', HTTP_REFERER);
            }
            if (!$ids_arr) {
                showmessage('至少选择一个', HTTP_REFERER);
            }
            $check = self::checkSetStatus($status);
            if (!$check) {
                showmessage('非法操作', HTTP_REFERER);
            }
            $ids_str = implode(",", $ids_arr);
            $where = "`id` in ($ids_str)";
            $list = $this->db->select($where, 'status');
            if (!$list) {
                showmessage('非法操作', HTTP_REFERER);
            }
            $flag = true;
            foreach ($list as $val) {
                if ($val['status'] != $status) {
                    $flag = false;
                    break;
                }
            }
            if (!$flag) {
                showmessage('非法操作', HTTP_REFERER);
            }
            $time = time();
            $status = $status + 1;
            $set_data = [
                'status' => $status,
            ];
            $set_time = $this->getStatusTime($status);
            $set_data = array_merge($set_data, $set_time);
            $this->db->update($set_data, $where);
            showmessage('操作完成', HTTP_REFERER);
        } else {
            $status = isset($_GET['s']) ? intval($_GET['s']) : 0;
            if (!$status) {
                showmessage('非法操作', HTTP_REFERER);
            }
            $where = [
                'status' => $status,
                ];
            $lists = $this->db->listinfo($where, '', $page, 10);
            $pages = $this->db->pages;
            include $this->admin_tpl('list');
        }
    }

    function set_next_status() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $status = isset($_GET['s']) ? intval($_GET['s']) : 0;
        if (!$id || !$status) {
            showmessage('非法操作', HTTP_REFERER);
        }
        $check = self::checkSetStatus($status);
        if (!$check) {
            showmessage('非法操作', HTTP_REFERER);
        }
        $where = [
            'id' => $id,
            ];
        $get_one = $this->db->get_one($where, 'status');
        if (!$get_one) {
            showmessage('非法操作', HTTP_REFERER);
        }
        if ($get_one['status'] != $status) {
            showmessage('非法操作', HTTP_REFERER);
        }
        $time = time();
        $status = $status + 1;
        $set_data = [
            'status' => $status,
        ];
        $set_time = $this->getStatusTime($status);
        $set_data = array_merge($set_data, $set_time);
        $this->db->update($set_data, $where);
        showmessage('操作完成', HTTP_REFERER);
    }

    function getStatusTime($status) {
        $time = time();
        $set_time = [];
        switch ($status) {
            case 4: $set_time['plane_time'] = $time;break;
            case 5: $set_time['clearance_time'] = $time;break;
            case 6: $set_time['distribute_time'] = $time;break;
            case 7: $set_time['complete_time'] = $time;break;
        }
        return $set_time;
    }

    static function checkSetStatus($status) {
        $status = intval($status);
        if ($status < 3 || $status >= 7) {
            return false;
        }
        return true;
    }
	
}
?>
