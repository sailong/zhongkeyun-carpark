var statData; //统计本班统计变量
var initWelcomeStr = '鉴湖景园欢迎您';
var led_show = new Object();
var led_show_status = 1;
//获取读卡器门号
function getReaderNumber(cardNumber,statusCode)
{
	var num16;
	var readerNumber=0;
	if(statusCode<10)
	{
		num16 = statusCode;
	}else
	{
		num16 = toHex(statusCode);
	}
	if(num16<10)
	{
		num16 = '0' + num16;
		//alert(num16);
	}
	if(cardNumber>100)
	{
		var lastNumber = num16.substring(1);
		if(!isNaN(lastNumber))
		{
			lastNumber = parseInt(lastNumber);
			if(lastNumber<=3) 
			{
				readerNumber = lastNumber + 1;
			}else if(lastNumber>=4 || lastNumber<=7)
			{
				readerNumber = lastNumber - 3;
			}else
			{
				readerNumber = lastNumber - 7;
			}
		}else
		{
			if(lastNumber=='C')
			{
				readerNumber = 1;
			}else if(lastNumber=='D')
			{
				readerNumber = 2;
			}else if(lastNumber=='A' || lastNumber=='E')
			{
				readerNumber = 3;
			}else if(lastNumber=='B' || lastNumber=='F')
			{
				readerNumber = 4;
			}
		}
	}
	return readerNumber;
}

function toHex(num){
	　　var rs = "";
	　　var temp;
	　　while(num/16 > 0){
	　　　　temp = num%16;
	　　　　rs = (temp+"").replace("10","a").replace("11","b").replace("12","c").replace("13","d").replace("14","e").replace("15","f") + rs;
	　　　　num = parseInt(num/16);
	　　}
	　　return rs;
}

//==========================================
function sendAjax(url,data)
{
	var result;
	$.ajax({
		  type: 'POST',
		  async:false,
		  url: url,
		  data: data,
		  success: function(resultData){
			  		result =  resultData;
				   },
		  dataType: "json"
		});
	return  result;
}

/**
 * 开闸
 * @param doorAddr
 * @param readerNo
 * @param type
 * @returns
 */
function manOpenDoor(doorAddr,readerNo,type,control_ip)
{
	//alert('ok');return;
	var url  = '/Monitor/Ajax/manOpenDoor';
	var data = {'doorAddr':doorAddr,'readerNo':readerNo,'type':type};
	var suffix_id_str = '_'+ doorAddr+'_'+readerNo;
	//var cardCode = $('#door_code'+suffix_id_str).val();//alert(cardCode);door_real_code
	var cardCode = $('#door_real_code'+suffix_id_str).val();
	if(!cardCode) return;
	data.cardCode = cardCode;
	if(type==4)
	{
		data.realMoney = $('#real_money'+suffix_id_str).val();
		if(!data.realMoney)
		{
			alert('请输入实收车费');
			return;
		}
	}
	var result = sendAjax(url,data);
	if(result.code==1) //服务器返回开门成功
	{
		var physical_door_id = result.data.physical_door_id;
		var returnData = px.openDoor(doorAddr,control_ip,port,physical_door_id);
		returnData = JSON.parse(returnData);
		if(returnData.ErrorCode==0)//控制器返回开门成功
		{
			//alert('开闸成功');
			//情况表单
			$('#door_real_code'+suffix_id_str).val('');
			$('#real_money'+suffix_id_str).val('');
			if(checkVariable(gangtingPage) && gangtingPage ==1)//岗亭页面
			{
				$('#door_code'+suffix_id_str).html('');
				$('#door_into_time'+suffix_id_str).html('');
				$('#door_out_time'+suffix_id_str).html('');
				$('#door_park_time'+suffix_id_str).html('')
				$('#card_type'+suffix_id_str).html('');
				$('#expire_time'+suffix_id_str).html('');
				$('#left_days'+suffix_id_str).html('');
				$('#should_money'+suffix_id_str).html('');
			}else
			{
				$('#door_code'+suffix_id_str).val('');
				$('#door_into_time'+suffix_id_str).val('');
				$('#door_out_time'+suffix_id_str).val('');
				$('#door_park_time'+suffix_id_str).val('');
				$('#should_money'+suffix_id_str).val('');
				$('#cate_id'+suffix_id_str).val(0);  
			}
		}else
		{
			alert('系统处理成功，但开闸失败');
		}
	}else
	{
		alert('02----开闸失败');
	}
	return false;
}

