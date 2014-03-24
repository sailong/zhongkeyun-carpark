<?php
class Constancearr {
	public static function stop_flag() {
		return array (
				- 1 => '未激活',
				0 => '未冻结',
				1 => '有时间冻结',
				2 => '永久冻结' 
		);
	}
	public static function phone_status() {
		return array (
				0 => '欠费',
				1 => '正常' 
		);
	}
	public static function business_enable() {
		return array (
				0 => '未去营业厅开通账号（手机号）',
				1 => '已开通手机',
				2 => '已取消手机业务' 
		);
	}
	public static function bj() {
		return array (
				array (
						'type' => '1',
						'name' => '小学' 
				),
				array (
						'type' => '2',
						'name' => '初中' 
				),
				array (
						'type' => '3',
						'name' => '高中' 
				) 
		);
	}
	
	/* 评语类型 */
	public static function pytype() {
		return array (
				1 => '存在问题',
				2 => '特长',
				3 => '交际',
				4 => '班务',
				5 => '学习情况',
				6 => '作业情况',
				7 => '成绩情况',
				8 => '课堂表现',
				9 => '总体印象',
				10 => '希望' 
		)
		;
	}
	public static function pytypeatt() {
		return array (
				1 => '优秀',
				2 => '良好',
				3 => '还需努力' 
		);
	}
	
