<?php
defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('foreground','member');
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);

class index extends foreground{
	private $express_trade_log;
	function __construct() {
		parent::__construct();
		$this->express_trade_log = pc_base::load_model('express_trade_log_model');
		$this->member_express = pc_base::load_model('member_express_model');
		$this->_userid = param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid');
		$this->alipay= pc_base::load_sys_class('alipay','',1);
	}
	//首页
	public function init() {
		
	}
	private  function needPay($id)
	{
		$memberinfo = $this->memberinfo;
		$epinfo = $this->member_express->get_one(array('userid'=>$memberinfo['userid'],'id'=>(int)$id,'status'=>2,'pay_status'=>0));
		if (empty($epinfo)) {
			return false;
		}
		return $epinfo;
	}

	public function express() {
		$memberinfo = $this->memberinfo;
		if (isset($_POST['submit'])) {
			$address_id = isset($_POST['address_id']) ? (int)$_POST['address_id']:0;
			$express_id = isset($_POST['express_id']) ? (int)$_POST['express_id']:0;
			if ($address_id <1 || $express_id<1) {
				showmessage('非法请求');
			}
			$this->member_address_model = pc_base::load_model('member_address_model');
			$addressInfo = $this->member_address_model->get_one(array('member_id'=>$memberinfo['userid'],'id'=>(int)$address_id,'auditstatus'=>1));

			if (empty($addressInfo)) {
				showmessage('无效收货地址');
			}
			$info = $this->member_express->get_one(array('userid'=>$memberinfo['userid'],'id'=>(int)$express_id));
			if (empty($info)) {
	            showmessage('访问不合法');
	        }
	        if ($info['status']!=2 || $info['price']<=0) {
	            showmessage('运单状态不支持支付');
	        }
	        if ($info['pay_status']==1 ) {
	            showmessage('运单已支付');
	        }
	        $this->member_express->update(array('address_id' => $address_id) , array('id' => $express_id) );

	        $order_no = $this->express_trade_log->getOrderNo($memberinfo['userid'], $info['expressno']);
	       
	        $trade = array(
				'out_trade_no'=>$order_no,
				'total_amount'=> 0.01,//$info['price'],
				'subject'=>'支付运费',
				'body'=>'快递费支付'.$info['price'].'元',
			);
			$res = $this->alipay->AlipayTradePagePayRequest($trade);
			echo $res;
			exit();
		}
		showmessage('非法请求');
		
	}
	
}
?>