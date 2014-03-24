<?php
class mMessageLog extends mBase {
	
	public function add($log_info) {
		if (empty( $log_info )) return false;
		$log_info['send_at'] = date('Y-m-d H:i:s');
		$model = ClsFactory::Create ( 'Data.dMessageLog' );
		$result = $model->add($log_info);
		return $result;
	}
}

    
