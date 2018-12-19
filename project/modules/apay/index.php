<?php
defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util','content');

class index {
	private $express_trade_log;
	function __construct() {
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
		$id = isset($_GET['id']) ? (int)$_GET['id']:0;

		if($id<=0) {
			exit('error');
		}
		$needpayinfo = $this->needPay($id);
		if (empty($needpayinfo) || $needpayinfo['pay_money']<=0) {
			exit('请求错误');
		}
		$order_no = $this->express_trade_log->getOrderNo($memberinfo['userid'], $needpayinfo['expressno']);

		$trade = array(
			'out_trade_no'=>$order_no,
			'total_amount'=>'0.01',
			'subject'=>'支付运费',
			'body'=>'快递费支付'.$needpayinfo['pay_money'].'元',
		);
		$res = $this->alipay->AlipayTradePagePayRequest($trade);
		echo $res;
		exit();
	}
	
}
?>