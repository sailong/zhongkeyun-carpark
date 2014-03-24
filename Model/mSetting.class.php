<?php
class mSetting extends mBase {
	
	//获取参数设置信息
	public function getSettingInfo() {
		$dSetting = ClsFactory::Create ( 'Data.dSetting' );
		$result = $dSetting->getSettingInfo();
		return $result;
	}
	
	//修改参数设置信息，$setting_info的key也字段名一致
	public function modifySettingInfo($setting_info) {
		if (empty($setting_info))
			return false;
		if (!$this->getSettingInfo()) {
			return $this->addSetting($setting_info);
		}
		$dSetting = ClsFactory::Create ( 'Data.dSetting' );
		$result = $dSetting->modifySettingInfo($setting_info);
		return $result;
	}
	
	//增加参数设置信息
	private function addSetting($setting_info) {
		if (empty($setting_info))
			return false;
		$dSetting = ClsFactory::Create ( 'Data.dSetting' );
		$result = $dSetting->addSetting($setting_info);
		return $result;
	}
	
}

    
