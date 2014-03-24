<?php
class mFee extends mBase {
	
	//获取收费列表
	public function getFeeById($fee_ids) {
		if (empty($fee_ids))
			return false;
		$dFee = ClsFactory::Create ( 'Data.dFee' );
		$result = $dFee->getFeeById($fee_ids);
		if (!$result) {
			return false;
		}
		
		$mCarCategory = ClsFactory::Create ('Model.mCarCategory');
		$cate_ids = array();
		foreach ($result as $info) {
			$cate_ids[] = $info['cate_id']; 
		}
		$car_category = $mCarCategory->getCarCategoryById(array_unique($cate_ids));
		foreach ($result as &$info) {
			$info['cate_name'] = $car_category[$info['cate_id']]['cate_name'];
		}
		
		return $result;
	}
	
	public function calculateFee($cate_id, $time) {
		$fee_list = $this->getFeeInfo();
		$fee_info = array();
		foreach ($fee_list as $fee) {
			if ($fee['cate_id'] == $cate_id) {
				$fee_info = $fee;
			}
		}
		
		if (empty($fee_info)) {
			return 0;
		}
		
		$days = floor(floatval($time) / (60 * 60 * 24));
		$time -= $days * (60 * 60 *24);
		$day_money = $days * $fee_info['max_money'];
		
		//免费停车时间
		if ($time <= $fee_info['free_time'] * 60) {
			return 0 + $day_money;
		}
		
		//至少停车时间
		if ($time <= $fee_info['start_time'] * 3600) {
			return $fee_info['start_money'] + $day_money;
		}
		
		$after_money = ceil((floatval($time) - $fee_info['start_time'] * 3600) / ($fee_info['step_time'] * 3600)) * $fee_info['step_money'];
		$money = $after_money + $fee_info['start_money'];
		
		return ($money >= $fee_info['max_money'] ? $fee_info['max_money'] : $money) + $day_money;
	}
	
	public function getFeeInfo() {
		$dFee = ClsFactory::Create ( 'Data.dFee' );
		$result = $dFee->getFeeInfo();
		if (!$result) {
			return false;
		}
		
		$mCarCategory = ClsFactory::Create ('Model.mCarCategory');
		$cate_ids = array();
		foreach ($result as $info) {
			$cate_ids[] = $info['cate_id'];
		}
		$car_category = $mCarCategory->getCarCategoryById(array_unique($cate_ids));
		foreach ($result as &$info) {
			$info['cate_name'] = $car_category[$info['cate_id']]['cate_name'];
		}
		
		return $result;
	}
	
	public function modifyFeeInfo($fee_info, $fee_id) {
		if (!is_numeric($fee_id) || empty($fee_info))
			return false;
		$dFee = ClsFactory::Create ( 'Data.dFee' );
		$result = $dFee->modifyFeeInfo($fee_info, $fee_id);
		
		return $result;
	}
	
	public function addFee($fee_info) {
		if (empty($fee_info))
			return false;
		
		$dFee = ClsFactory::Create ( 'Data.dFee' );
		$result = $dFee->addFee($fee_info);
		
		return $result;
	}
	
	public function delFeeInfo($fee_id) {
		if (!is_numeric($fee_id))
			return false;
		$dFee = ClsFactory::Create ( 'Data.dFee' );
		$result = $dFee->delFeeInfo($fee_id);
		
		return $result;
	}
	
}

    
