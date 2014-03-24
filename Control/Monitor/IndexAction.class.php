<?php
class IndexAction extends Controller {
	
	private $mAdmin;
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 401;
	
	public function _initialize() {
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) {
			$this->toLogin();
		}
		if (!$this->mAdmin->checkCurrentUserLevel($this->level))
		{
			$this->toWelcome();
		}
		$this->assign('nav','出入监控');
	}
	
	public function index(){
	
		$isAdministrator = false;
		$userInfo = $this->mAdmin->getCurrentUser();
		if($userInfo['group_id']==1)//管理员拥有最大权限
		{
			$isAdministrator = true;
			$doorModel = ClsFactory::Create('Model.mDoor');
			$doorList = $doorModel->getDoorInfo();
		}else
		{
			if(!isset($userInfo['door']) || empty($userInfo['door']))
			{
				$this->showMessage(0,'没有被授权控制器管理权限！');
			}
			$doorList = $userInfo['door'];
		}//var_dump($doorList);die;
		$carCategoryModel = ClsFactory::Create('Model.mCarCategory');
		$carCategoryList = $carCategoryModel->getCarCategory();
		$this->assign('carCategoryList',$carCategoryList);
		
		$carCateStr='<option value="0">请选择</option>';
		foreach ($carCategoryList as $cate)
		{
			$carCateStr.='<option value="'.$cate['cate_id'].'">'.$cate['cate_name'].'</option>';
		}
		$this->assign('carCateStr',$carCateStr);
		
		
		//---获取实际控制器列表-------------------------------------------------------------------------
		$realDoorList = $door_id_arr = $ledReleateDoorList = array();
		//用于刷卡区分
		$door_addr_readerNo_arr = array();
		foreach ($doorList as $key=>$val)
		{
			$val['reader_no'] = trim($val['reader_no'],',');
			if(stristr($val['reader_no'],',')!==false)//拆分
			{
				$r_arr = explode(',', $val['reader_no']);
				foreach ($r_arr as $reader_no)
				{
					
				}
			}else
			{
				
			}
			$sign = $val['door_addr'].'_'.$val['reader_no'];
			if($val['led_ip'])
			{
				$ledReleateDoorList[$sign] = array(
						'led_ip' => $val['led_ip'],
						'lane' => $val['lane'],
						'show_left_parking' => $val['show_left_parking']
				);
			}
		
			$doorList[$key]['unqiue_sign'] = $door_addr_readerNo_arr[] = $sign;
			$door_id_arr[] = $val['door_id'];
			//if(!isset($realDoorList[$val['door_addr']])) $realDoorList[$val['door_addr']] = $val;
			$realDoorList[] = $val;
		}//var_dump($doorList);
		
		
		$this->assign('realDoorListJson',$realDoorList ? json_encode($realDoorList) : '');
		$this->assign('v',time());
		
		//获取读卡器列表
		$this->assign('doorList',$doorList);
		$this->assign('doorJson',$doorList ? json_encode($doorList) : '');
		//-----------------------------------------------------------------------------------------
		$this->assign('isAdministrator',$isAdministrator);
		$this->assign('door_addr_readerNo',json_encode($door_addr_readerNo_arr));
		//--控制器参数---------------------------------------------------------------------------------------
		$controlData = array(
				//'mask' => MASK,
				//'gateway' => GATEWAY,
				'port' => PORT,
		);
		$this->assign('controlData',$controlData);
		if(in_array($userInfo['group_id'],array(2,8)))
		{
			//--led相关------------------------------------------
			$ledList = $this->filterLed($doorList);
			$this->assign('ledList',$ledList);
			$this->assign('ledListJson',json_encode($ledList));
			$this->assign('ledReleateDoorListJson',json_encode($ledReleateDoorList));
			//--------------------------------------------------
			$mSetting = ClsFactory::Create('Model.mSetting');
			$setting = $mSetting->getSettingInfo();
			$this->assign('setting',$setting);
			//---------------------------------------------------
			$this->assign('userInfo',$userInfo);
			$this->display('sentry_new');//岗亭页面
		}else
		{
			$this->display('index');
		}
	}
	
	public function test() {
		
		$this->display('test');
	}

	/**
	 * 过滤重复ip
	 * @param unknown_type $doorList
	 */
	private function filterLed($doorList)
	{
		if(!$doorList) return;
		$ipList = array();
		foreach ($doorList as $value) {
			if(!$value['led_ip']) continue;
			if(!isset($ipList[$value['led_ip']]))
			{
				$ipList[$value['led_ip']]['led_ip'] = $value['led_ip'];
				$ipList[$value['led_ip']]['lane'] = $value['lane'];
			}
		}
		return $ipList;
	}
}
