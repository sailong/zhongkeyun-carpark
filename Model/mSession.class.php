<?php
class mSession extends mBase {
	const SESSION_PAGE_SIZE = 24;
	
	public function getSessionById($session_ids) {
		if (empty($session_ids))
			return false;
		$dSession = ClsFactory::Create ( 'Data.dSession' );
		$result = $dSession->getSessionById($session_ids);
		
		return $this->mixOtherSession($result);
	}
	
	public function  mixOtherSession($session_list) {
		$card_ids = array();
		foreach ($session_list as &$session) {
			if (!in_array($session['card_id'], $card_ids)) {
				$card_ids[] = $session['card_id'];
			}
		}
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = $mCard->getCardById($card_ids);
		foreach ($session_list as &$session) {
			$session = array_merge($session, $card_info[$session['card_id']]);
		}
		return $session_list;
	}
	
	public function addExitPark($session_info, $card_id) {
		if (!is_numeric($card_id) || empty($session_info))
			return false;
		
		//卡片轻微检测
		$mCard = ClsFactory::Create('Model.mCard');
		$status_info = $mCard->getCardStatusById($card_id);
		$status_code = $status_info[$card_id];
		//如果卡片不存在，开门失败
		if ($status_code == 1) {
			return false;
		}
		
		$isSessionInfo = $this->isSessionInfo($card_id);
		if (!$isSessionInfo) {
			return false;
		}
		
		//门号检测
		$mDoor = ClsFactory::Create('Model.mDoor');
		$door_info = $mDoor->getDoorById($session_info['end_door_id']);
		if (!$door_info) {
			return false;
		}
		$door_info = array_pop($door_info);
		
		if ($door_info['door_type'] == 0) {
			return false;
		}
		
		$session_info['end_status'] = 1;
		
		//停车根据车型应该扣除费用计算
		$card_info = $mCard->getCardById($card_id);
		$card_info = array_pop($card_info);
		switch ($card_info['card_type']) {
			//临时卡
			case 1:
				{
					//回收卡片
					$recover_log = array();
					$recover_log['remark'] = '临时卡片出口人工开闸时回收';
					$recover_log['code'] = $card_info['code'];
					$recover_log['card_id'] = $card_id;
					$recover_log['money'] = $card_info['money'];
					$recover_log['real_money'] = 0;
					$mRecoverLog = ClsFactory::Create('Model.mRecoverLog');
					$mRecoverLog->addRecoverLog($recover_log);
					break;
				}
				//储值卡
			case 2:
				{
					break;
				}
				//月租卡
			case 3:
				//贵宾卡
			case 4:
				{
					break;
				}
		}
		
		$result = $this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
		if ($result) {
			//扣费
			$card = array();
			$card['money'] = $card_info['money'] - $session_info['real_money'];
			if ($card['money'] != $card_info['money']) $result = $mCard->modifyCardInfo($card, $card_id);
			
			//记录消费日志
			$mConsumeLog = ClsFactory::Create('Model.mConsumeLog');
			$consume_log = array();
			$consume_log['card_id'] = $card_id;
			$consume_log['session_id'] = $isSessionInfo['session_id'];
			$consume_log['charge'] = $session_info['real_money'];
			$consume_log['remark'] = $session_info['remark'];
			$mConsumeLog->addConsumeLog($consume_log);
		}
		
		return $result;
	}
	