	/**
	 * 获取学校的类型信息
	 * 
	 * @param
	 *        	$key
	 */
	public static function schooltype($key = false) {
		$dataarr = array (
				1 => '小学',
				2 => '初中',
				3 => '高中' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return $returnstr ? $returnstr : "暂无";
		}
		
		return false;
	}
	public static function qx() {
		return array (
				array (
						'id' => '0',
						'name' => '完全公开' 
				),
				array (
						'id' => '1',
						'name' => '仅好友可见' 
				),
				array (
						'id' => '2',
						'name' => '仅自己可见' 
				) 
		);
	}
	public static function mbquestion() {
		return array (
				array (
						'id' => '0',
						'name' => '您印象最深刻的老师名字是' 
				),
				array (
						'id' => '1',
						'name' => '您的小学校名是' 
				),
				array (
						'id' => '2',
						'name' => '您父亲的生日是' 
				),
				array (
						'id' => '3',
						'name' => '您母亲的生日是' 
				),
				array (
						'id' => '4',
						'name' => '您最重要纪念日子是' 
				),
				array (
						'id' => '5',
						'name' => '您最喜欢的运动员的名字是' 
				),
				array (
						'id' => '6',
						'name' => '您的宠物的名字是' 
				) 
		);
	}
	public static function clienttype() {
		return array (
				array (
						'id' => '0',
						'name' => '学生' 
				),
				array (
						'id' => '1',
						'name' => '老师' 
				),
				array (
						'id' => '2',
						'name' => '家长' 
				),
				array (
						'id' => '3',
						'name' => '专家' 
				),
				array (
						'id' => '4',
						'name' => '系统' 
				),
				array (
						'id' => '5',
						'name' => '学校管理员' 
				) 
		);
	}
	public static function client_type($key = false) {
		$dataarr = array (
				0 => '学生',
				1 => '老师',
				2 => '家长',
				3 => '专家',
				4 => '系统',
				5 => '学校管理员' 
		);
		
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	public static function abc() {
		return array (
				array (
						'name' => 'A' 
				),
				array (
						'name' => 'B' 
				),
				array (
						'name' => 'C' 
				),
				array (
						'name' => 'D' 
				),
				array (
						'name' => 'E' 
				),
				array (
						'name' => 'F' 
				),
				array (
						'name' => 'G' 
				),
				array (
						'name' => 'H' 
				),
				array (
						'name' => 'J' 
				),
				array (
						'name' => 'K' 
				),
				array (
						'name' => 'L' 
				),
				array (
						'name' => 'M' 
				),
				array (
						'name' => 'N' 
				),
				array (
						'name' => 'O' 
				),
				array (
						'name' => 'P' 
				),
				array (
						'name' => 'Q' 
				),
				array (
						'name' => 'R' 
				),
				array (
						'name' => 'S' 
				),
				array (
						'name' => 'T' 
				),
				array (
						'name' => 'W' 
				),
				array (
						'name' => 'X' 
				),
				array (
						'name' => 'Y' 
				),
				array (
						'name' => 'Z' 
				) 
		);
	}
	public static function classleader($key = false) {
		$dataarr = array (
				1 => '学生',
				2 => '班长',
				3 => '学委',
				4 => '体委',
				5 => '纪委' 
		);
		if ($key === false) {
			return $dataarr;
		} else {
			if (isset ( $dataarr [$key] ) && ! empty ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
		}
		return $returnstr ? $returnstr : "暂无";
	}
	public static function resourcestype() {
		return array (
				array (
						'type' => '1',
						'name' => '市重点' 
				),
				array (
						'type' => '2',
						'name' => '区重点' 
				),
				array (
						'type' => '3',
						'name' => '普通校' 
				),
				array (
						'type' => '4',
						'name' => '其他' 
				) 
		);
	}
	public static function school_resource_advantage($key = false) {
		$dataarr = array (
				1 => '市重点',
				2 => '区重点',
				3 => '普通校',
				4 => '其他' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return $returnstr ? $returnstr : "暂无";
		}
		return false;
	}
	
	/**
	 * 教师职称
	 */
	public static function teacher_clienttitle() {
		return array (
				array (
						'id' => 1,
						'name' => '初级教师' 
				),
				array (
						'id' => 2,
						'name' => '中级教师' 
				),
				array (
						'id' => 3,
						'name' => '高级教师' 
				),
				array (
						'id' => 4,
						'name' => '特级教师' 
				) 
		);
	}
	/**
	 * 教师职称
	 */
	public static function client_title($key = false) {
		$dataarr = array (
				1 => '初级教师',
				2 => '中级教师',
				3 => '高级教师',
				4 => '特级教师' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	/**
	 * 教师职务
	 */
	public static function teacher_clientjob() {
		return array (
				array (
						'id' => 1,
						'name' => '校长' 
				),
				array (
						'id' => 2,
						'name' => '副校长' 
				),
				array (
						'id' => 3,
						'name' => '教导主任' 
				),
				array (
						'id' => 4,
						'name' => '教研组长' 
				),
				array (
						'id' => 5,
						'name' => '无' 
				) 
		);
	}
	
	/**
	 * 教师职务
	 */
	public static function client_job($key = false) {
		$dataarr = array (
				1 => '校长',
				2 => '副校长',
				3 => '教导主任',
				4 => '教研组长',
				5 => '无' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	
	/**
	 * 用户血型
	 */
	public static function clientbloodtype() {
		return array (
				array (
						'id' => 1,
						'name' => 'A型' 
				),
				array (
						'id' => 2,
						'name' => 'B型' 
				),
				array (
						'id' => 3,
						'name' => 'AB型' 
				),
				array (
						'id' => 4,
						'name' => 'O型' 
				) 
		);
	}
	
	/**
	 * 用户血型
	 */
	public static function client_bloodtype($key = false) {
		$dataarr = array (
				1 => 'A型',
				2 => 'B型',
				3 => 'AB型',
				4 => 'O型' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	
	/*
	 * 家长行业
	 */
	public static function clienttrade() {
		return array (
				array (
						'id' => 1,
						'name' => '计算机/互联网/通信/电子' 
				),
				array (
						'id' => 2,
						'name' => '会计/金融/银行/保险' 
				),
				array (
						'id' => 3,
						'name' => '贸易/消费/制造/营运' 
				),
				array (
						'id' => 4,
						'name' => '制药/医疗' 
				),
				array (
						'id' => 5,
						'name' => '广告/媒体' 
				),
				array (
						'id' => 6,
						'name' => '房地产/建筑' 
				),
				array (
						'id' => 7,
						'name' => '专业服务/教育/培训' 
				),
				array (
						'id' => 8,
						'name' => '服务业' 
				),
				array (
						'id' => 9,
						'name' => '物流/运输' 
				),
				array (
						'id' => 10,
						'name' => '能源/原材料' 
				),
				array (
						'id' => 11,
						'name' => '政府/非营利机构/其他' 
				) 
		);
	}
	
	/**
	 * 家长行业
	 */
	public static function client_trade($key = false) {
		$dataarr = array (
				1 => '计算机/互联网/通信/电子',
				2 => '会计/金融/银行/保险',
				3 => '贸易/消费/制造/营运',
				4 => '制药/医疗',
				5 => '广告/媒体',
				6 => '房地产/建筑',
				7 => '专业服务/教育/培训',
				8 => '服务业',
				9 => '物流/运输',
				10 => '能源/原材料',
				11 => '政府/非营利机构/其他' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	
	/**
	 * 年级信息
	 */
	public static function grade_id() {
		return array (
				array (
						'id' => 1,
						'name' => '小学一年级' 
				),
				array (
						'id' => 2,
						'name' => '小学二年级' 
				),
				array (
						'id' => 3,
						'name' => '小学三年级' 
				),
				array (
						'id' => 4,
						'name' => '小学四年级' 
				),
				array (
						'id' => 5,
						'name' => '小学五年级' 
				),
				array (
						'id' => 6,
						'name' => '小学六年级' 
				),
				array (
						'id' => 7,
						'name' => '初中一年级' 
				),
				array (
						'id' => 8,
						'name' => '初中二年级' 
				),
				array (
						'id' => 9,
						'name' => '初中三年级' 
				),
				array (
						'id' => 10,
						'name' => '高中一年级' 
				),
				array (
						'id' => 11,
						'name' => '高中二年级' 
				),
				array (
						'id' => 12,
						'name' => '高中三年级' 
				),
				array (
						'id' => 13,
						'name' => '初中四年级' 
				) 
		);
	}
	
	/**
	 * 年级信息
	 */
	public static function class_grade_id($key = false) {
		$dataarr = array (
				1 => '小学一年级',
				2 => '小学二年级',
				3 => '小学三年级',
				4 => '小学四年级',
				5 => '小学五年级',
				6 => '小学六年级',
				7 => '初中一年级',
				8 => '初中二年级',
				9 => '初中三年级',
				10 => '高中一年级',
				11 => '高中二年级',
				12 => '高中三年级' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	
	/**
	 * 亲属关系类型
	 */
	public static function familyrelationtype() {
		return array (
				array (
						'id' => 1,
						'name' => '母亲' 
				),
				array (
						'id' => 2,
						'name' => '父亲' 
				),
				array (
						'id' => 3,
						'name' => '爷爷' 
				),
				array (
						'id' => 4,
						'name' => '奶奶' 
				),
				array (
						'id' => 5,
						'name' => '外公' 
				),
				array (
						'id' => 6,
						'name' => '外婆' 
				),
				array (
						'id' => 7,
						'name' => '其他亲属' 
				) 
		);
	}
	
	/**
	 * 亲属关系类型
	 */
	public static function family_relationtype($key = false) {
		$dataarr = array (
				1 => '母亲',
				2 => '父亲',
				3 => '爷爷',
				4 => '奶奶',
				5 => '外公',
				6 => '外婆',
				7 => '其他亲属' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	
	/**
	 * 生肖
	 */
	public static function clientzodiac() {
		return array (
				array (
						'id' => 1,
						'name' => '鼠' 
				),
				array (
						'id' => 2,
						'name' => '牛' 
				),
				array (
						'id' => 3,
						'name' => '虎' 
				),
				array (
						'id' => 4,
						'name' => '兔' 
				),
				array (
						'id' => 5,
						'name' => '龙' 
				),
				array (
						'id' => 6,
						'name' => '蛇' 
				),
				array (
						'id' => 7,
						'name' => '马' 
				),
				array (
						'id' => 8,
						'name' => '羊' 
				),
				array (
						'id' => 9,
						'name' => '猴' 
				),
				array (
						'id' => 10,
						'name' => '鸡' 
				),
				array (
						'id' => 11,
						'name' => '狗' 
				),
				array (
						'id' => 12,
						'name' => '猪' 
				) 
		);
	}
	
	/**
	 * 生肖
	 */
	public static function client_zodiac($key = false) {
		$dataarr = array (
				1 => '鼠',
				2 => '牛',
				3 => '虎',
				4 => '兔',
				5 => '龙',
				6 => '蛇',
				7 => '马',
				8 => '羊',
				9 => '猴',
				10 => '鸡',
				11 => '狗',
				12 => '猪' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : '暂无';
		}
		return false;
	}
	
	/**
	 * 星座
	 */
	public static function clientconstellation($id = false) {
		return array (
				array (
						'id' => 1,
						'name' => '白羊座' 
				),
				array (
						'id' => 2,
						'name' => '金牛座' 
				),
				array (
						'id' => 3,
						'name' => '双子座' 
				),
				array (
						'id' => 4,
						'name' => '巨蟹座' 
				),
				array (
						'id' => 5,
						'name' => '狮子座' 
				),
				array (
						'id' => 6,
						'name' => '处女座' 
				),
				array (
						'id' => 7,
						'name' => '天秤座' 
				),
				array (
						'id' => 8,
						'name' => '天蝎座' 
				),
				array (
						'id' => 9,
						'name' => '射手座' 
				),
				array (
						'id' => 10,
						'name' => '魔蝎座' 
				),
				array (
						'id' => 11,
						'name' => '水瓶座' 
				),
				array (
						'id' => 12,
						'name' => '双鱼座' 
				) 
		);
	}
	/**
	 * *
	 * 扩展资料中的性格
	 */
	public static function clienttemperament() {
		return array (
				'ym' => '幽默',
				'lg' => '乐观',
				'nx' => '内向',
				'wx' => '外向',
				'js' => '谨慎',
				'dd' => '胆大',
				'lm' => '浪漫',
				'ka' => '可爱',
				'ps' => '朴实',
				'mt' => '腼腆',
				'cm' => '聪明',
				'zy' => '正义',
				'sl' => '善良',
				'qt' => '其他' 
		);
	}
	/**
	 * *
	 * 扩展资料中的兴趣
	 */
	public static function clienthobby() {
		return array (
				'sf' => '书法',
				'hh' => '绘画',
				'lq' => '乐器',
				'wyx' => '玩游戏',
				'kdhp' => '看动画片',
				'cg' => '唱歌',
				'tw' => '跳舞',
				'ds' => '读书',
				'kb' => '看报',
				'kdm' => '看动漫',
				'sw' => '上网',
				'sdj' => '睡大觉',
				'ycw' => '养宠物',
				'qt' => '其他' 
		);
	}
	/**
	 * *
	 * 我是班上的职务
	 */
	public static function studentjob() {
		return array (
				'ptxs' => '普通学生',
				'xzz' => '小组长',
				'kdb' => '课代表',
				'tywy' => '体育委员',
				'xcwy' => '宣传委员',
				'wywy' => '文艺委员',
				'xxwy' => '学习委员',
				'ldwy' => '劳动委员',
				'bz' => '班长',
				'fbz' => '副班长',
				'xdz' => '小队长',
				'zdz' => '中队长',
				'ddz' => '大队长' 
		);
	}
	/**
	 * *
	 * 喜欢的动漫
	 */
	public static function cartoon() {
		return array (
				'cslr' => '城市猎人',
				'hzw' => '海贼王',
				'dlam' => '多啦A梦',
				'atm' => '奥特曼',
				'hyrz' => '火影忍者狼士',
				'xyy' => '喜洋洋与灰太狼',
				'hmbb' => '海绵宝宝',
				'kjys' => '铠甲勇士',
				'zzx' => '猪猪侠',
				'gfxm' => '功夫熊猫',
				'yjdwb' => '妖精的尾巴',
				'cwxjl' => '宠物小精灵',
				'qyc' => '犬夜叉',
				'qt' => '其他' 
		);
	}
	/**
	 * *
	 * 喜欢的游戏
	 */
	public static function game() {
		return array (
				'cf' => '穿越火线',
				'qqfc' => 'QQ飞车',
				'mssj' => '魔兽世界',
				'qqxw' => 'QQ炫舞',
				'dnf' => '地下城勇士',
				'lkwg' => '洛克王国',
				'seh' => '赛尔号',
				'abd' => '奥比岛',
				'ddt' => '弹弹堂',
				'qpl' => '棋牌类',
				'fndxn' => '愤怒的小鸟',
				'zwdzjs' => '植物大战僵尸',
				'qsg' => '切水果' 
		);
	}
	/**
	 * *
	 * 喜欢的运动
	 */
	public static function sports() {
		return array (
				'zq' => '足球',
				'lq' => '篮球',
				'pq' => '排球',
				'wq' => '网球',
				'ppq' => '乒乓球',
				'ymq' => '羽毛球',
				'ts' => '跳绳',
				'tj' => '踢毽',
				'pb' => '跑步',
				'sb' => '散步',
				'ps' => '爬山',
				'qt' => '其它' 
		);
	}
	public static function zscy_story_status() {
		return array (
				0 => '未审核',
				1 => '未通过',
				2 => '已审核' 
		);
	}
	
	/**
	 * 星座
	 * 
	 * @param
	 *        	$id
	 */
	public static function client_constellation($key = false) {
		$dataarr = array (
				1 => '白羊座',
				2 => '金牛座',
				3 => '双子座',
				4 => '巨蟹座',
				5 => '狮子座',
				6 => '处女座',
				7 => '天秤座',
				8 => '天蝎座',
				9 => '射手座',
				10 => '魔蝎座',
				11 => '水瓶座',
				12 => '双鱼座' 
		);
		if ($key === false) {
			return $dataarr;
		} elseif (is_numeric ( $key )) {
			$key = intval ( $key );
			if (isset ( $dataarr [$key] )) {
				$returnstr = $dataarr [$key];
			}
			return ! empty ( $returnstr ) ? $returnstr : "暂无";
		}
		return false;
	}
	
	/*
	 * 课程表系统科目
	 */
	public static function curriculumSubject() {
		return array (
				array (
						'id' => 1,
						'subjectName' => '语文',
						'subjectIco' => '1.jpg' 
				),
				array (
						'id' => 2,
						'subjectName' => '数学',
						'subjectIco' => '2.jpg' 
				),
				array (
						'id' => 3,
						'subjectName' => '英语',
						'subjectIco' => '3.jpg' 
				),
				array (
						'id' => 4,
						'subjectName' => '地理',
						'subjectIco' => '4.jpg' 
				) 
		);
	}
}