/**
 * 初始化控制器
 * @param doorJson
 * @param port
 * @param px
 * @returns
 */
function initControllerData(doorJson,port,px)
{
	if(doorJson)
	{
		$.each(doorJson, function(index, content_each){ 
			var door_addr = parseInt(content_each.door_addr);
			var ip = content_each.ip;
			var ret =px.getControlInfo(door_addr,content_each.ip,port);
			//px.setControlIP(door_addr,content_each.door_ip,port,'255.255.255.0','192.168.0.1');
			//px.adjustClockByPCTime(door_addr,ip,port);
			try{
					ret = JSON.parse(ret);
					var recordNum = ret.Data.recordNum;
					if(recordNum>0)
					{
						px.delRecords(door_addr, ip,port,recordNum);
					}
			 }catch(e){}
		});
	}
}
//alert(px.getControlNetworkInfo(31103,60000));
var left_park_count = 0;
//统计数据
function getStatData()
{
	var data = sendAjax('/Monitor/ajax/getStatInfo');
	if(!checkVariable(data.data)) return;
	$.each(data.data, function(k, content_each){ 
		if(k=='card_type')
		{
			$.each(data.data.card_type, function(k, t){ 
				if($('#stat_card_type_counts_'+k).size()) $('#stat_card_type_counts_'+k).html(t);
			});
		}else
		{
			if($('#stat_'+k).size()) $('#stat_'+k).html(content_each);
		}
		
	});
	statData = data.data;
	left_park_count = statData.left_park_count;
}
var inputObj = new Array();
inputObj[0] = 'door_code';
inputObj[1] = 'door_into_time';
inputObj[2] = 'door_out_time';
inputObj[3] = 'door_park_time';
inputObj[4] = 'cate_id';
inputObj[5] = 'should_money';
inputObj[6] = 'door_car_owner';
function clearInput(sign)
{
	for(i= 0;i <inputObj.length;i++)
    {
        if ($('#'+inputObj[i]+sign).size()>0) $('#'+inputObj[i]+sign).html('');  
    }  
}


function checkVariable(variable)
{
	if(typeof(variable)!='undefined') return true;
	return false;
}



/**
 * 开闸
 * @param doorAddr
 * @param readerNo
 * @param type
 * @returns
 */
function _manOpenDoor(doorAddr,readerNo,type,control_ip,physical_door_id)
{
	
		var _manReturnData = px.openDoor(doorAddr,control_ip,port,physical_door_id);
		_manReturnData = JSON.parse(_manReturnData);
		if(_manReturnData.ErrorCode==0)//控制器返回开门成功
		{
			alert('开门成功');
		}else
		{
			alert('开门失败');
		}
}

//频繁刷卡判断---------------------------------------------------------------
var pre_history_json = '';
var timestamp;
//card_id,door_addr,reader_no,swipdate
function checkCard(cardId,door_addr,readerNumber,swipeDate)
{
	var sign = cardId+'-'+door_addr+'-'+readerNumber;
	if(!pre_history_json)
	{
		pre_history_json={"sign":sign,'swipdate':swipeDate};
	}else
	{
		timestamp = Date.parse(new Date())/1000;
		
		if(pre_history_json.sign== sign && timestamp - js_strto_unixtime(pre_history_json.swipdate)<30)
		{
			//alert(pre_history_json);
			//从控制器中删除数据
			px.delRecords(door_addr,ip,port,1);
			return false;
		}else
		{
			pre_history_json={"sign":sign,'swipdate':swipeDate};
		}
	}	
	return true;
}
//日期格式转换成时间戳
function js_strto_unixtime(str_time)
{
    var new_str = str_time.replace(/:/g,'-');
    new_str = new_str.replace(/ /g,'-');
    var arr = new_str.split("-");
    var datum = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
    return strtotime = datum.getTime()/1000;
}
/**
 * 打开监控
 * @returns
 */
