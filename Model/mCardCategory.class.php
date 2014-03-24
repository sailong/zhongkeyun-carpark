<?php
class mCardCategory extends mBase {
	
	
	/**
	 * 卡片类型
	 * @return multitype:string
	 */
	public function getCardCategory($type=null)
	{
		$maps =  array(
				1 => '临时卡',
				2 => '储值卡',
				3 => '月租卡',
				4 => '贵宾卡'
		);
		return !empty($type)&&isset($maps[$type]) ? $maps[$type] : $maps;
	}
	
}

    
