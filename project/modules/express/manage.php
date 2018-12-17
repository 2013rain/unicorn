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
	}

	/**
	 * defalut
	 */
	function init() {
		$lists = $this->db->listinfo([], '', $page, 20);
		$pages = $this->db->pages;
		include $this->admin_tpl('list');
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
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="待入库列表_' . $_POST['start_time'] .'_' .$_POST['end_time'] . '.csv"');
                header('Cache-Control: max-age=0');
                $fp = fopen('php://output', 'a');
                $head = array('仓库', '快递公司', '快递单号', '创建时间', '重量');
                foreach($head as $i => $v)
                {
                    $head[$i] = iconv('utf-8', 'gbk', $v);
                }
                $limit = 10000;
                $r = 0;
                fputcsv($fp, $head);
                $row = array();
                for ($i=0;$i<$num;$i++) {
                    $page_start = $i * $limit;
                    $res = $this->db->select($where, 'storeid,company,expressno,createtime', "$page_start, $pagesize");
                    foreach ($res as $val) {
                        $r++;
                        if($r == $limit)
                        {
                            ob_flush();
                            flush();
                            $r = 0;
                        }
                        $row = array(self::$store[$val['storeid']]['name'], $val['company'], $val['expressno'], date('Y-m-d H:i:s', $val['createtime']));
                        foreach($row as $j => $v)
                        {
                            $row[$j] = iconv('utf-8', 'gbk', $v);
                        }
                        fputcsv($fp, $row);
                    }
                }
            }
            if (isset($_POST['import'])) {
                if (strpos($_FILES['express_data']['type'], 'application/vnd.ms-excel') === false && strpos($_FILES['express_data']['type'], 'text/csv') === false) {
                    showmessage('上传格式必须为csv的excel文件',HTTP_REFERER);
                }
                if ($_FILES['express_data']['size'] > 3145728) {
                    showmessage('上传文件大小不能超过3M',HTTP_REFERER);
                }
                if ($_FILES['express_data']['error'] != 0) {
                    showmessage('上传失败',HTTP_REFERER);
                }
                $row = 0;
                $time = time();
                if($_FILES['express_data']['tmp_name']) {
                    if (($fp = fopen($_FILES['express_data']['tmp_name'], 'rb')) !== false) {
                        while (($data = fgetcsv($fp)) !== false) {
                            if ($row!=0) {
                                if (count($data) != 5) {
                                    continue;
                                }
                                $store = $data[0];
                                $company = $data[1];
                                $expressno = $data[2];
                                $create_time = $data[3];
                                $weight = floatval($data[4]);
                                if (!$company || !$expressno || !$create_time || !$weight) {
                                    continue;
                                }
                                $where = [
                                    'expressno' => $expressno,
                                    'status' => 1
                                ];
                                $set_data = [
                                    'weight' => $weight,
                                    'status' => 2,
                                    'in_store_time' => $time
                                ];
                                $this->db->update($set_data, $where);
                            }
                            $row++;
                        }
                    }
                }
                showmessage('批量处理完成',HTTP_REFERER);
            }
        } else {
            $start_time = date("Y-m-d", strtotime("-3 month"));
            $end_time = date("Y-m-d", time());
            include $this->admin_tpl('batch_in_store');
        }
    }

    function batch_out_store() {
        if(isset($_POST['dosubmit'])) {
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
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="待出库列表_' . $_POST['start_time'] .'_' .$_POST['end_time'] . '.csv"');
                header('Cache-Control: max-age=0');
                $fp = fopen('php://output', 'a');
                $head = array('仓库', '快递公司', '快递单号', '入库时间', '重量', '支付金额', '折扣', '增值服务', '发货公司' , '发货单号');
                foreach($head as $i => $v)
                {
                    $head[$i] = iconv('utf-8', 'gbk', $v);
                }
                $limit = 10000;
                $r = 0;
                fputcsv($fp, $head);
                $row = array();
                for ($i=0;$i<$num;$i++) {
                    $page_start = $i * $limit;
                    $res = $this->db->select($where, 'storeid,company,expressno,in_store_time,weight,pay_money,rebate,service', "$page_start, $pagesize");
                    foreach ($res as $val) {
                        $r++;
                        if($r == $limit)
                        {
                            ob_flush();
                            flush();
                            $r = 0;
                        }
                        $in_store_time = format::date($val['in_store_time'], 1);
                        $service = $this->getServiceName($val['service']);
                        $rebate = $val['rebate'] ? $val['rebate'] : '无';
                        $row = array(self::$store[$val['storeid']]['name'], $val['company'], $val['expressno'], $in_store_time, $val['weight'], $val['pay_money'], $rebate, $service);
                        foreach($row as $j => $v)
                        {
                            $row[$j] = iconv('utf-8', 'gbk', $v);
                        }
                        fputcsv($fp, $row);
                    }
                }
            }
            if (isset($_POST['import'])) {
                if (strpos($_FILES['express_data']['type'], 'application/vnd.ms-excel') === false && strpos($_FILES['express_data']['type'], 'text/csv') === false) {
                    showmessage('上传格式必须为csv的excel文件',HTTP_REFERER);
                }
                if ($_FILES['express_data']['size'] > 5242880) {
                    showmessage('上传文件大小不能超过5M',HTTP_REFERER);
                }
                if ($_FILES['express_data']['error'] != 0) {
                    showmessage('上传失败',HTTP_REFERER);
                }
                $row = 0;
                $time = time();
                if($_FILES['express_data']['tmp_name']) {
                    if (($fp = fopen($_FILES['express_data']['tmp_name'], 'rb')) !== false) {
                        while (($data = fgetcsv($fp)) !== false) {
                            if ($row!=0) {
                                if (count($data) != 10) {
                                    continue;
                                }
                                $store = $data[0];
                                $company = $data[1];
                                $expressno = $data[2];
                                $in_store_time = $data[3];
                                $weight = floatval($data[4]);
                                $pay = floatval($data[5]);
                                $rebat = $data[6];
                                $service = $data[7];
                                $send_company = $data[8];
                                $send_no = $data[9];
                                if (!$company || !$expressno || !$in_store_time || !$weight || !$pay || !$rebat || !$service || !$send_company || !$send_no) {
                                    continue;
                                }
                                $where = [
                                    'expressno' => $expressno,
                                    'status' => 2,
                                    'pay_status' => 1
                                ];
                                $set_data = [
                                    'send_company' => iconv('gbk', 'utf-8', $send_company),
                                    'send_no' => $send_no,
                                    'out_store_time' => $time,
                                    'status' => 3
                                    ];
                                $this->db->update($set_data, $where);
                            }
                            $row++;
                        }
                    }
                }
                showmessage('批量处理完成',HTTP_REFERER);
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
            if ($serviceStr[$i] == '1' && isset(self::$services[$i])) {
                $name .= self::$services[$i]['name'] . ';';
            }   
        }
        if (!$name) {
            $name = '无';
        }
        return $name;
    }   

	
}
?>
