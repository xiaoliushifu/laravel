<?php

namespace App\SMS;
/**
*   使用方法1：
*   $alisms = new \Org\Util\AliSms("appkey","secretkey");
*   $result = $alisms->sign('短信签名')->data(短信模板变量[数组])->code('短信模板ID')->send('短信接收号码');
*/
class AliSms {

	//API请求地址
	private $gatewayUrl = "https://eco.taobao.com/router/rest";

	//API名称
	private $method="alibaba.aliqin.fc.sms.num.send";

	//响应格式
	private $format="json";

	//API协议版本
	private $v="2.0";

	//签名方式
	private $sign_method="md5";

	private $appKey;
	private $secretKey;

	//短信类型
	private $sms_type = "normal";

	//短信签名
	private $sms_free_sign_name = '';

	//短信模板变量
	private $sms_param = [];

	//短信接收号码
	private $rec_num = '';

	//短信模版ID
	private $sms_template_code = '';

	private function _send(){
		$param = [
			'method'				=>	$this->method,
			'format'				=>	$this->format,
			'app_key'				=>	$this->appKey,
			'timestamp'				=>	date("Y-m-d H:i:s"),
			'v'						=>	$this->v,
			'sign_method'			=>	$this->sign_method,
			'sms_type'				=>	$this->sms_type,
			'sms_free_sign_name'	=>	$this->sms_free_sign_name,
			'sms_param'				=>	json_encode($this->sms_param),
			'rec_num'				=>	$this->rec_num,
			'sms_template_code'		=>	$this->sms_template_code,
		];
		if(!$this->sms_param){
			unset($param['sms_param']);
		}
		//array_merge只传递一个数组，且是索引数组时才有用，所以其他情况下无用
		//param数组的，键值对，拼接字符串
		$param['sign'] = $this->_sign(array_merge($param));
		
		//去执行最终的发送
		//$param数组的sign下标的值，是其他元素的键值对进行，md5并转大写的字符串。
		return  $this->_sendSms($param);
	}

	private function _sign($param){
	    //按照 关联下标排序
		ksort($param);

		//字符串开头一个
		$sign = $this->secretKey;
		foreach ($param as $k => $v){
		    //键值对 紧贴着拼凑起一个字符串
			$sign .= "$k$v";
		}
		//字符串结尾一个
		$sign .= $this->secretKey;
        //md5后，全部转为大写。
		return strtoupper(md5($sign));
	}

	private function _sendSms($param){
		$url = $this->gatewayUrl . "?" . http_build_query($param);
		$ch = curl_init();
		$timeout = 5;
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		return $file_contents;
	}

	public function send($phone=''){
		if($phone!==''){
			$this->phone($phone);
		}
		return $result = $this->_send();
	}

	public function __construct($param1 = "",$param2 = "",$param3 = "",$param4 = ""){
	    //前两个不空，第三个空，且前两个参数是字符串类型
		if ($param1!=="" && $param2!=="" && $param3==="" && is_string($param1) && is_string($param2)) {
			$this->appkey($param1);
			$this->secret($param2);
		}
		else{
			$this->appkey(C("ALI_SMS_APP_KEY"));
			$this->secret(C("ALI_SMS_SECRET_KEY"));
		}
		if (is_array($param2) && $param3!=="") {
			$this->code($param1);
			$this->data($param2);
			$this->sign($param3);
			if($param4!==""){
				$result = $this->send($param4);
			}
		}
	}

	public function appkey($appKey=""){
		if($appKey) $this->appKey = $appKey;
		return $this;
	}

	public function secret($secretKey=""){
		if($secretKey) $this->secretKey = $secretKey;
		return $this;
	}

	 //确定发送哪一类短信，注册认证？修改密码？等
	public function sign($sign_name = ''){
		$this->sms_free_sign_name = $sign_name;
		return $this;
	}

	public function data($data = []){
		$this->sms_param = $data;
		return $this;
	}

	public function phone($phone=''){
		$this->rec_num = $phone;
		return $this;
	}

	public function code($code=''){
		$this->sms_template_code = $code;
		return $this;
	}
}