	/**
	 * 智能开门
	 * @return boolean true开门成功，false开门失败
	 *
	 */
	public function openDoor($card_id, $door_id) {
		//卡片轻微检测
		$mCard = ClsFactory::Create('Model.mCard');
		$status_info = $mCard->getCardStatusById($card_id);
		$status_code = $status_info[$card_id];
		
		$card_info = $mCard->getCardById($card_id);
		if (!$card_info) {
			return false;
		}
		$card_info = array_pop($card_info);
		
		//如果卡片不正常，开门失败,欠费情况在此处忽略
		if (!in_array($status_code, array(0, 4))) {
			return false;
		}
		//检测卡片余额信息
		$master_card_info = $mCard->getMasterCardInfo($card_id);
		if ($card_info['card_type'] == PREPAID_CARD_TYPE && $master_card_info['money'] <= 0) {
			return  false;
		}
		
		//门号检测
		$mDoor = ClsFactory::Create('Model.mDoor');
		$door_info = $mDoor->getDoorById($door_id);
		if (!$door_info) {
			return false;
		}
		$door_info = array_pop($door_info);
		
		$result = false;
		$isSessionInfo = $this->isSessionInfo($card_id);//在场信息
		
		//车已经进场，检测是否能进出某车库
		$hasShareParking = $mCard->hasShareParking($card_id);
		if ($isSessionInfo) {
			if ($door_info['park_id']) {
				//进入车库门
				if ($door_info['door_type'] == 0) {
					if ($hasShareParking) {
						if ($this->getFamilyRemainParkingCount($card_id)) {
							$family_card_list = $mCard->getFamilyCardList($card_id);
							$park_id_str = '';
							foreach ($family_card_list as $card) {
								$park_id_str .= ','.$card['park'];
							}
							if (!empty($park_id_str) && in_array($door_info['park_id'], explode(',', $park_id_str))) {
								$session_info = array();
								$session_info['park_id'] = $door_info['park_id'];
								$session_info['park_status'] = 0;
								$this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
								//return true;
								return false;
							}
						};
					} else {
						if ($card_info['park'] && in_array($door_info['park_id'], explode(',', $card_info['park']))) {
							return false;
						}
					}
				} else {
					if ($hasShareParking) {
						if ($isSessionInfo['park_id'] > 0) {
							$session_info = array();
							$session_info['park_status'] = 1;
							$this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
						}
						
						return false;
					}
				}
				
				return false;
			}
		}
		
		if ($door_info['door_type'] == 0) {
			if ($isSessionInfo) {
				$result = false;
			} else {
				$session_info = array();
				$session_info['card_id'] = $card_id;
				$session_info['start_door_id'] = $door_id;
				$session_info['start_time'] = time();
				$session_info['start_status'] = 0;
				
				//家庭在场停车车位共享信息，阶梯收费，入场登记收费类型
				$parking_count = $mCard->getFamilyParkingCount($card_id);
				$session_list = $this->getFamilySessionList($card_id);
				$start_no = count($session_list) - $parking_count + 1;
				if ($card_info['card_type'] > TEMPORARY_CARD_TYPE) {
					if ($start_no > 1) {
						if ($start_no == 2 && C('SECOND_CATE_ID')) {
							$session_info['new_cate_id'] = C('SECOND_CATE_ID');
						} elseif ($start_no == 3 && C('THIRD_CATE_ID')) {
							$session_info['new_cate_id'] = C('THIRD_CATE_ID');
						} else {
							if (C('THIRD_CATE_ID'))
								$session_info['new_cate_id'] = C('THIRD_CATE_ID');
						}
					} else {
						if ($start_no < 1 && $card_info['card_type'] == PREPAID_CARD_TYPE) {
							$session_info['new_cate_id'] = -1;
						}
					}
				}
				//---------------------------end-----------------------
				
				$result = $this->addSession($session_info);
				//临时卡系统开门
				if ($card_info['card_type'] == TEMPORARY_CARD_TYPE) {
					return true;
				}
			}
		} else {
			if ($isSessionInfo) {
				$session_info = array();
				$session_info['end_door_id'] = $door_id;
				$session_info['end_time'] = time();
				$session_info['end_status'] = 0;
		
				//停车根据车型应该扣除费用计算
				$card_info = $mCard->getCardById($card_id);
				$card_info = array_pop($card_info);
				switch ($card_info['card_type']) {
					//临时卡
					case 1:
						{
							$result = false;
							//break;//临时卡刷卡计费
						}
					//储值卡
					case 2:
					//月租卡
					case 3:
					//贵宾卡
					case 4:
						{
							//费用计算
							$lastSessionInfo = $this->lastSessionInfo($card_id);//上次停车信息
							
							$new_cate_id = $isSessionInfo['new_cate_id'];
							if ($new_cate_id > 0) {
								if ($lastSessionInfo && $session_info['end_time'] - $lastSessionInfo['start_time'] <= 24 * 3600) {
									$session_info['charge'] = 0;
								} else {
									$mFee = ClsFactory::Create('Model.mFee');
									$cate_id = $isSessionInfo['new_cate_id'];
									$money = $mFee->calculateFee($cate_id, $session_info['end_time'] - $isSessionInfo['start_time']);
									$session_info['charge'] = $money;
								}
								
							} elseif ($new_cate_id == 0) {	
								if ($card_info['card_type'] == 2 || $card_info['card_type'] == 1) {
									if ($lastSessionInfo && $session_info['end_time'] - $lastSessionInfo['start_time'] <= 24 * 3600) {
										$session_info['charge'] = 0;
									} else {
										$mFee = ClsFactory::Create('Model.mFee');
										$cate_id = $card_info['cate_id'];
										$money = $mFee->calculateFee($cate_id, $session_info['end_time'] - $isSessionInfo['start_time']);
										$session_info['charge'] = $money;
									}
								} else {
									$session_info['charge'] = 0;
								}
							} elseif ($new_cate_id == -1) {
								$session_info['charge'] = 0;
							}
							$session_info['real_money'] = $session_info['charge'];
							$result = $this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
							break;
						}
				}
				
				if ($result) {
					//扣费
					$card = array();
					$card['money'] = $master_card_info['money'] - $session_info['real_money'];
					if ($card['money'] != $master_card_info['money']) $result = $mCard->modifyCardInfo($card, $master_card_info['card_id']);
					
					//记录消费日志
					$mConsumeLog = ClsFactory::Create('Model.mConsumeLog');
					$consume_log = array();
					$consume_log['card_id'] = $card_id;
					$consume_log['session_id'] = $isSessionInfo['session_id'];
					$consume_log['charge'] = $session_info['real_money'];
					$consume_log['admin_id'] = '0';
					$consume_log['user_name'] = 'system';
					$mConsumeLog->addConsumeLog($consume_log);
				}
			} else {
				$result = false;
			}
		}
		if ($hasShareParking && $master_card_info) {
			return $result ? false : false;
		}
		return $result ? false : false;
	}
	
