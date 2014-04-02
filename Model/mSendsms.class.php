<?php
import('Common.Sms');
//短信发送
class mSendsms extends mBase {
	
	public function sendSms($card_info,$action,$otherData = array())
	{
		$mobile = trim($card_info['tel']);
		if(!$mobile) return;
		//获取系统设置（是否需要发短信）
		$settingModel = ClsFactory::Create('Model.mSetting');
		$setting = $settingModel->getSettingInfo();
		if(!$setting['is_send_sms']) return;
		//-----------------------------------
		$content = '';
		$sms = new Sms();
		if($action == 1)     //储值卡入场
		{
			if($card_info['money']<=5)
			{
				$content = '尊敬的业主：你的车辆卡内余额已不足5元,请尽快充值,回复TD退订此类信息。';
			}
		}elseif($action == 2)//储值卡出场
		{
			return;
			if(!$otherData['real_money']) $otherData['real_money'] = 0;
			$left_money = $card_info['money'] - $otherData['real_money'];
			if($left_money<0) $left_money = '负'.trim($left_money,'-');
			$content = '您好！你的车辆卡现扣费'.$otherData['real_money'].'元，余额为'.$left_money.'元。祝您生活愉快！回复TD退订此类信息。';
		}elseif($action == 3)//卡片充值
		{
			$otherData['charge'] = intval($otherData['charge']);
			$card_info['money'] = intval($card_info['money']);
			$content = '尊敬的业主：您好！你现已充值%s元，余额为%s元。祝您生活愉快！回复TD退订此类信息。';
			$content = sprintf($content,$otherData['charge'],$card_info['money']);
		}elseif ($action == 4) //控制器异常发送短信
		{
			$content = $otherData['sms_content'];
			$log['extra'] = $otherData['door_id'];
		}
		if($content)
		{
			$return = $sms->send($mobile,$content);
			$log['card_code'] = isset($card_info['card_code']) ? $card_info['card_code'] : '';
			$log['mobile'] = $mobile;
			$log['action'] = $action;
			$log['content'] = $content;
			$log['send_status'] = $return['sendStatus'] ? 1 : 0;
			$log['return_str'] = $return['returnStr'];
			$model = ClsFactory::Create('Model.mMessageLog');
			$model->add($log);
		}
	}	
	
	public function checkMobile($mobile)
	{
		if(!$mobile) return false;
		$pattern = "/^(13|14|15|18)\d{9}$/";
		if (strlen($mobile) == 11 && preg_match($pattern,$mobile))
		{
			return true;
		}else
		{
			return false;
		}
	}
	
}

    