var monitor_status = 0;
function openMonitor(obj)
{
	if(!monitor_status)
	{
		//开启监控
		setTimeout('checkSn()',2000);
		monitor_status = 1;
		obj.value='停止监控';
	}else
	{
		//停止监控
		monitor_status = 0;
		obj.value='打开监控';
	}
}

/**
 * 初始化LED
 * @param ledJson
 * @param left_park_counts
 * @returns
 */
function initLed(ledJson,left_park_count)
{
	if(!led_show_status) return;
	var ledObj;
	$.each(ledJson, function(index, content_each){ 
		if(!content_each.led_ip) return true;
		ledObj = document.getElementById('ledop');
		if(!ledObj) return true;
		ledObj.setSocketIP(content_each.led_ip);
		ledObj.init();
		
		var left_park_str = '';
		if(left_park_count<=0)
		{
			left_park_str = '     车位已满';
		}else
		{
			if(content_each.show_left_parking == 1) left_park_str = '   空余车位:'+left_park_count+'个';
		}
		
		var led_ret = ledObj.setWelcome(" 　"+initWelcomeStr+"　  　　　"+content_each.lane+"　　　 ",left_park_str);
		//sendAjax('/Monitor/Ajax/addLog',{'function_name':'led.setWelcome','param':'1111','return':led_ret});
	});
}
/**
 * LED显示
 * @returns
 */
function showLed(ledReleateDoorListJson,doorSign,doorType,data)
{
	if(!led_show_status) return;
	var ledObj;
	var ledIp;
	var param1 = '';
	var param2 = '';
	var param3 = '';
	var param4 = '';
	var serviceData = data.sessionData;
	$.each(ledReleateDoorListJson, function(index, content_each){ 
		ledObj = null;
		if(index==doorSign)
		{
			ledIp = content_each.led_ip;
			if(!ledIp) return true;
			ledObj = document.getElementById('ledop');
			ledObj.setSocketIP(ledIp);
			ledObj.init();
			//更新led显示信息
			led_show[ledIp] = {led_ip:ledIp,timestamp:getTimestamp(),lane:content_each.lane,show_left_parking:content_each.show_left_parking,card_type:serviceData.card_type};
			return false;
		}
	});
	if(!ledObj) return;
	//如果没有卡片数据
	if(!checkVariable(data.code) || data.code=='')
	{	
		ledObj.set4LinesInfo('   车辆未登记','','','');
		return;
	}
	if(typeof(data.car_code)!='undefined')
	{
		if(!data.car_code)
		{
			param2 = ' 请登记车辆信息';
		}else
		{
			param2 = '    '+data.car_code+' ';
		}
	}
	//如果是入场
	if(doorType==0)
	{
		param1 = '   欢迎您回家  ';
		if(typeof(data.parking)!='undefined')
		{
			param4 = ' 请停车位:'+data.parking;
		}
		if(serviceData.card_type==1)
		{
			param1 = ' '+initWelcomeStr;
			param2 = '   类型:'+data.card_type_str;
			param3 = '    入场时间: ';
			param4 = '  '+serviceData.format_start_time;
		}else if(serviceData.card_type==2)
		{
			param3 = ' 剩余金额:'+data.money+'元';
			param4 = '   请有序停车';
		}else if(serviceData.card_type==3)
		{
			param3 = '  剩余天数:'+data.left_days;
		}
		ledObj.set4LinesInfo(param1,param2,param3,param4);
	}else
	{
		param1 = '    一路顺风  ';
		param3 = '   类型:'+data.card_type_str;
		if(serviceData.card_type==1)
		{
			ledObj.set3LinesInfo("    入场时间:       出场时间:　"," "+serviceData.format_start_time+"   "+serviceData.format_end_time+" "," 收费:"+serviceData.charge+"元");
			return;
		}else if(serviceData.card_type==2)
		{
			param4 = '   余额:'+data.money+'元';
		}else if(serviceData.card_type==3)
		{
			param4 = '  剩余天数:'+data.left_days;
		}
		ledObj.set4LinesInfo(param1,param2,param3,param4);
	}
}