	/**
	 * 人工开门
	 * @param $real_money 可选参数，出门时的实收金额，进门时无此参数
	 * @return boolean true开门成功，false开门失败
	 *
	 */
	public function manOpenDoor($card_id, $door_id, $real_money = 0) {
		if (!is_numeric($real_money) || $real_money < 0) {
			return false;
		}
		
		//卡片轻微检测
		$mCard = ClsFactory::Create('Model.mCard');
		$status_info = $mCard->getCardStatusById($card_id);
		$status_code = $status_info[$card_id];
		//如果卡片不存在，开门失败
		if ($status_code == 1) {
			return false;
		} 
		
		$card_info = $mCard->getCardById($card_id);
		if (!$card_info) {
			return false;
		}
		$card_info = array_pop($card_info);
		
		//门号检测
		$mDoor = ClsFactory::Create('Model.mDoor');
		$door_info = $mDoor->getDoorById($door_id);
		if (!$door_info) {
			return false;
		}
		$door_info = array_pop($door_info);
		
		$result = false;
		$isSessionInfo = $this->isSessionInfo($card_id);
		
		//车已经进场，检测是否能进出某车库
		if ($isSessionInfo) {
			if ($door_info['park_id']) {
				//进入车库门
				$hasShareParking = $mCard->hasShareParking($card_id);
				if ($door_info['door_type'] == 0) {
					if ($hasShareParking) {
						if ($this->getFamilyRemainParkingCount($card_id)) {
							$family_card_list = $mCard->getFamilyCardList($card_id);
							$park_id_str = '';
							foreach ($family_card_list as $card) {
								$park_id_str .= ','.$card['park'];
							}
							if (!empty($park_id_str) && in_array($door_info['park_id'], explode(',', $park_id_str))) {
								$session_info = array();
								$session_info['park_id'] = $door_info['park_id'];
								$session_info['park_status'] = 0;
								$this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
								return false;
							}
						};
					} else {
						if ($card_info['park'] && in_array($door_info['park_id'], explode(',', $card_info['park']))) {
							
							return false;
						}
					}
				} else {
					if ($hasShareParking) {
						if ($isSessionInfo['park_id'] > 0) {
							$session_info = array();
							$session_info['park_status'] = 1;
							$this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
						}
						return false;
					}
				}
		
				return false;
			}
		}
		
		if ($door_info['door_type'] == 0) {
			//
			if ($isSessionInfo) {
				$result = false;
			} else {
				$session_info = array();
				$session_info['card_id'] = $card_id;
				$session_info['start_door_id'] = $door_id;
				$session_info['start_time'] = time();
				$session_info['start_status'] = 1;
				
				//家庭在场停车信息，阶梯收费，入场登记收费类型
				if ($card_info['card_type'] > TEMPORARY_CARD_TYPE) {
					$session_list = $this->getFamilySessionList($card_id);
					foreach ($session_list as $key=>$session) {
						if ($session['new_cate_id'] <= 0) {//删除不是阶梯收费的session
							unset($session_list[$key]);
						}
					}
					$start_no = $session_list ? count($session_list) + 1 : 1;
					if ($start_no > 1) {
						if ($start_no == 2 && C('SECOND_CATE_ID')) {
							$session_info['new_cate_id'] = C('SECOND_CATE_ID');
						} elseif ($start_no == 3 && C('THIRD_CATE_ID')) {
							$session_info['new_cate_id'] = C('THIRD_CATE_ID');
						} else {
							if (C('THIRD_CATE_ID'))
								$session_info['new_cate_id'] = C('THIRD_CATE_ID');
						}
					}
				}
				//---------------------------end-----------------------
				
				$result = $this->addSession($session_info);
			}
		} else {
			if ($isSessionInfo) {
				$session_info = array();
				$session_info['end_door_id'] = $door_id;
				$session_info['end_time'] = time();
				$session_info['end_status'] = 1;
				$session_info['real_money'] = $real_money;
				
				//停车根据车型应该扣除费用计算
				$card_info = $mCard->getCardById($card_id);
				$card_info = array_pop($card_info);
				switch ($card_info['card_type']) {
					//临时卡
					case 1:
						{
							//费用计算
							$mFee = ClsFactory::Create('Model.mFee');
							$money = $mFee->calculateFee($card_info['cate_id'], $session_info['end_time'] - $isSessionInfo['start_time']);
							$session_info['charge'] = $money;
							//回收卡片
// 							$recover_log = array();
// 							$recover_log['remark'] = '临时卡片出口人工开闸时回收';
// 							$recover_log['code'] = $card_info['code'];
// 							$recover_log['card_id'] = $card_id;
// 							$recover_log['money'] = $card_info['money'];
// 							$recover_log['real_money'] = 0;
// 							$mRecoverLog = ClsFactory::Create('Model.mRecoverLog');
// 							$mRecoverLog->addRecoverLog($recover_log);
							break;
						}
					//储值卡
					case 2:
					//月租卡
					case 3:
					//贵宾卡
					case 4:
						{
							//费用计算
							$lastSessionInfo = $this->lastSessionInfo($card_id);//上次停车信息
								
							$new_cate_id = $isSessionInfo['new_cate_id'];
							
							if ($lastSessionInfo && $session_info['end_time'] - $lastSessionInfo['start_time'] <= 24 * 3600) {
								$session_info['charge'] = 0;
							} else {
								$mFee = ClsFactory::Create('Model.mFee');
								$cate_id = $new_cate_id > 0 ? $isSessionInfo['new_cate_id'] : $card_info['cate_id'];
								$money = $mFee->calculateFee($cate_id, $session_info['end_time'] - $isSessionInfo['start_time']);
								$session_info['charge'] = $money;
							}
							break;
						}
				}
				
				$result = $this->modifySessionInfo($session_info, $isSessionInfo['session_id']);
				if ($result) {
					//临时不收费时使用，扣费
					$master_card_info = $mCard->getMasterCardInfo($card_id);
					$card = array();
					$card['money'] = $master_card_info['money'] - $session_info['real_money'];
					if ($card['money'] != $master_card_info['money'] && $card_info['card_type'] == TEMPORARY_CARD_TYPE) $result = $mCard->modifyCardInfo($card, $card_id);
					
					//记录消费日志
					$mConsumeLog = ClsFactory::Create('Model.mConsumeLog');
					$consume_log = array();
					$consume_log['card_id'] = $card_id;
					$consume_log['session_id'] = $isSessionInfo['session_id'];
					$consume_log['charge'] = $real_money;
					$mConsumeLog->addConsumeLog($consume_log);
				}
			} else {
				$result = false;
			}
		}
		return $result ? true : false;
	}
	
