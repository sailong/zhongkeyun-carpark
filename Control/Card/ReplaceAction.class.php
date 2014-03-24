<?php
import('Control.Card.CardController');

/**
 * 卡片更换
 * @author JZLJS00
 *
 */
class ReplaceAction extends CardController
{
	
	private $tepPrefix = 'replace_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 307;
	
	
	/**
	 * 更换列表
	 */
	public function index()
	{
		$search = $this->getSearch(array('code','add_time','user_name'));
		extract($search[1]);
		$offset = $this->getOffset();
		$data = ClsFactory::Create('Model.mChangeLog')->getChangeLogByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mChangeLog')->modifyChangeLogInfo($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('data',$data);
		$this->assign('paging',$paging);
		$this->display($this->tepPrefix.'index');
	}
	
	/**
	 * 增加更换
	 */
	public function add()
	{
		$input = $this->objInput->postArr('data');
		$data = array();
		if(!empty($input))
		{
			$data = $this->getCardByCode($input['code']);
			if(empty($data))
			{
				$this->showMessage(0,'卡片不存在','index');
			}
			if(!$this->checkReplaceAble())
			{
				$this->showMessage(0,$this->_error);
			}
			if($input['new_code'] == $input['code'])
			{
				$this->showMessage(0,'怎么能换同一张卡！','add');
			}
			if(isset($input['new_code'],$input['remark']))
			{
				$new_code = $input['new_code'];
				if(!$this->checkCanReplace($new_code))
				{
					$this->showMessage(0,$this->_error);
				}
				$param = array('card_id'=>$data['card_id'],'new_code' => $new_code,'old_code'=>$input['code'],'charge'=>$input['charge'],'add_time'=>time(),'remark'=>trim($input['remark']));
				if(ClsFactory::Create('Model.mChangeLog')->addChangeLog($param)) {
					
					$mCard = ClsFactory::Create('Model.mCard');
					$card = array_pop($mCard->getCardById($data['card_id']));
					$card_info = array();
					if ($card['card_type'] != 1) {
						$card_old = array();
						$card_old['code'] = $input['code'];
						$card_old['start_time'] = date('Y-m-d', time());
						$card_old['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
						$card_old['status'] = 0;
						$card_old['park'] = $data['park'];
						
						$card_new = $card_old;
						$card_new['code'] = $new_code;
						$card_new['status'] = 1;
						
						$card_info = array($card_old, $card_new);
					}
					
					$this->showMessage(1,'更换成功',__URL__ . '/index', 1, $card_info);
				}
				else
					$this->showMessage(0,'更换失败');
			}
		}
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'add');
	}
	
}