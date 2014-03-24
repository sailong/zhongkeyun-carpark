<?php
// 加载框架入口文件
include_once (dirname ( __FILE__ ) . '/global.inc.php');
// 初始化项目
App::init ();

// $user = ClsFactory::Create('Model.mAdmin');
// //$data = $user->getCurrentUser();
// $data = $user->checkCurrentUserLevel(101);
// var_dump($data);

//setting
// $mSetting = ClsFactory::Create('Model.mSetting');
// $setting_info = array();
// $setting_info['company_name'] = '云健康';
// $mSetting->modifySettingInfo($setting_info);
// var_dump($mSetting->getSettingInfo());


// $mFee = ClsFactory::Create('Model.mFee');
// // $setting_info = array();
// // $setting_info['company_name'] = '云健康';
// // $mSetting->modifySettingInfo($setting_info);
// // var_dump($mSetting->getSettingInfo());
// $result = $mFee->calculateFee(2, 63);
//var_dump($result);


// $card_info = array(
// 		'code' => '124343',
// 		'card_type' => 2,
// 		'expire_time' => time() + 3600*24*30,
// 		'money' => 100,
// 		'car_code' => '京P13434',
// 		'cate_id' => 2,
// 		'car_type' => 's40',
// 		'car_color' => '红色',
// 		'name' => '杨益',
// 		'person_id' => '42112619830711311X',
// 		'tel' => '18600805024',
// 		'address' => '北京市昌平区沙河镇于辛庄',
// 		'add_time' => time(),
// 		'parking' => 1,
// 		'park' => '1,2,3',
// 		'status' => 0,
// 		'charge' => 120,
// 		'remark' => '开卡'
// );
// $m = ClsFactory::Create('Model.mCard');
// //$m->addCard($card_info);
// $info = $m->getCardListByCond('card_id=1', 0, 24);
// var_dump($info);


// $m = ClsFactory::Create('Model.mIssueLog');
// $info = $m->getIssueLogByCond();
// var_dump($info);

// $log = array(
// 'card_id' => 1,
// 'expire_time' => time() + 3600*24*30 * 5,
// 'charge' => 30,
// 'add_time' => time(),
// 'remark' => '延期',
// );
// $m = ClsFactory::Create('Model.mDelayLog');
// $info = $m->batchAddDelayLog(array($log, $log, $log, $log));
// //$info = $m->getDelayLogByCond();
// var_dump($info);

//$m = ClsFactory::Create('Model.mCard');
//$m->addCard($card_info);
//$info = $m->getCardById(array(1, 2, 3));
//$info = $m->getFamilyCardList('043143');
//$info = $m->getFamilyCardList('3301');
//$info = $m->getMasterCardInfo('3301');
//$info = $m->getFamilyParkingCount('3301');
//$info = $m->hasShareParking(3301);
//var_dump($info);

//$m = ClsFactory::Create('Model.mSession');
//$m->addCard($card_info);
//$info = $m->manOpenDoor(1, 2);
//$info = $m->openDoor(3301, 12);
//$info = $m->manOpenDoor(1, 2);
//$info = $m->addExitPark(Array ( 'end_door_id' => 2, 'end_status' => 1, 'end_time' => 1387814400, 'charge' => 22, 'real_money' => 22, 'remark'=>'ffff' ), 1);
//$info = $m->getSessionInfoByEndDoorTime(2, 1387549213, 1387814401);
//$info = $m->lastSessionInfo(2674);
//$info = $m->getFamilyRemainParkingCount(3301);
//$info = $m->openDoor(120, 2);
//$info = $m->getSessionInfo(121);
//var_dump($info);


// $mAdmin = ClsFactory::Create('Model.mAdmin');
// $user_info = $mAdmin->getCurrentUser();
// var_dump($user_info);

//echo ltrim(trim('00100'), '0');




// function generateAccessToken()
// {
// 	$tokenLen = 40;
// 	if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
// 		$randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
// 	} else {
// 		$randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
// 	}
// var_dump(file_get_contents('/dev/urandom', false, null, 0, 100));
// 	return substr(hash('sha512', $randomData), 0, $tokenLen);
// }

// var_dump(generateAccessToken());



$a = array(1, 3);

foreach ($a as $v) {
	$a[] = $v;
}

var_dump($a);

