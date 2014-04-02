<?php
class AjaxAction extends Controller {
	
	private $mAdmin;
	protected $notice;
	public function _initialize() {
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) {
			$this->toLogin();
		}
	}
	/**
	 * 
	 */
	public function openDoor()
	{
		$returnData = array();
		$cardCode = $this->objInput->postStr('cardCode');  //卡号
		$readerNo = $this->objInput->postStr('readerNo');  //读卡器编号
		$doorAddr = $this->objInput->postStr('doorAddr');    
		//-------------------------------
		$cardId = 0;
		//获取card_id
		$cardModel = ClsFactory::Create('Model.mCard');
		$cardInfo = $cardModel->getCardByCode($cardCode);
		if(!$cardInfo)
		{
			$this->setNotice(-1,'卡片信息不存在');
		}else
		{
			$cardInfo = $cardInfo[$cardCode];
			$cardId = $cardInfo['card_id'];
			$cardStatus = $cardModel->getCardStatusById($cardId);
			if(!$cardStatus)
			{
				$this->setNotice(-1,'未检测到到卡片状态');
			}else
			{
				$status =  $cardStatus[$cardId];
					
				$cardInfo['check_status'] = $status;
				$cardInfo['start_time'] = date('Y-m-d', time());
				$cardInfo['end_time'] = $cardInfo['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $cardInfo['expire_time']);
					
				$masterCard = $cardModel->getMasterCardInfo($cardId);
				if($masterCard)
				{
					if($cardInfo['card_type'] == 2 && $masterCard['money']<=0) $this->setNotice(-1,'该卡已欠费');
				}else
				{
					if($status > 0) $this->setNotice(-1,$this->getCardStatusStr($status));
				}
			}	
			//---验证结束-------
		}
		//---------------------------------------------------------------------
		$doorModel = ClsFactory::Create('Model.mDoor');
		$doorData = $doorModel->getDoorInfoByAddrReader($doorAddr,$readerNo);
		$door_id = $doorData['door_id'];
		//$doorData = $doorModel->getDoorById($door_id);
		if(!$doorData)
		{
			$this->setNotice(-1,'开门失败:没有找到相应的门号信息！');
		}else
		{
			$cardInfo['physical_door_id'] = $doorData['brake_no'];
			$cardInfo['door_type'] = $doorData['door_type'];
			//---------------------------------------------------------------------
			if(!$this->checkSwipe($doorData,$cardInfo)) $this->setNotice(-1,'没在授权进入的传感器刷卡');
			//---------------------------------------------------------------------
		}
		
		$sessionModel = ClsFactory::Create('Model.mSession');
		if($cardInfo['door_type']==1)
		{
			$cardInfo['sessionData'] = $sessionModel->getSessionInfo($cardId);
		}else 
		{
			if($doorData['park_id'] && $cardModel->hasShareParking($cardId))
			{
				$_leftParkCounts = $sessionModel->getFamilyRemainParkingCount($cardId);
				if($_leftParkCounts==0) $this->setNotice(-2,'车位已满');
			}
		}
		//--储值卡入场时-----------------------------------------------------------
		if($cardInfo['card_type']==2)
		{
			$first_SessionInfo = $sessionModel->isSessionInfo($cardId);
		}
		//-------------------------------------------------------------
		//---------------------------------------------------------------------
		$status = $sessionModel->openDoor($cardId, $door_id);
		//-------------------------------------------------------------
		if($cardInfo['door_type']==0)
		{
			$cardInfo['sessionData'] = $sessionModel->getSessionInfo($cardId);
		}
		//--储值卡出入场-----------------------------------------------------------
		if($cardInfo['card_type']==2)
		{
			//主账户相关------显示主账户余额--------------------------------
			if(!$cardInfo['is_master'])
			{
				if($masterCard)
				{
					$cardInfo['money'] = $masterCard['money'];
				}
			}
			//--------------------------------------------
			$second_SessionInfo = $sessionModel->isSessionInfo($cardId);
			//--发送短信-------------------------------------
			if(isset($first_SessionInfo))
			{
				$smsModel = ClsFactory::Create('Model.mSendsms');
				if($first_SessionInfo==false && $second_SessionInfo)
				{
					$smsModel->sendSms($cardInfo,1);
				}elseif ($first_SessionInfo && $second_SessionInfo==false)
				{
					$currentSessionInfo = $sessionModel->currentSessionInfo($cardId);
					$smsModel->sendSms($cardInfo,2,array('real_money'=>$currentSessionInfo['real_money']));
				}
			}
			
		}
		//--------------------------------------------------------------------------
		$cardInfo['sessionData']['park_time'] = $this->getParkTime($cardInfo['sessionData']['start_time'], $cardInfo['sessionData']['end_time']);
		$cardInfo['sessionData']['park_time_arr'] = $this->getParkTimeArr($cardInfo['sessionData']['start_time'], $cardInfo['sessionData']['end_time']);
		if($cardInfo['sessionData']['start_time'])
		{
			$cardInfo['sessionData']['format_start_time'] = date('m月d日H:i',$cardInfo['sessionData']['start_time']);
			$cardInfo['sessionData']['start_time'] = date('Y-m-d H:i:s',$cardInfo['sessionData']['start_time']);
			
		}
		if($cardInfo['sessionData']['end_time'])
		{
			$cardInfo['sessionData']['format_end_time'] = date('m月d日H:i',$cardInfo['sessionData']['end_time']);
			$cardInfo['sessionData']['end_time'] = date('Y-m-d H:i:s',$cardInfo['sessionData']['end_time']);	
		}
		//有效期 剩余天数【仅月租卡及贵宾卡显示】
		if(isset($cardInfo['card_type']) && in_array($cardInfo['card_type'], array(3,4)) && isset($cardInfo['expire_time']) && $cardInfo['expire_time'])
		{
			//计算剩余天数
			$cardInfo['left_days'] = $this->countLeftDays($cardInfo['expire_time']).'天';
			$cardInfo['expire_time'] = date('Y-m-d',$cardInfo['expire_time']);
		}else
		{
			$cardInfo['expire_time'] = $cardInfo['left_days'] = '';
		}
		//卡片类型
		if(isset($cardInfo['card_type']))
		{
			$cardInfo['card_type_str'] = self::getCardTypes($cardInfo['card_type']);
		}
		$cardInfo['notice'] = $this->notice ? $this->notice : array('code' => 1, 'msg' => '');
		$cardInfo['door'] = $doorData;
		if($status) $this->returnData(1,'',$cardInfo);
		$this->returnData(0,'开门失败',$cardInfo);
	}
	
	
	public function manOpenDoor()
	{
		$returnData = array();
		$cardCode = trim($this->objInput->postStr('cardCode'));        //卡号
		$readerNo = trim($this->objInput->postStr('readerNo'));  		 //读卡器编号
		$doorAddr = trim($this->objInput->postStr('doorAddr'));        //刷卡时间
		$type = $this->objInput->postStr('type');        //刷卡时间
		if(isset($_GET['test']))
		{
			$cardCode = '15743897';
			$readerNo = 4;
			$doorAddr = '31103';
		}
		//-------------------------------
		//获取card_id
		$cardModel = ClsFactory::Create('Model.mCard');
		$cardInfo = $cardModel->getCardByCode($cardCode);
		if(!$cardInfo) $this->returnData(0,'卡片信息不存在');
		
		$cardInfo = $cardInfo[$cardCode];
		$cardId = $cardInfo['card_id'];
		$cardType = $cardInfo['card_type'];
		$cardStatus = $cardModel->getCardStatusById($cardId);
		if(!$cardStatus) $this->returnData(0,'未检测到到卡片状态');
		$status = $cardStatus[$cardId];
		if($status > 0) $this->returnData(0,$this->getCardStatusStr($status));
		//---验证结束-------
		
		//---------------------------------------------------------------------
		$doorModel = ClsFactory::Create('Model.mDoor');
		$doorData = $doorModel->getDoorInfoByAddrReader($doorAddr,$readerNo);
		$door_id = $doorData['door_id'];
		if(!$doorData) $this->returnData(0,'开门失败:没有找到相应的门号信息！');
		$cardInfo['physical_door_id'] = $doorData['brake_no'];
		$cardInfo['door_type'] = $doorData['door_type'];
		//---------------------------------------------------------------------
		$sessionModel = ClsFactory::Create('Model.mSession');
		$real_money = 0 ;
		if($type==4) 
		{
			$real_money = trim($this->objInput->postStr('realMoney')); 
		}
		$status = $sessionModel->manOpenDoor($cardId, $door_id,$real_money);
		if($status) 
		{
			if($type==4 && $cardType == 2)//储值卡 手动开门出场
			{
				$smsModel = ClsFactory::Create('Model.mSendsms');
				$smsModel->sendSms($cardInfo,2,array('real_money'=>$real_money));
			}
			$this->returnData(1,'开闸成功!',$cardInfo);
		}
		$this->returnData(0,'系统无法完成开闸!');
	}
	
	public function getStatInfo()
	{
	
		$end_time = time();
		$start_time = strtotime(date('Y-m-d'));//管理员设置为当天最早
		$doorModel = ClsFactory::Create('Model.mDoor');
		$doorList = $doorModel->getDoorInfo();
		$door_id_arr = array();
		foreach ($doorList as $val)
		{
			if($val['door_type']==1) $door_id_arr[] = $val['door_id'];
		}
		$data['charge'] = $data['real_money'] = 0;
		$sessionModel = ClsFactory::Create('Model.mSession');
		$end_data = $sessionModel->getSessionInfoByEndDoorTime($door_id_arr,$start_time,$end_time);
		if($end_data && isset($end_data[1]) && $end_data[1])
		{
			foreach ($end_data[1] as $val)
			{
				foreach ($val as $v)
				{
					$data['charge']+= $v['charge'];
					$data['real_money']+= $v['real_money'];
				}
			}
		}
		$data['useing_park_counts'] = 0;
		$data['card_type'][1] = 0;
		$data['card_type'][2] = 0;
		$data['card_type'][3] = 0;
		$data['card_type'][4] = 0;
	
		//车位及卡类型统计
		$start_data = $sessionModel->getSessionInfoByStartDoorTime(0,$end_time);
		if($start_data)
		{
			foreach ($start_data as $card_type=>$val)
			{
				$data['card_type'][$card_type] = count($val);
				if($card_type!=3)
				{
					$data['useing_park_counts'] += $data['card_type'][$card_type];
				}
				
			}
		}
		//获取总车位数
		$setingModel = ClsFactory::Create('Model.mSetting');
		
		$seting_data = $setingModel->getSettingInfo();
		$data['park_count'] = $seting_data['park_count'];
		$data['left_park_count'] = $seting_data['park_count'] - $data['useing_park_counts'];
		$this->returnData(1,'',$data);
	}
	
	private function returnData($code,$msg='',$resurnData='')
	{
		$data['code']     = $code;
		$data['msg']      = $msg;
		$data['data']     = $resurnData;
		echo json_encode($data);
		die;
	}
	
	private function getCardStatusStr($statusCode)
	{
	
		$arr[1] = '该卡不存在';
		$arr[2] = '该卡已经挂失';
		$arr[3] = '该卡已经回收';
		$arr[4] = '该卡欠费';
		$arr[5] = '该卡过期';
		return isset($arr[$statusCode]) ? $arr[$statusCode] : '';
	}
	
	private function getParkTime($start_time,$end_time)
	{
		$time = $end_time - $start_time;
		if($time < 60)
		{
			return '1分钟';
		}
		$hour = floor($time/3600);
		if(!$hour) $hour = 0;
		$hour.='小时<br>';
		$minute = ceil(($time-3600*$hour)/60);
		$minute.='分钟';
		return $hour.$minute;
	}

	/**
	 * 卡片类型
	 * @return multitype:string
	 */
	public static function getCardTypes($type=null)
	{
		$maps =  array(
				1 => '临时卡',
				2 => '储值卡',
				3 => '月租卡',
				4 => '贵宾卡'
		);
		return !empty($type)&&isset($maps[$type]) ? $maps[$type] : $maps;
	}
	/**
	 * 计算剩余天数
	 * @param int $expire_time
	 * @return number
	 */
	private function countLeftDays($expire_time)
	{
		$time = time();
		if($expire_time<=$time) return 0;
		return ceil( ($expire_time - $time)/86400);
	}
	
	public function addLog()
	{
		$data['function_name'] = $this->objInput->postStr('function_name');
		$data['param'] = $this->objInput->postStr('param');
		$data['return'] = str_ireplace('&quot;', '',$this->objInput->postStr('return'));
	
		$model = ClsFactory::Create('Model.mLog');
		if($model->addLog($data)) $this->returnData(1,'',$data);
		$this->returnData(0);
	}
	
	private function getParkTimeArr($start_time,$end_time)
	{
		$data = array('hour'=>0,'minute'=>0);
		$time = $end_time - $start_time;
		if($time < 60)
		{
			$data['minute'] = 1;
			return $data;
		}
		$hour = floor($time/3600);
		if(!$hour) $hour = 0;
		$data['hour'] = $hour;
		$minute = ceil(($time-3600*$hour)/60);
		$data['minute'] = $minute;
		return $data;
	}
	
	private function getParkTimeStr($start_time,$end_time,$showPage='index')
	{
		$timeArr = $this->getParkTimeArr($start_time, $end_time);
		if($showPage=='index')
		{
			return $timeArr['hour'].'小时'.$timeArr['minute'].'分钟';
		}elseif ($showPage=='sentry')
		{
			$str='';
			if($timeArr['hour']) $str='<span class="num">'.$timeArr['hour'].'</span>小时';
			if($timeArr['minute']) $str='<span class="num">'.$timeArr['minute'].'</span>分钟';
		}
	}
	/**
	 * 判断是否可以通过此门刷卡进入
	 * @param array $doorData
	 * @param array $cardInfo
	 * @return boolean
	 */
	private function checkSwipe($doorData,$cardInfo)
	{
		if(!$cardInfo) return false;
		$cardInfo['park'] = trim($cardInfo['park'],',');
		if(!$doorData['park_id']) return true;
		
		if($cardInfo['park']=='' && $doorData['park_id']== 0 ) return true;
		
		$cardId = $cardInfo['card_id'];
		$cardModel = ClsFactory::Create('Model.mCard');
		if($cardModel->hasShareParking($cardId))
		{
			$family_card_list = $cardModel->getFamilyCardList($cardId);
			if($family_card_list)
			{
				$park_id_str = '';
				foreach ($family_card_list as $f)
				{
					$park_id_str .= ','.$f['park'];
				}
				if (!empty($park_id_str) && in_array($doorData['park_id'], explode(',', $park_id_str))) {
					return true;
				}else
				{
					return false;
				}
			}
		}else
		{
			if(!in_array($doorData['park_id'], explode(',', $cardInfo['park']))) return false;
		}
		return true;
	}
	
	private function setNotice($code,$msg)
	{
		if($this->notice) return;
		$this->notice= array('code' => $code, 'msg' => $msg);
	}
	
	//--2013-03-20-----------------------
	public function getShareParkCardData()
	{
		$cardId = trim($this->objInput->postStr('card_id'));      //卡片id
		$doorId = trim($this->objInput->postStr('door_id'));  //控制器出入口类型
		if(!$cardId) $this->returnData(0,'!card_id');
		//获取控制器信息
		$doorModel = ClsFactory::Create('Model.mDoor');
		$doorData = $doorModel->getDoorById($doorId);
		$doorData = array_pop($doorData);
		if(!$doorData) $this->returnData(0,'!door data');
		$data = array();
		if ($doorData['door_type']==1)
		{
			//获取入口控制器的数据
			$cond = "door_name = '%s' and door_type = 0";
			$cond = sprintf($cond,$doorData['door_name']);
			$dDoor = ClsFactory::Create ( 'Data.dDoor' );
			$door = $dDoor->getDoorInfoByCond($cond);
			if(!$door) $this->returnData(0,'!door');
			$data['door'] = $door;
		}
		$cardModel = ClsFactory::Create('Model.mCard');
		if($cardModel->hasShareParking($cardId))
		{
			$sessionModel = ClsFactory::Create('Model.mSession');
			$_leftParkCounts = $sessionModel->getFamilyRemainParkingCount($cardId);
			if($_leftParkCounts ==0 || ($doorData['door_type'] == 1 && $_leftParkCounts == 1 ))
			{
				$familyCardList = $cardModel->getFamilyCardList($cardId);
				if(!$familyCardList) $this->returnData(0,'service no card list');
				//获取在场的停车场信息
				$session_list = $sessionModel->getFamilyParkSessionList($cardId);
				$cardList = array();
				foreach ($familyCardList as $card)
				{
					if(!isset($session_list[$card['card_id']])) $cardList[] = $card;
				}
				$data['cardList'] = $this->sortCard($cardList);
				if($data) $this->returnData(1,'door_type:'.$doorData['door_type'],$data);
			}
		}
		$this->returnData(0,'!share');
	}
	//-------------------
	
	private function sortCard($card_list)
	{
		$data = array();
		foreach ($card_list as $card)
		{
			$card_info = array();
			$card_info['code'] = $card['code'];
			$card_info['start_time'] = date('Y-m-d', time());
			$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
			$data[] = $card_info;
		}
		uasort($data, create_function('$a, $b', 'if (intval($a["code"]) == intval($b["code"])) { return 0; } return (intval($a["code"]) < intval($b["code"])) ? -1 : 1;'));
		return $data;
	}
}