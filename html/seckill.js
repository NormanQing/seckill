var seckill={
	URL:{
		now:function(){
			return '/now.php';
		},
		exposer:function(seckillId){
			return '/exposer.php?id='+seckillId;
		},
		excution:function(){
			return '/excution.php';
		}
	},
	validatePhone:function(userPhone){
		if(userPhone && userPhone.length==11 && !isNaN(userPhone))
		{
			return true;
		}
		return false;
	},
	detail:{
		init:function(param){
			//手机验证和登陆
			var userPhone = $.cookie('userPhone');
			if(!seckill.validatePhone(userPhone)){
				//没有登录
				var killPhoneModal =$("#killPhoneModal");

				killPhoneModal.modal({
					show:true,//显示弹层
					backdrop:'static',//禁止位置关闭
					keyboard:false//关闭键盘事件
				});
				$("#killPhoneBtn").click(function(){
					var inputPhone = $("#killphoneKey").val();
					if(seckill.validatePhone(inputPhone))
					{
						//7天有效
						$.cookie('userPhone', inputPhone, {expires: 7, path: '/'});
						//验证通过，刷新页面
						window.location.reload();
					}else{
						console.log('1111');
						$('#killphoneMessage').hide().html('<label class="label label-danger">手机号错误!</label>').show(300);
					}
				});
			}
			
			//已经登陆
			//计时交互
			var startTime =param['startTime'];
			var endTime = param['endTime'];
			var seckillId = param['seckillId'];
			$.get(seckill.URL.now(),{},function(result){
				if(result && result['success'])
				{
					var nowTime = result.data;
					//时间判断计时交互
					seckill.countDown(seckillId,nowTime,startTime,endTime);
				}
			},'json');
		}
	},
	
	countDown:function(seckillId,nowTime,startTime,endTime){
		console.log(seckillId + '_' + nowTime + '_' + startTime + '_' + endTime);
		var seckillBox = $("#seckill-box");
		if(nowTime>endTime){
			//秒杀结束
			seckillBox.html('秒杀结束！');
		}else if(nowTime<startTime){
			//秒杀未开始，计时时间绑定
			var killTime = new Date(startTime); //防止时间便偏移
			console.log(killTime);
			seckillBox.countdown(killTime,function(event){
				var format = event.strftime("秒杀倒计时：%D天 %H时 %M分 %S秒");
				seckillBox.html(format);
			}).on('finish.countdown',function(){
				//时间完成后回调时事件
				//获取秒杀地址，控制显示逻辑，执行秒杀
				seckill.handelSeckill(seckillId,seckillBox);
			});
		 }else{
			//执行秒杀
			seckill.handelSeckill(seckillId,seckillBox);
		 }
	},
	handelSeckill:function(seckillId,node){
		//获取秒杀地址,控制显示器,执行秒杀
		node.hide().html('<button class="btn btn-primay btn-lg" id="killBtn">秒杀开始</button>');
		$.post(seckill.URL.exposer(seckillId),{},function(result){

			if(result && result['success']){
				var exposer = result['data'];

				if(exposer['exposed'])
				{
					//秒杀开启
					//获取秒杀地址
					var md5 = exposer['md5'];
					var killUrl = seckill.URL.excution(seckillId,md5);
					
					//绑定一次点击事件
					$("#killBtn").one('click',function(){
						//执行秒杀请求
						//1:先禁用按钮
						$(this).addClass('disabled');
						//2:发送请求
						var data={
							seckillId:seckillId,
							md5:md5
						}
						$.post(killUrl,data,function(result){
							console.log(result);
							if(result && result['success']){
								var killResult = result['data'];
								var state = result['state'];
								var msg = result['msg'];
								node.html('<span class="label label-success">'+msg+'</span>');
							}
						},'json');
					});
					node.show();
				}else{
					//未开启
					var start = exposer['start_time'];
					var end = exposer['start_time'];
					var now = exposer['start_time'];
					seckill.countDown(seckillId,now,start,end);

				}
			}
		},'json');
		
	}
}