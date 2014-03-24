<?php
class mLog extends mBase {
	
	public function addLog($log_info) {
		if (empty( $log_info ))
			return false;
		$log_info['create_time'] = date('Y-m-d H:i:s');
		$model = ClsFactory::Create ( 'Data.dLog' );
		$result = $model->addLog($log_info);
		return $result;
	}
}

    
