<html>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<head>
	</head>
<script src="/Public/js/jquery-1.7.2.min.js"></script>
	<body>
		F5C7248A-79F7-4866-9836-31227D69570B
		请先安装证书 <a href="/psc/psc.pfx">下载证书</a>，安装时密码为1.
		<div>
			<object classid="clsid:F5C7248A-79F7-4866-9836-31227D69570B" codebase="PSC.cab#version=1,0,0,1"
				id="px" width="0" height="0" VIEWASTEXT>
			</object>
			<input type="button" name="show" onclick="getInfo();" value="获取信息" ID="Button1" />
			
			<script language="javascript" type="text/javascript">  
			
				var _ControllerSN = '31103';
				var _Port='60000';
				var sm='';
				function getInfo(){
					try{
						sm=px.getControlInfo(_ControllerSN,'192.168.0.220',_Port);
						//sm = px.getControlNetworkInfo(_ControllerSN,_Port);

						
						$('#controlInfo').html(sm);
					}catch(e){
						alert("error:"+e);
					}
				}
			</script>
			<div>点击按钮查看控制器信息:
				<div id="controlInfo"></div>
			</div>
		</div>
	</body>
</html>