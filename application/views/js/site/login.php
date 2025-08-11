<script>
$(document).ready(function(){
	<?php if($use_js == 'loginJs'): ?>
		$('#pin').keypress(function(event){
		  if(event.keyCode == 13){
		   $('#pin-login').trigger('click');
		  }
		});
		if($('#shift_end').exists()){
			rMsg('Last Shift has ended.','success');
		}
		if($('.rot-login-by').exists()){
			var rot = $('.rot-login-by').first();
			$('.logins').hide();
			$('#pin-user').hide();
			$('#pin-id').val('');
			$(rot.attr('act')).show();
			if(rot.attr('act') == '#loginPin'){
				if(rot.attr('name') !== undefined){
					$('#pin-user').text(rot.attr('name'));
					$('#pin-user').show();
					$('#pin-id').val(rot.attr('user'));
				}
				else{
					$('#pin-user').text("");
					$('#pin-user').hide();
					$('#pin-id').val('');			
				}
			}
		}
		$('.login-by').click(function(){
			$('.logins').hide();
			$('#pin-user').hide();
			$('#pin-id').val('');
			$($(this).attr('act')).show();
			if($(this).attr('act') == '#loginPin'){
				if($(this).attr('name') !== undefined){
					$('#pin-user').text($(this).attr('name'));
					$('#pin-user').show();
					$('#pin-id').val($(this).attr('user'));
				}
				else{
					$('#pin-user').text("");
					$('#pin-user').hide();
					$('#pin-id').val('');
				}
			}
			$('#pin').focus();			
			return false;
		});
		$('#training').click(function(){
			// alert(baseUrl);
			window.location = 'http://localhost/dineTrain';
			return false;
		});
		$('#uname-login').click(function(){
			$("#uname-login-form").rOkay({
				btn_load		: 	$('#uname-login'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.error_msg != null){
											rMsg(data.error_msg,'error');
										}
										else{
											window.location = baseUrl+'dashboard';
										}
									}
			});
			return false;
		});
		$('#pin-login').click(function(){
			var pin = $('#pin').val();
			var pin_id = $('#pin-id').val();
			$.post(baseUrl+'site/go_login','pin='+pin+'&pin_id='+pin_id,function(data){
				if(data.error_msg != null){
					rMsg(data.error_msg,'error');
					$('#pin').focus();
				}
				else{
					window.location = data.redirect_address;
				}
			// },'json');
			},'json');
			return false;
		});
		// $('#login-btn').click(function(){
		// 	$("#login-form").rOkay({
		// 		btn_load		: 	$('#login-btn'),
		// 		bnt_load_remove	: 	true,
		// 		asJson			: 	true,
		// 		onComplete		:	function(data){
		// 								// alert(data);
		// 								if(data.error_msg != null){
		// 									rMsg(data.error_msg,'error');
		// 								}
		// 								else{
		// 									window.location = baseUrl;
		// 								}
		// 							}
		// 	});
		// 	return false;
		// });
		setInterval(function(){
	  		checkNewShifts();
		}, 3000);
		function checkNewShifts(){
			$.post(baseUrl+'site/get_login_unclosed_shifts',function(data){
				$.each(data,function(user_id,row){
					if(!$('#shift-btn-'+user_id).exists()){
						console.log('added');
						var div = $('<div act="#loginPin" id="shift-btn-'+user_id+'" user="'+user_id+'" name="'+row['name']+'"'+
									'class="login-by tsc_awb_large tsc_awb_white tsc_flat">'+
										'<img style="height:40px;" src="'+baseUrl+'img/avatar.jpg">'+
										'<h5>'+row['username']+'</h5>'+
									'</div>');
						$('#shift-column').append(div);
						$('.login-by').click(function(){
							$('.logins').hide();
							$('#pin-user').hide();
							$('#pin-id').val('');
							$($(this).attr('act')).show();
							if($(this).attr('act') == '#loginPin'){
								if($(this).attr('name') !== undefined){
									$('#pin-user').text($(this).attr('name'));
									$('#pin-user').show();
									$('#pin-id').val($(this).attr('user'));
								}
								else{
									$('#pin-user').text("");
									$('#pin-user').hide();
									$('#pin-id').val('');
								}
							}
							$('#pin').focus();			
							return false;
						});
					}
				});
			},'json');	
		}
		//nicko
	<?php elseif($use_js == 'loginJs1'): ?>
		$("#username").keypress(function(event){
		  if(event.keyCode == 13){
			   $('#login-btn').trigger('click');
		  }
		});

		$('#login-btn').click(function(event){
			console.log("test");
			$("#login-form").rOkay({
				btn_load		: 	$('login-btn'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.error_msg != null){		
															// console.log(data.error_msg);
	   								
											rMsg(data.error_msg,'error');											
										}
										else{
												window.location = baseUrl+'dashboard';
												// window.location = baseUrl+'setup/brands';
									
										}
									}
			});
			return false;
		});		
		//end
	<?php elseif($use_js == 'autoZreadJs'): ?>
		$.post(baseUrl+'reads/auto_zread',function(data){
			// alert(data);
			$('.ztxt').html(data);
			setTimeout(function() {
			  window.location.href = baseUrl+"site/login";
			}, 2000);
		});
	<?php endif; ?>
});
</script>