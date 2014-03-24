<?php
class mDoor extends mBase {
	
	public function getDoorById($door_ids) {
		if (empty($door_ids))
			return false;
		$dDoor = ClsFactory::Create ( 'Data.dDoor' );
		$result = $dDoor->getDoorById($door_ids);
		
		return $result;
	}
	
	public function getDoorInfo() {
		$dDoor = ClsFactory::Create ( 'Data.dDoor' );
		$result = $dDoor->getDoorInfo();
		
		return $result;
	}
	
	public function getDoorInfoByAddrReader($addr, $readerNo) {
		$door_list = $this->getDoorInfo();
		foreach ($door_list as $door) {
			if ($door['door_addr'] == $addr) {
				$nos = explode(',', $door['reader_no']);
				if (in_array($readerNo, $nos)) {
					return $door;
				}
			}
		}
	
		return false;
	}
	
	public function modifyDoorInfo($door_info, $door_id) {
		if (!is_numeric($door_id) || empty($door_info))
			return false;
		$dDoor = ClsFactory::Create ( 'Data.dDoor' );
		$result = $dDoor->modifyDoorInfo($door_info, $door_id);
		
		return $result;
	}
	
	public function addDoor($door_info) {
		if (empty($door_info))
			return false;
		
		$dDoor = ClsFactory::Create ( 'Data.dDoor' );
		$result = $dDoor->addDoor($door_info);
		
		return $result;
	}
	
	public function delDoorInfo($door_id) {
		if (!is_numeric($door_id))
			return false;
		$dDoor = ClsFactory::Create ( 'Data.dDoor' );
		$result = $dDoor->delDoorInfo($door_id);
		
		return $result;
	}
	
}

    
