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
		$productlist = $this->db->listinfo($where, 'id DESC', $page, 15);
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
				if (empty($info["country_goods_name"]) || empty($info["uprice"])|| empty($info["uweight"])) {
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
					$goods_data["create_time"] = date('Y-m-d H:i:s');
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
		$productid = isset($_POST['productid']) ? $_POST['productid'] : showmessage(L('illegal_parameters'), HTTP_REFERER);
		
		
		if(empty($productid)) {	
				showmessage(L('operation_failure'), HTTP_REFERER);
			
		} else {
			$productid = array_map("intval",$productid);
			$where = " id IN (". implode(",",$productid). ") ";
			$this->db->delete($where);		
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
	
	public function import_goods() {
		
		$template = isset($_GET['template']) && trim($_GET['template']) ? (int)trim($_GET['template']) :0;
		$import = isset($_POST['import']) && trim($_POST['import']) ? (int)trim($_POST['import']) :0;
		if($template==1) {
			 	
                $filename = "product_goods_template_".date("YmdHis");
                $head = array('条形码', '状态(1有效，0无效)', '日文名', '中文名', 
                	'英文名','特殊商品','规格',
                	'计量单位','价格（日元）','价格（人民币）',
                	'净重（g）','毛重（g）','生产国',
                );
                $row = array();
                $excel= pc_base::load_sys_class('excel','',1);
                $excel->export($head, $row, $filename);
                exit;
		}
		if($import==1) {
			 	
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
                $create_time = date("Y-m-d H:i:s");

                if($_FILES['express_data']['tmp_name']) {
                    $excel= pc_base::load_sys_class('excel','',1);
                    $data = $excel->import($_FILES['express_data']['tmp_name'], $ext);
                    if (!$data) {
                        showmessage('没检测到数据,请检查是否excel或者是否有内容',HTTP_REFERER);
                    }
                    $row = count($data) - 1;
                    if ($row>20000) {
                    	showmessage('每次导入数量限制在2w内',HTTP_REFERER);
                    }
                    $status_str= array('有效'=>'1','无效'=>'0');
                    $special_str= array('是'=>'1','否'=>'0');

                    foreach ((array)$data as $k=>$v) {

                        if ($k > 1) {
                            if (count($v) < 13) {
                                continue;
                            }
                            $v = array_map("addslashes",$v);
                            $bar_code = trim($v['A']);
                            $status = trim($v['B']);
                            $country_goods_name = trim($v['C']);
                            $ch_name = trim($v['D']);
                            $en_name = trim($v['E']);
                            $is_special = trim($v['F']);
                            $goodsmodel = trim($v['G']);
                            $measur_unit = trim($v['H']);
                            $uprice = trim($v['I']);
                            $zhprice = trim($v['J']);
                            $nweight = trim($v['K']);
                            $uweight = trim($v['L']);
                            $product_area = trim($v['M']);

                            $nweight = (int)$nweight;
                            $uweight = (int)$uweight;

                            if (empty($bar_code) || empty($status)|| empty($is_special)) {
                            	continue;
                            }
                            $uprice = floatval($uprice);
                            $zhprice = floatval($zhprice);
                            
                           	$status = isset($status_str[$status]) ? $status_str[$status] :$status;
                           	$is_special = isset($special_str[$is_special]) ? $special_str[$is_special] :$is_special;

                            if (!in_array($status, array('0','1')) || !in_array($is_special, array('0','1'))) {
                            	continue;
                            }
                            if ($uprice<0 || $uweight<0 ) {
                            	continue;
                            }
                            
                            $one = $this->db->get_one(array("bar_code"=>$bar_code));
                            
                            $goods_data["bar_code"] = $bar_code;
                            $goods_data["status"] = $status;
							$goods_data["country_goods_name"] = $country_goods_name;
							$goods_data["ch_name"] = $ch_name;
							$goods_data["en_name"] = $en_name;
							$goods_data["is_special"] = $is_special;
							$goods_data["goodsmodel"] = $goodsmodel;
							$goods_data["measur_unit"] = $measur_unit;
							$goods_data["uprice"] = $uprice;
							$goods_data["uweight"] = $uweight;
							$goods_data["product_area"] = $product_area;

							$goods_data["zhprice"] = $zhprice;
							$goods_data["nweight"] = $nweight;

                            $goods_data["create_time"] = $create_time;
                            if (empty($one)) {
                            	$res = $this->db->insert($goods_data);
                            } else {
                            	$res = $this->db->update($goods_data,array('id'=>$one['id']));
                            }
                            if ($k%1000==0) {
                            	usleep(500000);
                            }
                            // var_dump($res);exit();
                            $suc_row++;
							
                        }
                    }
                } else {
                    showmessage('上传失败',HTTP_REFERER);
                }
                showmessage('文件中含有'.$row.'条,成功导入'.$suc_row.'条', '?m=express&c=product&a=init');
                
		}
		include $this->admin_tpl('import_goods');
	}
	
}
?>