	/**
	 * 是否刷卡进入停车场
	 * @return mixed false该卡片没有进入，array返回该卡片session信息
	 *
	 */
	public function isSessionInfo($card_id) {
		$session_info = $this->getSessionByCond('card_id='.$card_id.', start_time desc', 0, 1);
		if ($session_info == false) {
			return false;
		}
		$session_info = array_pop($session_info);
		if (is_null($session_info['end_status'])) {
			return $session_info;
		}
		return false;
	}
	
	/**
	 * 获取当次停车信息
	 * @return mixed false该卡片没有进入，array返回该卡片session信息
	 *
	 */
	public function currentSessionInfo($card_id) {
		$session_info = $this->getSessionByCond('card_id='.$card_id.', start_time desc', 0, 1);
		if ($session_info == false) {
			return false;
		}
		$session_info = array_pop($session_info);
		
		return $session_info;
	}
	
	/**
	 * 某卡上次收费停车信息
	 * @return mixed false该卡片没有进入，array返回该卡片session信息
	 *
	 */
	public function lastSessionInfo($card_id) {
		$session_info = $this->getSessionByCond('card_id='.$card_id.' and end_status is not null and real_money>0, start_time desc', 0, 1);
		if ($session_info == false) {
			return false;
		}
		$session_info = array_pop($session_info);
		
		return empty($session_info) ? false : $session_info;
	}
	
