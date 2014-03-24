<?php
class dSetting extends dBase {
	// 表名
	protected $name = 'setting';
	protected $pk = '';
	protected $_fields = array (
			'setting' => array (
			'company_name',
			'capture_stay',
			  'del_capture',
			  'gocome_stay',
			  'del_gocome',
			  'work_stay',
			  'del_work',
			  'park_count',
			  'code_len',
			  'least_money',
			  'image_contrast',
			  'voice_tips',
			  'display_carcode',
			  'entrance_image_addr',
			  'export_image_addr',
			  'entrance_reader',
			  'export_reader',
			  'card_type',
			  'is_issue_check',
			  'repeat_entrance',
			  'repeat_export',
			  'nontemp_contrast',
			  'abb',
			  'warn_days',
			  'is_far_ready',
			  'seconds1',
			  'artificial_brake',
			  'display_carcode1',
			  'print_ticket',
			  'record_artificial',
			  'repeat_park',
			  'is_send_sms',
			  'allow_share_parking_into'
			)
	);
	public function _initialize() {
	}
	
	public function addSetting($setting_info) {
		if (empty ( $setting_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $setting_info, 'setting' );
		$sql = "insert into setting set $setsql";
		$result = $this->execute ( $sql );
		return $result ? true : false;
	}
	
	public function modifySettingInfo($setting_info) {
		if (empty ( $setting_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $setting_info, 'setting' );
		$result = $this->execute ( "update setting set $setsql" );
		return $result;
	}
	
	public function getSettingInfo() {
		$list = $this->query ( "select * from setting" );
		if (! $list)
			return false;
		
		return array_pop($list);
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'setting') {
		if (empty ( $dataarr ) || empty ( $table )) {
			return false;
		}
		$dataarr = is_array ( $dataarr ) ? $dataarr : array (
				$dataarr 
		);
		$dataarr = $this->checkFields ( $dataarr, $this->_fields [$table] );
		$arr = array ();
		foreach ( $dataarr as $key => $value ) {
			$arr [] = "`$key`='$value'";
		}
		if (! empty ( $arr )) {
			$str = implode ( ',', $arr );
		}
		return $str ? $str : false;
	}
}
