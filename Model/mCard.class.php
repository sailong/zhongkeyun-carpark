<?php
class mCard extends mBase {
	const CARD_PAGE_SIZE = 24;
	
	
	public function getCardById($card_ids) {
		if (empty($card_ids))
			return false;
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$result = $dCard->getCardById($card_ids);
		
		return $this->mixOtherCardInfo($result);
	}
	
	public function getCardByCode($codes) {
		if (empty($codes))
			return false;
		if (is_array($codes)) {
			foreach ($codes as &$code) {
				$code = ltrim(trim($code), '0');
			}
		} else {
			$codes = ltrim(trim($codes), '0');
		}
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$result = $dCard->getCardByCode($codes);
	
		return $this->mixOtherCardInfo($result);
	}
	
	/**
	 * 获取一张或者多张卡片的状态
	 * @return array('$card_id'=>$status_code，...)
	 * $status_code状态码含义
	 * 0 正常
	 * 1 该卡不存在
	 * 2 该卡已经挂失
	 * 3 该卡已经回收
	 * 4 该卡欠费
	 * 5 该卡过期
	 * 
	 */
	public function getCardStatusById($card_ids) {
		if (empty($card_ids))
			return false;
		
		$card_info = $this->getCardById($card_ids);
		if (!is_array($card_ids)) {
			$card_ids = array($card_ids);
		}
		
		$status_info = array();
		foreach ($card_ids as $card_id) {
			$card = $card_info[$card_id];
			$status = 0;
			if ($card == false) {
				$status = 1;
			} else {
				if ($card['status'] != 0) {
					switch ($card['status']) {
						case 1:
							$status = 2;
							break;
						case 2:
							$status = 3;
							break;
					}
				} else {
					switch ($card['card_type']) {
						//临时卡
						case 1:
							{
								if ($card['status'] != 2) {
									
								}
								break;
							}
						//储值卡
						case 2:
							{
								if ($card['money'] < 0) {
									$status = 4;
								}
								break;
							}
						//月租卡
						case 3:
						//贵宾卡
						case 4:
							{
								if ($card['expire_time'] < time()) {
									$status = 5;
									//过期后进行扣费
									if ($card['money'] > 0) {
										$this->modifyCardInfo(array('money'=>0), $card_id);
										//记录消费日志
										$mConsumeLog = ClsFactory::Create('Model.mConsumeLog');
										$consume_log = array();
										$consume_log['card_id'] = $card_id;
										$consume_log['session_id'] = 0;
										$consume_log['charge'] = $card['money'];
										$consume_log['admin_id'] = '0';
										$consume_log['user_name'] = 'system';
										$mConsumeLog->addConsumeLog($consume_log);
									}
								}
								break;
							}
					}
				}
			}
			
			$status_info[$card_id] = $status;
		}
		return $status_info;
	}
	
	/**
	 * 某卡的家庭车位数
	 * @return int 车位数 0没有购买车位
	 *
	 */
	public function getFamilyParkingCount($card_id) {
		$card_list = $this->getFamilyCardList($card_id);
		if (!$card_list) {
			return 0;
		}
		
		$count = 0;
		foreach ($card_list as $card) {
			if ($card['card_type'] > 2 && $card['card_type'] < 5 && $card['expire_time'] > time()) {
				$count ++;
			}
		}
		return $count;
	}
	
	/**
	 * 某卡的家庭所有成员的卡片信息
	 * @return mixed false没有在场停车信息，array返回该卡片的家庭卡片信息
	 *
	 */
	public function getFamilyCardList($card_id) {
		$card_info = $this->getCardById($card_id);
		if (!$card_info) {
			return false;
		}
		$card_info = array_pop($card_info);
	
		$address = trim($card_info['address']);
		if (empty($address)) {
			return false;
		}
	
		$card_list = $this->getCardListByCond('address="'.$card_info['address'].'" and status!=2', 0, 0);
		if (!$card_list) {
			return false;
		}
	
		return $card_list;
	}
	
	/**
	 * 某卡的家庭是否共享车位
	 * @return bool false没有 true有
	 *
	 */
	public function hasShareParking($card_id) {
		$card_list = $this->getFamilyCardList($card_id);
		
		$types = array();
		foreach ($card_list as $card) {
			if (!in_array($card['card_type'], $types) && $card['card_type'] != TEMPORARY_CARD_TYPE) {
				$types[] = $card['card_type'];
			}
		}
		
		return count($types) >1 ? true : false;
	}
	
