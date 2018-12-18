<?php


class alipay {
	public $gateway = 'https://openapi.alipaydev.com/gateway.do';
	// public $gateway = 'https://openapi.alipay.com/gateway.do';
	public $appid ='2016092200572120';
	public $rsaPrivateKey ='MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCN4Ip6JzkqgBIqk9C8lH+jeW+i+kMoQ6ITeq29GBAb9dPmXpY9U/VGlz5aD42mrgycxsTY4DsVcRF4MuvHENNDZ5+br+lByj00iQPJrpbQ5U3IZXqSCovHgxSWOLc93xdpp8nVtf6N0aMeYJEgUcOWHR2+v+nwBnzbeuqS5EuNbffFFZjPgLzqsBK4csb3g4W9uqeSEbYVAJc2Yxii2+yo+SYH6FEDtbJH+sJBcwwXauDAke3Geu+TLdoSGVlUwfYEyIdaeI0J2ElfdF/ibiAr4RI6MZkqV+DD5M0D1G/ZZ1+//4KB/VTaxjsdXR+xnolu3r24jdQ97cS+6r+5BLETAgMBAAECggEAA7DoyV7F3PYyAsuPrahLXcxl/B54qs5/o/QYGwhKOv/9QEwLqpLJmcmLhLpM3B5BE78UFfRXdSpY4wCoI6irZmCGxX0hG2YQi5JY9+FD6TFeAa2qL6x4kHs3iOT213Q7XrNPy/CNK9FdZgfrHqUh6oDdaj4B7w2sJ+Wdvq6TzJ0a1XS2NrBPmjn6ppWttLrIwGgiyeMxvSdK6SFngwnqPA5IE7DkaTAq97C1BqVxoey6BRIq5fjOEvPBObnbNK266AovjA9MfrWN7RdaWR11gJAdP00Z4wZYVOi3pV1O0Y4s2SlGP12ZMJJIqrOXm7CYkPYgOYaeBV/q7k3QN1GaAQKBgQDQTlwv3uNN6kTXET1VmbLEl8+unn44IVSAXj/L3BIRhKR0wukqzBwBVrinP5LwlQ9kPC6kgun1e7jnDf1oUhXOJX4dQTDliwg+/0Nq4txgiGS0G3xk9CODYeYm1T6EthwPZEcjP95IeyIQJ84S9nYfTmbnhcM+rqfLVKCEbkYdAQKBgQCuXIROwRcCNvwkmJhYh+7cmKWTTHcrnojLjqs8foD+bkaPCktCIeRw2eJaxD6ocQldUSjiPg4xO14ZroNnGHQQLwE0daSpcjFj6pWjst+U/mHR+IStqVULEHUugLkGfC6TRe42n2KuWzdGlqCSBNlmI6rbzl3yM4r+7/V4iC6KEwKBgBMk0HYZztwPRqpZTEC8smA873pF2KXB8mklwEk0/Xgcr5yHeTRUs9IvrSK5xgBUQqjcmxDFc0j7SN/7OinHOXfLnE6F+j9uSqC3hvOgy84XWuHukL0abtUZPzScbnV14xhLB/qmWQBiSJwhfF/jxLFV3EE02t+833DhYysok0sBAoGAG+9usz90dEJi+2oHXofI2UcLoCHsJezsddCR1std12XLoDdB/4J2my4NegVfFJWnrm/GYRkYJQMh/HNdUjM0xTMCumjuzMci6qM2/lc2QyTOf1mVyE0M9wzX+W9eNY/H1oiptc6rfMvYe1K+vyZPvnGQek8B9kAT0OyZI4qOCkkCgYAQyLCo1vQAAxHspcsKNjmprUj8WgNo9OfN7rZn39BgxgDosb7Qp0px6mHa0VRbtZI2GFSeLyqJX3XDqMgWi8jiVXi2Ce+/Yz9gtsE9Wsm7BGqkd+XNt4Dm40tsRgmsuX3tAVxeScO2e89Q7I0FwK82LPQuyfLkr/2sqnN6DN8tGQ==';
	public $alipayrsaPublicKey ='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjeCKeic5KoASKpPQvJR/o3lvovpDKEOiE3qtvRgQG/XT5l6WPVP1Rpc+Wg+Npq4MnMbE2OA7FXEReDLrxxDTQ2efm6/pQco9NIkDya6W0OVNyGV6kgqLx4MUlji3Pd8XaafJ1bX+jdGjHmCRIFHDlh0dvr/p8AZ823rqkuRLjW33xRWYz4C86rASuHLG94OFvbqnkhG2FQCXNmMYotvsqPkmB+hRA7WyR/rCQXMMF2rgwJHtxnrvky3aEhlZVMH2BMiHWniNCdhJX3Rf4m4gK+ESOjGZKlfgw+TNA9Rv2Wdfv/+Cgf1U2sY7HV0fsZ6Jbt69uI3UPe3Evuq/uQSxEwIDAQAB';
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
		$this->aop->sign= '';
		$this->aop->timestamp= date('Y-m-d H:i:s');
	}
	

	public function AlipayTradePagePayRequest($info) {
		$this->initAop();
		$request = new AlipayTradePagePayRequest ();

		$data = array();
		$data['out_trade_no']=$info['out_trade_no'];
		$data['total_amount']=$info['total_amount'];
		$data['subject']=$info['subject'];
		$data['body']=$info['body'];
		$data['out_trade_no']='FAST_INSTANT_TRADE_PAY';

		$bz = json_encode((object)$data);
		// var_dump($bz);
		$request->setBizContent($bz);
		$result = $this->aop->pageExecute ( $request); 
		echo $result;exit();
		$responseNode = str_replace(".", "", $request->getApiMethodName()) . "response";
		// var_dump(json_encode($result));
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			return true;
		} else {
			return false;
		}
	}
}