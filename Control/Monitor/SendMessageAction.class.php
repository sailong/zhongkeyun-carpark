<?php
import('Common.Sms');
class SendMessageAction extends Controller {
	
	private $mAdmin;
	public function _initialize() {
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) {
			$this->toLogin();
		}
	}
	/**
	 * 
	 */
	public function send()
	{
		header('content-type:text/html;charset=utf-8');
		$door_id = $this->objInput->postStr('door_id');
		//$door_id = 1;
		if(!$door_id) $this->returnData(0,'door_id error');
		//获取配置信息
		$settingModel = ClsFactory::Create('Model.mSetting');
		$setting = $settingModel->getSettingInfo();
		if(!$setting['is_send_sms']) return;
		$model = ClsFactory::Create('Model.mDoor');
		$doorData = $model->getDoorById($door_id);
		if(!isset($doorData[$door_id])) $this->returnData(0,'no data');
		$doorData = $doorData[$door_id];
		//获取需要发短信人员的列表
		$adminList = $this->mAdmin->getAdminList(0,100);
		if(!$adminList) $this->returnData(0,'no admin data');
		$mobileArr = array();
		foreach ($adminList as $val)
		{
			if($val['is_receive_msg']) $mobileArr[] = $val['mobile'];
		}
		if(!$mobileArr) $this->returnData(0,'no mobile data');
		$doorType = $doorData['door_type'] == 1 ? '出口':'入口';
		$sms = new Sms();
		$content='%s车辆管理系统中%s%s道闸控制器通讯故障，请查原因。';
		$content = sprintf($content,$setting['company_name'],$doorData['door_name'],$doorType);
		$return = $sms->send(implode(',',$mobileArr),$content);
		//$return['sendStatus'] = 1;
		$this->returnData($return['sendStatus'] ? 1 : 0,$content);
	}
	
	private function returnData($code,$msg='',$resurnData='')
	{
		$data['code']     = $code;
		$data['msg']      = $msg;
		$data['data']     = $resurnData;
		echo json_encode($data);
		die;
	}
}