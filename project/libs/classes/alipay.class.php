<?php


class alipay {
	public $gateway = 'https://openapi.alipaydev.com/gateway.do';
	// public $gateway = 'https://openapi.alipay.com/gateway.do';
	public $appid ='2016092200572120';
	public $rsaPrivateKey ='MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCEiAeCWQCDypBaJlAmflw2hk3ff7TcqlRqYEJAPOi/D8qs+OLvywVe4ZSyx1l7HUK9igRUf0Wku6VefUeLSt1ogldwundvoZEp/RrrMDoQfV0CKcS5joXEy14w0HVWggp6ofb27osIPhw7VJATOfmdoNoQgJi3r/rQP/J6ntWkqFpaVNBeHqzjnuU9s+d8/GZfnA3gftBLwzD41QvX0bMX104iUuuCJ3Oj6eWnr4BUsDa6jovCu3NrSQz8Lz4pXRC6OqtHGUC+Uu2Mpc+C2ZJS/Hp6a8PVmlCvsCSPazzx1e8lrxtvBEHErSCTVEQfe2bhiqVDCuYgs29I3by/EmHVAgMBAAECggEAfB1kYub0+3d1bmeZAiOuOgXW4esYNttxCNoy0TP5iAzC8vB2nrjTw8KgXjkhDJIfkZ+yZGt4jkiJGw9aNBGdfiUqvFB0T9/QMbFuRzk3939f3gm9yUS289C82hwu9x/7rlMGFMTbAZIhIFMWRlsx8DPWZEP6QXQWAHcSn1EmpPb1pID4KIzhHbkFGGpI/r/8Td2rAR6efbEjg4+uzZFXBiUoQEMijw+i5qKYyNDg9erUSdsuZDqwjpuEiwbRdaYlxAsg8a4R7Hhunkz9gZm5UyEz7OEs+hdbh2ASqSdE8ypAsZSVoBnrGxxuEebQYbLQDUpOslY+TlxfbPYT6yOPoQKBgQD+3XnjESmEpdTWnARc//zSZwx52zOXRPK4JN6K+JgVf0zgdhunJ3kWQ1LECLotmMal9kRGkaS3VovT/HUUwGUnRC3wvMeCkaaIsS6PKjiszBdN1RI1vVXbFUlF31MJCZ5NW25x8ktngs1Bahc6FPNI57zlfBY9VJUhM6zyWxYQLQKBgQCFHxp7rJATnwdypvorD+qWwxNBa/MpDvU0sMMo+lppC+yvU/M3crW7JW4LYQcK+LG30mcxBuxLe0KCRR/FejR3SQ6FtL9aJlcZZS4IdJg5qIDp93g4r8jfNBZASCbK8stY4VnSNwVlKqoUqpNkU4EBpknyfqvQHZbYjpq8i7z5SQKBgGWH3ZKzt5J8bbnBFYvm/tTi8Hlq9baBZSAur+k094E44WtaIdIJS5eu4j3uLB0WC6z52mEyjCftdm7Q1+OYcbOe2Z5Z1jERzQIPc7jc3Q8Zjv8WrtZxws4wQKxxNycWidXPYsRJ/fgSh6pTlUUyK2kTyJCzSjjMhUxgxbkWyEdRAoGARbjtsPrHl09Col0sR+OVNZNEgY5dlBKegRNXlB3TvpMdSxMIHvF4l2VaRqFyA+APjLypiXcbycjsI7i6lD6WPifIcGUjl77e/ZEN1CXlGMXVhzUzBVWkOqtYQCDKGJ+ju5CD7+Mbc0ouPJa+jdxihA+pSVXo/R6SRCgkiT4TIukCgYEAr4GJStBDguxbS9pq/QmtnkSs03GLBp4whKgb4nLLL0A0x/MDN4ATMiQKFjLuPL92PNWZY9mkY3OMvweOmNeTy0hOuOdxVHNGklf64ooZUqh/Ci47R+gfjuJm0jF6aOtIV+7M571yCDHFJdB7fvaJVObel0bVhcaisdA91R70dzI=';
	public $alipayrsaPublicKey ='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhIgHglkAg8qQWiZQJn5cNoZN33+03KpUamBCQDzovw/KrPji78sFXuGUssdZex1CvYoEVH9FpLulXn1Hi0rdaIJXcLp3b6GRKf0a6zA6EH1dAinEuY6FxMteMNB1VoIKeqH29u6LCD4cO1SQEzn5naDaEICYt6/60D/yep7VpKhaWlTQXh6s457lPbPnfPxmX5wN4H7QS8Mw+NUL19GzF9dOIlLrgidzo+nlp6+AVLA2uo6Lwrtza0kM/C8+KV0QujqrRxlAvlLtjKXPgtmSUvx6emvD1ZpQr7Akj2s88dXvJa8bbwRBxK0gk1REH3tm4YqlQwrmILNvSN28vxJh1QIDAQAB';
	public $apiVersion ='1.0';
	public $signType ='RSA2';
	public $postCharset ='UTF-8';
	public $format ='json';
	public $aop ;
	/**
	 * 构造函数
	 */
	public function __construct() {
		include(dirname(dirname(__FILE__)) .DIRECTORY_SEPARATOR."alipay".DIRECTORY_SEPARATOR."AopSdk.php");
		$this->aop = new AopClient ();

	}
	
	/**
	 * 调用件事
	 */
	private function init() {
		
	}

	private function initAop() {
		$this->aop->gatewayUrl = $this->gateway;
		$this->aop->appId = $this->appid;
		$this->aop->rsaPrivateKey =$this->rsaPrivateKey;
		$this->aop->alipayrsaPublicKey=$this->alipayrsaPublicKey;
		$this->aop->apiVersion = $this->apiVersion;
		$this->aop->signType = $this->signType;
		$this->aop->postCharset=$this->postCharset;
		$this->aop->format= $this->format;
		
		$this->aop->timestamp= date('Y-m-d H:i:s');
		$this->aop->returnUrl= 'http://local.wangfuyu.com.cn/receive_notify.php';
	}
	
	//pc支付
	public function AlipayTradePagePayRequest($info) {
		$this->initAop();
		$request = new AlipayTradePagePayRequest ();

		$data = array();
		$data['out_trade_no']=$info['out_trade_no'];
		$data['total_amount']=$info['total_amount'];
		$data['subject']=$info['subject'];
		$data['body']=$info['body'];
		$data['product_code']='FAST_INSTANT_TRADE_PAY';
		$data['return_url']='http://local.wangfuyu.com.cn/receive_notify.php';

		$bz = json_encode((object)$data);
		// var_dump($bz);
		$request->setBizContent($bz);
		$result = $this->aop->pageExecute ( $request); 
		return $result;
		
	}
	public function AlipayTradeQueryRequest($info) {
		$this->initAop();
		$request = new AlipayTradeQueryRequest ();

		$data = array();
		$data['out_trade_no']=$info['out_trade_no'];
		

		$bz = json_encode((object)$data);
		// var_dump($bz);
		$request->setBizContent($bz);
		try {
			$result = $this->aop->execute ( $request); 
		} catch (Exception $e) {

			return false;
		}
		return json_decode(json_encode($result),true);
		// $result = $this->aop->execute ( $request); 

		var_dump($result);exit();
		
	}

}