	/**
	 * 某卡的家庭的空闲车位数
	 * @return int 0表示已经没有空闲车位了
	 *
	 */
	public function getFamilyRemainParkingCount($card_id) {
		$session_list = $this->getFamilySessionList($card_id);
		
		$current_count = 0;
		foreach ($session_list as $session) {
			if (intval($session['park_id']) && intval($session['park_status']) == 0) {
				$current_count ++;
			}
		}
		
		$mCard = ClsFactory::Create('Model.mCard');
		$count = $mCard->getFamilyParkingCount($card_id);
		
		return $count - $current_count > 0 ? $count - $current_count : 0;
	}
	
	/**
	 * 某卡的家庭在场停车场信息
	 * @return mixed false没有在场停车信息，array返回该卡片的家庭卡片session信息
	 *
	 */
	public function getFamilySessionList($card_id) {
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = $mCard->getCardById($card_id);
		if (!$card_info) {
			return false;
		}
		$card_info = array_pop($card_info);
		
		$address = trim($card_info['address']);
		if (empty($address)) {
			return false;
		}
		
		$card_list = $mCard->getCardListByCond('address="'.$card_info['address'].'"', 0, 0);
		if (!$card_list) {
			return false;
		}
		
		$session_list = array();
		foreach ($card_list as $card) {
			$isSessionInfo = $this->isSessionInfo($card['card_id']);
			if ($isSessionInfo) {
				$session_list[$card['card_id']] = array_merge($isSessionInfo, $card);
			}
		}
		
		return empty($session_list) ? false : $session_list;
	}
	
	/**
	 * 某卡的家庭在场车库停车场信息
	 * @return mixed false没有在场车库停车信息，array返回该卡片的家庭卡片session信息
	 *
	 */
	public function getFamilyParkSessionList($card_id) {
		$session_list = $this->getFamilySessionList($card_id);
		
		$result = array();
		foreach ($session_list as $session) {
			if (intval($session['park_id']) && intval($session['park_status']) == 0) {
				$result = $session;
			}
		}
		
		return count($result) ? $result : false;
	}
	
	/**
	 * 获取卡片收费信息
	 * @return mixed false该卡片没有进入，array返回该卡片session收费信息
	 *
	 */
	public function getSessionInfo($card_id) {
		$isSessionInfo = $this->isSessionInfo($card_id);
		if (!$isSessionInfo) {
			return false;
		}
		
		$isSessionInfo['end_time'] = time();
		$mFee = ClsFactory::Create('Model.mFee');
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = $mCard->getCardById($card_id);
		$card_info = array_pop($card_info);
		
		$status_info = $mCard->getCardStatusById($card_id);
		$status_code = $status_info[$card_id];
		
		//正常卡片扣费
		$master_card_info = $mCard->getMasterCardInfo($card_id);
		$lastSessionInfo = $this->lastSessionInfo($card_id);//上次停车信息
		if (($card_info['card_type'] == PREPAID_CARD_TYPE && $master_card_info['money'] > 0) || ($card_info['card_type'] != PREPAID_CARD_TYPE && $status_code == 0)) {
			$new_cate_id = $isSessionInfo['new_cate_id'];
			if ($new_cate_id > 0) {
				if ($lastSessionInfo && $isSessionInfo['end_time'] - $lastSessionInfo['start_time'] <= 24 * 3600) {
					$money = 0;
				} else {
					$mFee = ClsFactory::Create('Model.mFee');
					$cate_id = $isSessionInfo['new_cate_id'];
					$money = $mFee->calculateFee($cate_id, $isSessionInfo['end_time'] - $isSessionInfo['start_time']);
				}
			
			} elseif ($new_cate_id == 0) {
				if ($card_info['card_type'] == 2 || $card_info['card_type'] == 1) {
					if ($lastSessionInfo && $isSessionInfo['end_time'] - $lastSessionInfo['start_time'] <= 24 * 3600) {
						$money = 0;
					} else {
						$mFee = ClsFactory::Create('Model.mFee');
						$cate_id = $card_info['cate_id'];
						$money = $mFee->calculateFee($cate_id, $isSessionInfo['end_time'] - $isSessionInfo['start_time']);
					}
				} else {
					$money = 0;
				}
			} elseif ($new_cate_id == -1) {
				$money = 0;
			}
		} else {
			if ($lastSessionInfo && $isSessionInfo['end_time'] - $lastSessionInfo['start_time'] <= 24 * 3600) {
				$money = 0;
			} else {
				$cate_id = $isSessionInfo['new_cate_id'] > 0 ? $isSessionInfo['new_cate_id'] : $card_info['cate_id'];
				$money = $mFee->calculateFee($cate_id, $isSessionInfo['end_time'] - $isSessionInfo['start_time']);
			}
		}
		$isSessionInfo['charge'] = $money;
		
		return $isSessionInfo;
	}
	
