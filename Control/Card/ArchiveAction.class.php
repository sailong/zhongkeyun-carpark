<?php

import('Control.Card.CardController');

/**
 * 卡片档案
 * @author JZLJS00
 *
 */
class ArchiveAction extends CardController
{
	
	/**
	 * 模板前缀
	 * @var unknown_type
	 */
	private $tepPrefix = 'archive_';
	
	/**
	 * 角色权限码
	 */
	protected $level = 309;
	
	/**
	 * 档案列表
	 */
	public function index()
	{
		if(isset($_GET['daochu_x']))
		{
			$this->export();exit();	
		}
		$search = $this->getSearch(array('code','car_code','card_type','name','address','parking','card_status'));
		$offset = $this->getOffset();
		extract($search[1]);
		if($search[0])
		{
			$search[0].= ' and `status` != 2 ';
		}else
		{
			$search[0] = ' `status` != 2 ';
		}
		$data = ClsFactory::Create('Model.mCard')->getCardListByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mCard')->getCardListCount($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		//------------------------------------
		if($data)
		{
			$mCard= ClsFactory::Create('Model.mCard');
			foreach ($data as $key=>$val)
			{
				if($val['address'] && $val['money']==0 && $val['card_type_code'] == 2 && $val['is_master']==0 && $val['status']=='该卡欠费')
				{
					//判断是否属于子账户
					if($mCard->getMasterCardInfo($val['card_id']))
					{
						$data[$key]['status'] = '子账户';
					}
				}
			}
		}
		//------------------------------------
		$isSetMaster = false;
		if($searchType=='address' && $data)
		{
			foreach ($data as $val)
			{
				if($val['is_master'])
				{
					$isSetMaster = true;
					break;
				}
			}
		}
		$this->assign('data',$data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('paging',$paging);
		$this->assign('isSetMaster',$isSetMaster);
		//导出数据时的车库信息
		$park_info = ClsFactory::Create('Model.mPark')->getParkInfo();
		$this->assign('park_info', $park_info);
		//卡片类型
		$this->assign('cardTypeList', CardController::getCardTypes());
		
		$this->display($this->tepPrefix.'index');
	}
	
	/**
	 * 修改
	 */
	public function modify()
	{
		$data = $this->objInput->postArr('data');
	
		if(!empty($data))
		{
			$info = $this->getCardById($data['card_id']);
			if(empty($info))
			{
				$this->showMessage(0,'卡片不存在');
			}
			if ($info['card_type'] == 2 && $info['is_master'] && !$data['address'])
			{
				$this->showMessage(0,'联系地址不能为空！');
			}
			//------判断是否可以修改地址---------------------------
			if(isset($data['address']) && $data['address'] && $data['address'] !=$info['address'] )
			{
				$shareParking = ClsFactory::Create('Model.mCard')->hasShareParking($data['card_id']);
				if($shareParking) $this->showMessage(0,'修改失败：共享车位地址冲突,请重新发行！');
				$typeArr = $this->getCurrentCardTypes($data['address']);
				if($typeArr )
				{
					$typeCounts = count($typeArr);
					if($typeCounts > 1 || !isset($typeArr[$info['card_type']]))
					{
						$this->showMessage(0,'修改失败：该地址与其他共享车位地址冲突！');
					}
				}
			}
			//---------------------------------
			
            //$data['park'] = join(',',$data['park']);
            $cardModel = ClsFactory::Create('Model.mCard');
			if($cardModel->modifyCardInfo($data,$data['card_id']))
			{
				
				$this->showMessage(1,'修改成功！',__URL__.'/index');
			}else{
				$this->showMessage(0,'修改失败！');
			}
		}
		$id = $this->objInput->getStr('id');
		$data = $this->getCardById($id);
		if(empty($data))
		{
			$this->showMessage(0,'卡片不存在');
		}
		$park = array();
		if(!empty($data['park_info']))
		{
			foreach ($data['park_info'] as $value)
			{
				$park[] = $value['park_id'];
			}
		}
		$data['park'] = $park;
		$data['add_time'] = date('Y-m-d H:i:s',$data['add_time']);
		//-----------------
		$data['shareParing'] = ClsFactory::Create('Model.mCard')->hasShareParking($id) ;
		//----------------------------
		$carCategoryList = ClsFactory::Create('Model.mCarCategory')->getCarCategory();
		$parkCategoryList = ClsFactory::Create('Model.mPark')->getParkInfo();
		$cardCategoryList = self::getCardTypes();
		$this->assign('cardCategoryList',$cardCategoryList);
		$this->assign('carCategoryList',$carCategoryList);
		$this->assign('parkCategoryList',$parkCategoryList);
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'modify');
	}
	
