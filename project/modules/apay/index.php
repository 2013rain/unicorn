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
		$this->member_address_model = pc_base::load_model('member_address_model');
		$this->member_model = pc_base::load_model('member_model');
		$this->_userid = param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid');
		$this->alipay= pc_base::load_sys_class('alipay','',1);
	}
	//首页
	public function init() {
		$SEO = seo(1);
		$memberinfo = $this->memberinfo;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$where=array(
			'member_id'=>$memberinfo['userid'],
			'status'=>1,
		);
		$tradelist = $this->express_trade_log->listinfo($where, 'finish_time DESC', $page, 30);
		include template('apay', 'apay_list');

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

			$addressInfo = $this->member_address_model->get_one(array('member_id'=>$memberinfo['userid'],'id'=>(int)$address_id,'auditstatus'=>1));

			if (empty($addressInfo)) {
				showmessage('无效收货地址','index.php?m=express&c=index&a=init');
			}
			$info = $this->member_express->get_one(array('userid'=>$memberinfo['userid'],'id'=>(int)$express_id));
			if (empty($info)) {
	            showmessage('访问不合法');
	        }
	        if ($info['status']!=2 || $info['price']<=0) {
	            showmessage('运单状态不支持支付','index.php?m=express&c=index&a=init');
	        }
	        if ($info['pay_status']==1 ) {
	            showmessage('运单已支付','index.php?m=express&c=index&a=init');
	        }
	        $this->member_express->update(array('address_id' => $address_id) , array('id' => $express_id) );

	        $order_no = $this->express_trade_log->getOrderNo($memberinfo['userid'], $info['expressno']);
	       	$userinfo = $this->member_model->get_one(array('userid'=>$memberinfo['userid']));
	       	if ($userinfo['amount']< $info['price'] || $userinfo['amount']<=0) {
	       		showmessage('余额不足','index.php?m=express&c=index&a=init');
	       	}
	       	//
	       	$paydata = array(
	       		'status'=>1,
	       		'amount'=>$info['price'],
	       		'balance'=>($userinfo['amount']-$info['price']),
	       		'finish_time'=>date('Y-m-d H:i:s'),
	       		'express_id'=>$express_id,
	       		'order_name'=> '快递费支付'.$info['price'].'元',
	       	);
	       	$this->express_trade_log->update($paydata, array('order_no'=>$order_no));
	       	//
	       	$user_update =array(
	       		'amount'=> ($userinfo['amount']-$info['price'])
	       	);
	       	$this->member_model->update($user_update, array('userid'=>$memberinfo['userid']));
	       	//
	       	$exp_data = array(
	       		'pay_status'=>1,
	       		'pay_money'=>$info['price'],
	       		'pay_time'=>time(),
	       	);
	       	$this->member_express->update($exp_data, array('id'=>$express_id) );
			showmessage('支付成功','index.php?m=express&c=index&a=init');
	       
		}
		showmessage('非法请求');
		
	}
	
}
?>