	/**
	 * 获取某些门(岗亭)的指定时间段内的停车收费统计信息
	 * @return mixed false该卡片没有进入，array返回该卡片session收费信息
	 *
	 */
	public function getSessionInfoByEndDoorTime($door_ids, $start_time, $end_time) {
		if (empty($door_ids) || $start_time < 0 || $end_time < $start_time) {
			return false;
		}
		
		if (!is_array($door_ids)) {
			$door_ids = array($door_ids);
		}
		$session_list = $this->getSessionByCond('end_door_id in ('.implode(',', $door_ids).') and end_time >'.$start_time.' and end_time <'.$end_time.', end_time desc', 0, 0);
		
		if (!$session_list) {
			return false;
		}
		
		$result = array();
		
		$result = array();
		foreach ($session_list as $session) {
			$result[$session['card_type']][$session['session_id']] = $session;
		}
		
		return $result;
	}
	
	/**
	 * 获取指定时间段内的在场停车统计信息
	 * @return mixed false该卡片没有进入，array返回该卡片session收费信息
	 *
	 */
	public function getSessionInfoByStartDoorTime($start_time, $end_time) {
		if ($start_time < 0 || $end_time < $start_time) {
			return false;
		}
		
		$session_list = $this->getSessionByCond('start_time >'.$start_time.' and start_time <'.$end_time.' and end_status IS NULL, start_time desc', 0, 99999);
		if (!$session_list) {
			return false;
		}
	
		$result = array();
		foreach ($session_list as $session) {
			$result[$session['card_type']][$session['session_id']] = $session;
		}
	
		return $result;
	}
	
	//$condition_str值：筛选条件可以为表中一个或多个字段条件组合，排序条件只能为start_time和end_time的升序或降序
	//筛选条件在前，排序条件在后，中间用逗号分隔
	//如"card_id=$ and start_time > $ and start_time<$, start_time desc"
	//筛选条件可以为空取出全部列表
	public function getSessionByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		//if (! $length)
			//$length = self::SESSION_PAGE_SIZE;
	
		$dSession = ClsFactory::Create ( 'Data.dSession' );
		$session_list = $dSession->getSessionByCond($condition_str, $offset, $length);
	
		return $this->mixOtherSession($session_list);
	}
	
	//$condition_str同getSessionByCond的$condition_str
	public function getSessionCount($condition_str = '') {
	
		$dSession = ClsFactory::Create ( 'Data.dSession' );
		$count = $dSession->getSessionCount($condition_str);
	
		return $count;
	}
	
	public function modifySessionInfo($session_info, $session_id) {
		if (!is_numeric($session_id) || empty($session_info))
			return false;
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$session_info['admin_id'] = $admin_info['admin_id'];
		$session_info['user_name'] = $admin_info['admin_name'];
		
		$dSession = ClsFactory::Create ( 'Data.dSession' );
		$result = $dSession->modifySessionInfo($session_info, $session_id);
		
		return $result;
	}
	
	private function addSession($session_info) {
		if (empty($session_info))
			return false;
		
		$dSession = ClsFactory::Create ( 'Data.dSession' );
		$result = $dSession->addSession($session_info);
		
		return $result;
	}
	
	public function delSessionInfo($session_id) {
		if (!is_numeric($session_id))
			return false;
		$dSession = ClsFactory::Create ( 'Data.dSession' );
		$result = $dSession->delSessionInfo($session_id);
		
		return $result;
	}
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getChargeSum($field,$condition_str = '') {
	
		$model= ClsFactory::Create ( 'Data.dSession' );
		$sum = $model->getChargeSum($field,$condition_str);
		return $sum ? $sum : '0';
	}
	
}