	/**
	 * 某卡的家庭的主卡卡片信息
	 * @return mixed false没有在场停车信息，array返回该卡片的家庭主卡片信息
	 *
	 */
	public function getMasterCardInfo($card_id) {
		$card_list = $this->getFamilyCardList($card_id);
		if (!$card_list) {
			return false;
		}
		$card_info = array();
		foreach ($card_list as $card) {
			if ($card['is_master'] == 1 && $card['card_type'] == 2) {
				$card_info = $card;
				break;
			}
		}
	
		return empty($card_info) ? false : $card_info;
	}
	
	//$condition_str值：(card_id|car_code|card_type|name)='1|京p1343|2|杨益'，可以为空取出全部列表
	public function getCardListByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
// 		if (! $length)
// 			$length = self::CARD_PAGE_SIZE;
	
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$card_list = $dCard->getCardListByCond($condition_str, $offset, $length);
		
		return $this->mixOtherCardInfo($card_list);
	}
	
	public function getCardListCount($condition_str = '') {
	
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$count = $dCard->getCardListCount($condition_str);
	
		return $count;
	}
	
	private function mixOtherCardInfo($card_list) {
		$park_ids = array();
		$cate_ids = array();
		foreach ($card_list as $card) {
			$cate_ids[] = $card['cate_id'];
				
			if (empty($card['park'])) {
				continue;
			}
			$park_ids = array_merge($park_ids, explode(',', $card['park']));
		}
		
		//可停放停车场
		$mPark = ClsFactory::Create('Model.mPark');
		$park_info = $mPark->getParkById(array_unique($park_ids));
		//获取车辆类别信息
		$mCarCategory = ClsFactory::Create ('Model.mCarCategory');
		$car_category = $mCarCategory->getCarCategoryById(array_unique($cate_ids));
		foreach ($card_list as &$card) {
			$card['cate_name'] = $car_category[$card['cate_id']]['cate_name'];
				
			if (empty($card['park'])) {
				continue;
			}
			$ids = explode(',', $card['park']);
			foreach ($ids as $id) {
				$card['park_info'][$id] = $park_info[$id];
			}
		}
		
		return $card_list;
	}
	
	public function modifyCardInfo($card_info, $card_id) {
		if (!is_numeric($card_id) || empty($card_info))
			return false;
		if (isset($card_info['code'])) $card_info['code'] = ltrim(trim($card_info['code']), '0');
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$result = $dCard->modifyCardInfo($card_info, $card_id);
		if ($card_info['issue_id']) {
			$mAdmin = ClsFactory::Create('Model.mAdmin');
			$admin_info = $mAdmin->getCurrentUser();
			$card_info['admin_id'] = $admin_info['admin_id'];
			$card_info['user_name'] = $admin_info['admin_name'];
			
			$mIssueLog = ClsFactory::Create ('Model.mIssueLog');
			$result1 = $mIssueLog->modifyIssueLogInfo($card_info, $card_info['issue_id']);
		}
		
		return $result || $result1;
	}
	
	public function addCard($card_info) {
		if (empty($card_info))
			return false;
		$card_info['add_time'] = time();
		$card_info['code'] = $code = ltrim(trim($card_info['code']), '0');
		//没有相同的家庭地址时，将该储值卡设为主卡
		$card_list = $this->getCardListByCond('address="'.$card_info['address'].'" and card_type=2 and status !=2', 0, 0);
		if (!$card_list && $card_info['card_type'] == 2) {
			$card_info['is_master'] = 1;
		} else {
			$card_info['is_master'] = 0;
		}
		//var_dump($card_info);die;
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$result = $dCard->addCard($card_info);
		if ($result) {
			$mIssueLog = ClsFactory::Create ('Model.mIssueLog');
			$mAdmin = ClsFactory::Create('Model.mAdmin');
			$admin_info = $mAdmin->getCurrentUser();
			$card_info['card_id'] = $result;
			$card_info['admin_id'] = $admin_info['admin_id'];
			$card_info['user_name'] = $admin_info['admin_name'];
			$mIssueLog->addIssueLog($card_info);
		}
		
		return $result;
	}
	
	public function delCardInfo($card_id) {
		if (!is_numeric($card_id))
			return false;
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		$result = $dCard->delCardInfo($card_id);
		
		$mIssueLog = ClsFactory::Create ('Model.mIssueLog');
		$issue_log = $mIssueLog->getIssueLogByCond('card_id='.$card_id, 0, 10);
		foreach (array_keys($issue_log) as $log_id) {
			$mIssueLog->delIssueLogInfo($log_id);
		}
		
		return $result;
	}
	
	/**
	 * 卡片更改
	 */
	public function modifyCard($card_info, $condition) {
	
		$dCard = ClsFactory::Create ( 'Data.dCard' );
		return $dCard->modifyCard($card_info, $condition);
	}
}