	/**
	 * 导出卡片
	 */
	public function export()
	{
		$search = $this->getSearch(array('code','car_code','card_type','name','address','parking','card_status'));
		//-----------------------------------------
		if($search[0])
		{
			$search[0].= ' and `status` != 2 ';
		}else
		{
			$search[0] = ' `status` != 2 ';
		}
		//--------------------------------------------
		$total = ClsFactory::Create('Model.mCard')->getCardListCount($search[0]);
		$data = ClsFactory::Create('Model.mCard')->getCardListByCond($search[0],0,$total);
		$this->formatListData($data);
		$header = array('ID','卡片内码','卡片类别','有效期','卡片余额','车牌号码','车主姓名',
				'身份证号码','联系电话','联系地址','所属车位',
				'状态','操作员');
		array_walk($header, function(&$value){
			$value = mb_convert_encoding($value,'GBK', 'UTF-8');
		});
		include_once (WEB_ROOT_DIR . '/Common/HtmlExcel.class.php');
		$excel = new HtmlExcel();
		$excel->addHead($header);
		foreach ($data as $value)
		{
			$card_type = mb_convert_encoding($value['card_type'], 'GBK', 'UTF-8');
			$name = mb_convert_encoding($value['name'], 'GBK', 'UTF-8');
			$status = mb_convert_encoding($value['status'], 'GBK', 'UTF-8');
			$user_name = mb_convert_encoding($value['user_name'], 'GBK', 'UTF-8');
			$value['car_code'] = iconv("utf-8", "GBK", $value['car_code']);
			$value['address'] = iconv("utf-8", "GBK", $value['address']);
			$value['parking'] = iconv("utf-8", "GBK", $value['parking']);
			
			$row = array(
				$value['card_id'],$value['code'],$card_type,$value['expire_time'],
				$value['money'],$value['car_code'],$name,
				$value['person_id'],$value['tel'],$value['address'],$value['parking'],
				$status,$user_name
			);
			$excel->addRow($row);
		}
		$filename = date('Y-m-d-H-i',time());
		$excel->export($filename);
	}
	
