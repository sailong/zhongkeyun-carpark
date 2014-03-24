<?php
class mPark extends mBase {
	
	public function getParkById($park_ids) {
		if (empty($park_ids))
			return false;
		$dPark = ClsFactory::Create ( 'Data.dPark' );
		$result = $dPark->getParkById($park_ids);
		
		return $result;
	}
	
	public function getParkInfo() {
		$dPark = ClsFactory::Create ( 'Data.dPark' );
		$result = $dPark->getParkInfo();
		
		return $result;
	}
	
	public function modifyParkInfo($park_info, $park_id) {
		if (!is_numeric($park_id) || empty($park_info))
			return false;
		$dPark = ClsFactory::Create ( 'Data.dPark' );
		$result = $dPark->modifyParkInfo($park_info, $park_id);
		
		return $result;
	}
	
	public function addPark($park_info) {
		if (empty($park_info))
			return false;
		
		$dPark = ClsFactory::Create ( 'Data.dPark' );
		$result = $dPark->addPark($park_info);
		
		return $result;
	}
	
	public function delParkInfo($park_id) {
		if (!is_numeric($park_id))
			return false;
		$dPark = ClsFactory::Create ( 'Data.dPark' );
		$result = $dPark->delParkInfo($park_id);
		
		return $result;
	}
	
}