/**
 * 更新led信息显示
 * @returns
 */
function updateLedToWelcome()
{
	if(!led_show_status) return;
	if(!led_show) return;
	$.each(led_show, function(index, content_each){
		if(!content_each || !content_each.led_ip) return true;
		var timeDifference = getTimestamp()-content_each.timestamp;
		//临时卡 停留30秒再更新led 其他类型的卡5秒后即可更新led
		if((content_each.card_type !=1 &&  timeDifference > 5) || (content_each.card_type ==1 && timeDifference > 30))
		{
			setLedToWelcome(content_each);
			delete led_show[index];
		}
	});
	
}
/**
 * 获取时间戳
 * @returns
 */
function getTimestamp()
{
	return Date.parse(new Date())/1000;
}
//判断是否在数组内
function in_array(item, array) {
	for(var i = 0;i<array.length;i++){  
		if(array[i] == item){  
			return i;  
		}  
	}  
	return -1; 
}
//--------------------------------------------
function setLedToWelcome(content_each)
{
	if(!content_each || !content_each.led_ip) return;
	var ledObj;
	ledObj = document.getElementById('ledop');
	ledObj.setSocketIP(content_each.led_ip);
	ledObj.init();
	var left_park_str = '';
	if(left_park_count<=0)
	{
		left_park_str = '     车位已满';
	}else
	{
		if(content_each.show_left_parking == 1) left_park_str = '   空余车位:'+left_park_count+'个';
	}
	ledObj.setWelcome(" 　"+initWelcomeStr+"　  　　　"+content_each.lane+"　　　 ",left_park_str);
}
//更新空闲的显示屏
var pre_update_idle_led_show_time = 0;
function updateIdleLedShow()
{
	if(ledReleateDoorListJson.length==0 || getTimestamp() - pre_update_idle_led_show_time < 20) return;
	$.each(ledReleateDoorListJson, function(index, content_each){ 
		if(led_show && led_show.hasOwnProperty(content_each.led_ip))  return true;
		if(content_each.show_left_parking == 1) setLedToWelcome(content_each);
	});
	pre_update_idle_led_show_time = getTimestamp();
}

//---发送异常短信通知--------------
var exceptionControllerData = new Object();
function sendExceptionMessage(controllerData)
{
	var door_id = controllerData.door_id;
	var isExists = exceptionControllerData.hasOwnProperty(door_id);
	if( !isExists || getTimestamp() - exceptionControllerData.sendTime > 3600)
	{
		//发送短信
		exceptionControllerData[door_id] = {sendTime:getTimestamp()};
		sendAjax('/Monitor/SendMessage/send',{door_id:door_id});
	}
}
//----2013-03-20---更新控制器（共享车位相关）---------------------------------
function updateController(cardInfo)
{
	var doorData = cardInfo.door;
	if(!checkVariable(cardInfo.card_id) || !cardInfo.card_id || doorData.park_id == 0) return;
	var service_return = sendAjax('/Monitor/Ajax/getShareParkCardData',{card_id:cardInfo.card_id,door_id:doorData.door_id});
	if(service_return && checkVariable(service_return.code) && service_return.code==1)
	{
		//alert(service_return.msg);
		if(!checkVariable(service_return.data.cardList) || service_return.data.cardList.length == 0)
		{
			return;
		}
		var actionId = 0;//0 删除  1 添加
		if(doorData.door_type==1)
		{
			if(!checkVariable(service_return.data.door)) return;
			actionId = 1;
		}
		$.each(service_return.data.cardList, function(index, card_info){ 
			if(doorData.door_type ==1 )
			{
				if(service_return.data.door.length > 0)
				{
					$.each(service_return.data.door, function(ind, door){ 
						px.addOrModifyPrivilege(door.door_addr, door.door_ip, port, door.brake_no, card_info.code,card_info.start_time, card_info.end_time, 1, 123456);
					});
				}
			}else
			{
				px.addOrModifyPrivilege(doorData.door_addr, ip, port, doorData.brake_no, card_info.code,card_info.start_time, card_info.end_time, 0, 123456);
			}
		});
	}
}