<?php
class mCarCategory extends mBase {
	
	public function getCarCategoryById($cate_ids) {
		if (empty($cate_ids))
			return false;
		$dCarCategory = ClsFactory::Create ( 'Data.dCarCategory' );
		$result = $dCarCategory->getCarCategoryById($cate_ids);
		return $result;
	}
	
	public function getCarCategory() {
		$dCarCategory = ClsFactory::Create ( 'Data.dCarCategory' );
		$result = $dCarCategory->getCarCategory();
		return $result;
	}
	
	public function modifyCarCategory($car_category, $cate_id) {
		if (!is_numeric($cate_id) || empty($car_category))
			return false;
		$dCarCategory = ClsFactory::Create ( 'Data.dCarCategory' );
		$result = $dCarCategory->modifyCarCategory($car_category, $cate_id);
		
		return $result;
	}
	
	public function addCarCategory($car_category) {
		if (empty($car_category))
			return false;
		$dCarCategory = ClsFactory::Create ( 'Data.dCarCategory' );
		$result = $dCarCategory->addCarCategory($car_category);
		return $result;
	}
	
	public function delCarCategory($cate_id) {
		if (!is_numeric($cate_id))
			return false;
		$dCarCategory = ClsFactory::Create ( 'Data.dCarCategory' );
		$result = $dCarCategory->delCarCategory($cate_id);
		return $result;
	}
	
}

    
