<?php
header('content-type:text/html;charset=utf-8');
// 加载框架入口文件
include_once (dirname(dirname ( __FILE__ )) . '/global.inc.php');
// 初始化项目
App::init ();

$model = ClsFactory::Create('Model.mCard');
$data = $model->getCardListByCond("address != '' and `status`!=2 ",0,0);
if(!$data) die('1 no card data');
//var_dump($data);
//去掉地址中的前后空格
foreach ($data as $d)
{
	$model->modifyCardInfo(array('address'=>trim($d['address'])),$d['card_id']);
}
//根据地址分组
$dCard = ClsFactory::Create ( 'Data.dCard' );
$data = $dCard->query("SELECT address FROM card WHERE address != '' and `status`!=2 and card_type = 2 GROUP BY address");
if(!$data) die('query no card data');
$masterArr = array();
foreach ($data as $d)
{
	//$card_list = $model->getFamilyCardList($d['card_id']);
	$card_list = $model->getCardListByCond('address="'.$d['address'].'" and status!=2 and card_type = 2', 0, 0);
	if(!$card_list) continue;
	
	$types = $subCardList = array();
	$master_id = 0;
	$master_code = '';
	foreach ($card_list as $card) {
	
			//随机设定一个主账户
			if(!$master_id)
			{
				$master_id = $card['card_id'];
				$master_code = $card['code'];
			}
		
	}
	//如果符合共享车位的条件
	if($master_id)
	{
		//计算其他储值卡余额
		$money = 0;
		$sub_id_arr = array();
		$remark_str='';
		foreach ($card_list as $sub)
		{
			if($sub['is_master'] || $sub['card_id'] == $master_id ) continue;
			$money+= $sub['money'];
			$subCardList[] = $sub['code'].'('.$sub['money'].'元)';
			$sub_id_arr[] = $sub['card_id'];
		}
		//设置为主账户
		$model->modifyCardInfo(array('is_master'=>1),$master_id);
		$masterArr[] = $master_code;
		//把余额挪进主账户
		if($money)
		{
			$param = array('card_id'=>$master_id,'charge'=>intval($money),'add_time'=>time(),'remark'=>'卡号为：'.implode(',',$subCardList).'的卡余额转移到主账户（系统设置主账户）,共转移'.$money.'元');
			if(ClsFactory::Create('Model.mRechargeLog')->addRechargeLog($param))
			{
				if($sub_id_arr)
				{
					//把子账户的余额设置为0
					$model->modifyCard(array('money'=>0),' card_id in ('.implode(',', $sub_id_arr).')');
				}
			}
		}
	}
}
echo '处理完毕,成功设置了'.count($masterArr).'个主账户.'.($masterArr ? '主账户内码为：'.implode(',',$masterArr) : '');