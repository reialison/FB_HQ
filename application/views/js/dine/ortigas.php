<script>
$(document).ready(function(){
	<?php if($use_js == 'ortigasPageJs'): ?>
		$('#ortigas-content').rLoad({url:'ortigas/dailyFileRead'});
		
		$('#daily-sales-btn').click(function(){
			$('#ortigas-content').rLoad({url:'ortigas/daily_sales'});
			return false;
		});
		$('#hourly-sales-btn').click(function(event){
			$('#ortigas-content').rLoad({url:'ortigas/hourly_sales'});
			return false;
		});
		$('#invoice-sales-btn').click(function(event){
			$('#ortigas-content').rLoad({url:'ortigas/invoice_sales'});
			return false;
		});
		$('#settings-btn').click(function(event){
			$('#ortigas-content').rLoad({url:'ortigas/settings'});
			return false;
		});
		$('#daily-read-sales-btn').click(function(event){
			$('#ortigas-content').rLoad({url:'ortigas/dailyFileRead'});
			return false;
		});
	<?php elseif($use_js == 'generateJs'): ?>
		$('#generate-btn').click(function(e){
 		   $("#generate_form").rOkay({
				btn_load		: 	$('#generate-btn'),
				bnt_load_remove	: 	true,
				goSubmit		:   true,
				onComplete		: 	function(data){
										rMsg(data,'success');
									}
			});
			// if(noError){
			// 	var formData = $("#generate_form").serialize();
			// 	var passTo = $("#generate_form").attr('action');
			// 	window.location = baseUrl+passTo+"?"+formData;
			// }
			return false;
		});	
	<?php elseif($use_js == 'dailyFileReadJS'): ?>
		$('#show-file-btn').click(function(){
			var date = $('#file_date').val();
			var types = ['daily','hourly','invoice'];
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'ortigas/get_zread_id','date='+date,function(data){
				if(data != "0"){
					$.each(types,function(key,val){
						$.post(baseUrl+'ortigas/file_view/'+val+'?date='+date,function(file){
							$('#'+val+'-div').html(file);
						});	
					});
					btn.goLoad2({load:false});
				}
			});
		});	
		$('#regen-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			var types = ['daily','hourly','invoice'];
			btn.goLoad2();
			$.post(baseUrl+'ortigas/get_zread_id','date='+date,function(data){
				if(data != "0"){
					$.post(baseUrl+'reads/ortigas_file/'+data+'/1/1',function(data){
												
						$.each(types,function(key,val){
							$.post(baseUrl+'ortigas/file_view/'+val+'?date='+date,function(file){
								$('#'+val+'-div').html(file);
							});	
						});
						
						rMsg('File Generated','success');
						btn.goLoad2({load:false});
					});
				}	
			});	
			return false;
		});	
		// $('.view_file').rPopView();
		// $('.regen-btn').each(function(){
		// 	$(this).click(function(){
		// 		var id = $(this).attr('zread_id');
		// 		var btn = $(this);
		// 		btn.goLoad2();
		// 		$.post(baseUrl+'reads/ortigas_file/'+id+'/1/1',function(data){

		// 			rMsg('File Generated','success');
		// 			btn.goLoad2({load:false});
		// 		});
		// 		return false;
		// 	});
		// });
	<?php elseif($use_js == 'ortigasSettingsJs'): ?>
		$('#save-btn').click(function(e){
 		   $("#settings-form").rOkay({
				btn_load		: 	$('#save-btn'),
				bnt_load_remove	: 	true,
				asJson			:   true,
				onComplete		: 	function(data){
										rMsg(data.msg,'success');
									}
			});
			// if(noError){
			// 	var formData = $("#generate_form").serialize();
			// 	var passTo = $("#generate_form").attr('action');
			// 	window.location = baseUrl+passTo+"?"+formData;
			// }
			return false;
		});	
	<?php endif; ?>
});
</script>