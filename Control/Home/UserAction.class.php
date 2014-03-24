<?php
class UserAction extends Controller {
	public function _initialize() {
	}
	
	public function index()
	{
		$this->signup();
	}
	
	public function signup()
	{
		include_once (CONFIGE_DIR . '/resource.php');
		$height_arr = range(140, 220);
		$weight_arr = range(30, 125);
		
		$this->assign('height_arr', $height_arr);
		$this->assign('weight_arr', $weight_arr);
		$this->assign('edu', $CON_EDU);
		$this->assign('revenue', $CON_REVENUE);
		
		$this->display('register');
	}
	
	public function register()
	{
		$mobile = $this->objInput->postStr ('mobile');
		$sex = $this->objInput->postInt ('sex');
		$name = $this->objInput->postStr ('name');
		$nick_name = $this->objInput->postStr ('nick_name');
		$YYYY = $this->objInput->postInt ('YYYY');
		$MM = $this->objInput->postInt ('MM');
		$DD = $this->objInput->postInt ('DD');
		$height = $this->objInput->postInt ('height');
		$weight = $this->objInput->postInt ('weight');
		$edu = $this->objInput->postInt ('edu');
		$job = $this->objInput->postStr ('job');
		$revenue = $this->objInput->postInt ('revenue');
		$has_house = $this->objInput->postInt ('has_house');
		$has_car = $this->objInput->postInt ('has_car');
		$introduce = $this->objInput->postStr('introduce');
		
		$user_info = array();
		$user_info['mobile'] = $mobile;
		$user_info['sex'] = $sex;
		$user_info['name'] = $name;
		$user_info['nick_name'] = $nick_name;
		$user_info['birthday'] = strtotime(sprintf("%d-%d-%d", $YYYY, $MM, $DD));
		$user_info['height'] = $height;
		$user_info['weight'] = $weight;
		$user_info['edu'] = $edu;
		$user_info['job'] = $job;
		$user_info['revenue'] = $revenue;
		$user_info['has_house'] = $has_house;
		$user_info['has_car'] = $has_car;
		$user_info['introduce'] = $introduce;
		$user_info['status'] = 1;
		
		$up_init = array (
				'attachmentspath' => WEB_ROOT_DIR . '/attachment',
				'ifresize' => true,
				'resize_width' => 200,
				'resize_height' => 200,
				'cut' => 1
		);
		import ( "Libraries.uploadfile" );
		$upload = new uploadfile ( $up_init );
		$upload->allow_type = explode ( ",", 'jpg,gif,png' );
		$file = $upload->upfile ( 'avatar' );
		if (empty ( $file )) {
			$this->displayError ( 20010 );
		}
		$user_info ['avatar'] = '/' . str_replace ( WEB_ROOT_DIR . '/', '', $file ['getfilename'] );
		if ($file ['getsmallfilename']) {
			$user_info ['club_logo'] = '/' . str_replace ( WEB_ROOT_DIR . '/', '', $file ['getsmallfilename'] );
		}
		
		$mUser = ClsFactory::Create('Model.mUser');
		$result = $mUser->addUser($user_info);
		if ($result) {
			header("location:/user/audit/mobile/".$mobile);
		} else {
			echo '注册失败';
		}
	}
	
	public function audit()
	{
		$mobile = $this->objInput->getStr('mobile');
		$this->assign('mobile', $mobile);
		$this->display('audit');
	}
	
}
