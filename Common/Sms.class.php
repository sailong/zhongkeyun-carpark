<?php
class Sms
{
	private $apiUrl='http://ums.zj165.com:8888/sms/Api/';
	private $account;
	private $ch;
	
	public function __construct()
	{
		$this->account['SpCode'] = '005671';          //企业编号
		$this->account['LoginName'] = 'sx_qt';        //用户名称 
		$this->account['Password'] = 'qt1227';		  //用户密码
	}
	/*
	 * 根据接口获取产品信息
	 * 多个手机号用 英文逗号分开
	 */
	public function send($mobile,$content)
	{
		if(!$mobile || !$content) return;
		$this->init_curl();
		$data = $this->account;
		$data['MessageContent'] = $content;
		$data['UserNumber'] = $mobile;
		$data['SerialNumber'] = str_pad(time().mt_rand(1,1000),20,'0');  //流水号
		$data['ScheduleTime'] = '';      //预约发送时间
		$data['ExtendAccessNum'] = '';
		$data['f'] = 1;                  //检测方式
		$returnData = $this->send_get($data,$this->apiUrl.'Send.do');
		
		$return['returnStr'] = $returnData;
		if($returnData && substr($returnData,0,8)=='result=0')
		{
			$return['sendStatus'] = true;
			return $return;
		}
		$return['sendStatus'] = false;
		return $return;
	}
	
	private function send_post($data,$url='')
	{
		if(!$url) $url = $this->apiUrl;
		curl_setopt($this->ch,CURLOPT_URL, $url);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch,CURLOPT_POST, TRUE); //指定post数据
		$data = $this->iconvData($data);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($this->ch);
		/* if(curl_errno($this->ch))
		{
			print curl_error($this->ch);die;
		} */
		return $response;
	}
	
	private function send_get($data,$url='')
	{
		$data = $this->iconvData($data);
		if(!$url) $url = $this->apiUrl;
		$url.='?'.http_build_query($data);
		if($this->ch)
		{
			curl_setopt($this->ch , CURLOPT_URL, $url);
			curl_setopt($this->ch , CURLOPT_RETURNTRANSFER, TRUE);
			return curl_exec($this->ch);
		}else
		{
			return file_get_contents($url);
		}
	}

	/**
	 * 初始化curl
	 */
	private function init_curl()
	{
		if(extension_loaded('curl_init'))
		{
			$this->ch  = curl_init();
		}
	}
	private function close_curl()
	{
		if($this->ch) curl_close($this->ch);
	}
	
	function __destruct()
	{
		if($this->ch) $this->close_curl();
	}
	
	private function iconvData($data)
	{
		foreach ($data as $key=>$val)
		{
			$data[$key] = $val ? iconv("UTF-8","GB2312//IGNORE",$val) : '';
		}
		return $data;
	}

}
?>