	/*
	 * 根据车库层出卡片内码和有效期
	 */
	public function exportCardExcel()
	{
		$park_id = $this->objInput->getStr('park_id');
		
		$mCard = ClsFactory::Create('Model.mCard');
		$total = $mCard->getCardListCount();
		$card_list = $mCard->getCardListByCond('',0,$total);
		if (!$card_list) {
			$this->showMessage(0, '没有导出卡片数据');
		}
		
		$card_ids = array();
		foreach ($card_list as $card) {
			$card_ids[] = $card['card_id'];
		}
		
		uasort($card_list, create_function('$a, $b', 'if (intval($a["code"]) == intval($b["code"])) { return 0; } return (intval($a["code"]) < intval($b["code"])) ? -1 : 1;'));
		
		$card_status = $mCard->getCardStatusById($card_ids);
		
		$card_info = array();
		foreach ($card_list as $card) {
			
			//$hasShareParking = $mCard->hasShareParking($card['card_id']);
			//共享车位
// 			if (empty($pard_ids) || ($hasShareParking && $park_id !=0)) {
// 				continue;
// 			}

			$family_list = $mCard->getFamilyCardList($card['card_id']);
			$park_str = '';
			foreach ($family_list as $ca) {
				$park_str .= ','.$ca['park'];
			}
			$card['park'] = $park_str;
			
			$pard_ids = explode(',', $card['park']);
			
			if ($card_status[$card['card_id']] == 0 && $card['card_type'] != 1 && ($park_id == 0 || in_array($park_id, $pard_ids))) {
				$tmp = array();
				$tmp['code'] = $card['code'];
				$tmp['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
				$tmp['start_time'] = date('Y-m-d', time());
				$tmp['status'] = 1;
				$tmp['park'] = $card['park'];
				
				$card_info[] = $tmp;
			}
		}
		
// 		//导出excel
// 		header("content-type:application/vnd.ms-excel");
// 		header("content-disposition:attachment;filename=cardInfo_{$park_id}.xls" );
// 		foreach($card_info as $key=>$val) {
// 			foreach($val as $val1) {
// 				echo iconv("utf-8", "GBK",$val1)."\t";
// 			}
// 			echo "\n";
// 		}

		//写数据到控制器
		$this->showMessage(1, '同步数据中', __URL__.'/index', 1, $card_info);
	}
	
	/**
	 * 设置主账户
	 */
	public function setMaster()
	{
		$id = $this->objInput->getStr('card_id');
		if(!$id) return;
		$card = $this->getCardById($id);
		if (!$card || $card['status'] > 0)
		{
			echo json_encode(array('code'=>0,'msg'=>'处理失败：卡片信息不存在或该卡已被挂失或回收'));
			die;
		}
		if ($card['card_type'] != 2)
		{
			echo json_encode(array('code'=>0,'msg'=>'处理失败：非储值卡不能设置主账户'));
			die;
		}
		$cardModel = ClsFactory::Create('Model.mCard');
		$masterCard = $cardModel->getMasterCardInfo($id);
		if($masterCard)
		{
			echo json_encode(array('code'=>0,'msg'=>'处理失败：不能重复设置主账户'));
			die;
		}
		//设置主账户，转移余额
		//$cardModel->modifyCard(array('is_master'=>1)," card_id = ".$id);
		if($cardModel->modifyCardInfo(array('is_master'=>1),$id))
		{
			//获取副卡数据
			$subCardList = $this->getFamilySubCardList($id);
			if($subCardList)
			{
				$totalMoney = 0;
				$sub_id_arr = $sub_card_arr = array();
				foreach ($subCardList as $sub)
				{
					$totalMoney += $sub['money'];
					$sub_id_arr[] = $sub['card_id'];
					$sub_card_arr[] = $sub['code']; 
				}
				if($totalMoney)
				{
					//转移余额到主账户
					$param = array('card_id'=>$id,'charge'=>intval($totalMoney),'add_time'=>time(),'remark'=>'副卡 '.implode(',', $sub_card_arr).' 金额转移到主账户（设置主账户）,充值   '.$totalMoney.'元');
					if(ClsFactory::Create('Model.mRechargeLog')->addRechargeLog($param))
					{
						//更新副卡余额
						$cardModel->modifyCard(array('money'=>CardController::SUB_CARD_INIT_MONEY),' card_id in ('.implode(',', $sub_id_arr).')');
					}
				}
			}
			echo json_encode(array('code'=>1,'msg'=>'设置主账户成功'));
		}else
		{
			echo json_encode(array('code'=>0,'msg'=>'处理失败'));
		}
		die;
	}
	
	public function writeToController()
	{
		$id = $this->objInput->getStr('id');
		if(!$id) $this->showMessage(0,'参数错误');
		$card = $this->getCardById($id);
		if(!$card) $this->showMessage(0,'获取不到该卡片数据');
		if($card['card_type'] == 1) $this->showMessage(0,'临时卡不能写入控制器');
		/* $shareParking = ClsFactory::Create('Model.mCard')->hasShareParking($id);
		if($shareParking) $this->showMessage(0,'共享车位相关卡片不能写入控制器'); */
		$card_info = array();
		
				$card_info['code'] = $card['code'];
				$card_info['start_time'] = date('Y-m-d', time());
				$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
				$card_info['status'] = 1;
				$card_info['park'] = '10,';
		
		
		$this->showMessage(1,'处理中...',__URL__.'/index', 1, array($card_info));
	}
}
