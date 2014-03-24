<?php

function insert_user_state($params = array(), & $smarty) {
	
	$mAdmin = ClsFactory::Create ( 'Model.mAdmin' );
	$admin_info = $mAdmin->getCurrentUser();
	$smarty->assign ( 'admin_info', $admin_info );
	echo $smarty->fetch ( "./Public/user_state.html" );
}

/**
 * 系统设置菜单栏目
 * @param unknown_type $params
 * @param unknown_type $smarty
 */
function insert_setting_nav($params = array(), & $smarty) {

	$navArr = array(
					'base'          => array('name'=>'基本设置', 'url'=>'/setting/setting/view/item/base'),
					'communication' => array('name'=>'通讯参数', 'url'=>'/setting/setting/view/item/communication'),
					'door'          => array('name'=>'控制器参数','url'=>'/setting/door'),
					//'data'  		=> array('name'=>'数据维护', 'url'=>'/setting/data'),
					'CarCategory'	=> array('name'=>'车型管理', 'url'=>'/setting/CarCategory'),
					'fee'		    => array('name'=>'设置收费', 'url'=>'/setting/fee'),
					'password'      => array('name'=>'修改密码', 'url'=>'/setting/setting/password'),
			);
	$str = '';
	foreach ($navArr as $key=>$val)
	{
		$class = '';
		if($key==$params['id']) $class='class="nav_cur"';
		$str.='<li '.$class.'><a href="'.$val['url'].'">'.$val['name'].'</a></li>';
	}
	echo $str;
}


/**
 * 档案管理菜单栏目
 * @param unknown_type $params
 * @param unknown_type $smarty
 */
function insert_archives_nav($params = array(), & $smarty) {

	$navArr = array(
			'group'         => array('name'=>'管理组权限', 'url'=>'/Archives/group'),
			'admin' 	    => array('name'=>'管理组人员', 'url'=>'/Archives/admin'),
			'park'          => array('name'=>'停车场管理', 'url'=>'/Archives/park'),
	);
	$str = '';
	foreach ($navArr as $key=>$val)
	{
		$class = '';
		if($key==$params['id']) $class='class="nav_cur"';
		$str.='<li '.$class.'><a href="'.$val['url'].'">'.$val['name'].'</a></li>';
	}
	echo $str;
}

/**
 * 卡片管理
 * @param unknown_type $params
 * @param unknown_type $smarty
 */
function insert_sub_nav($params = array(), & $smarty) {

	$groupName = strtolower(substr(__GROUP__, 1));
	$id = $params['id'];
	$model = ClsFactory::Create('Model.mMenu');
	$menuList = $model->getMenuList($groupName);
	if($menuList)
	{
		$str = '';
		foreach ($menuList as $key=>$val)
		{
			$class = '';
			if($val['controller']==$id) $class='class="nav_cur"';
			$val['url'] = '/'.$groupName.'/'.$val['controller'];
			$str.='<li '.$class.'><a href="'.$val['url'].'">'.$val['name'].'</a></li>';
		}
	}
	echo $str;
}

