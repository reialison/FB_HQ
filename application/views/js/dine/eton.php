<script>
$(document).ready(function(){
	<?php if($use_js == 'mainPageJs'): ?>
		$('#content').rLoad({url:'eton/daily_files'});
		$('#daily-files-btn').click(function(){
			$('#content').rLoad({url:'eton/daily_files'});
			return false;
		});
		$('#settings-btn').click(function(){
			$('#content').rLoad({url:'eton/settings'});
			return false;
		});
	<?php elseif($use_js == 'dailyFilesJS'): ?>
		$('#show-file-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'eton/get_file','date='+date,function(data){
				$('#daily-div').html(data.daily);					
				$('#hourly-div').html(data.hourly);					
				$('#discount-div').html(data.discount);					
				btn.goLoad2({load:false});
			},'json').fail( function(xhr, textStatus, errorThrown) {
			 	alert(xhr.responseText);
				btn.goLoad2({load:false});
			});
		});	
		$('#regen-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'eton/regen_file','date='+date,function(data){
				if(data.error == 0){
					rMsg(data.msg,'success');
					$.post(baseUrl+'eton/get_file','date='+date,function(data2){
						$('#daily-div').html(data2.daily);					
						$('#hourly-div').html(data2.hourly);					
						$('#discount-div').html(data2.discount);						
						btn.goLoad2({load:false});
					},'json').fail( function(xhr, textStatus, errorThrown) {
					 	alert(xhr.responseText);
						btn.goLoad2({load:false});
					});
				}
				else{
					rMsg(data.msg,'error');
					btn.goLoad2({load:false});
				}
			},'json');
		});	
	<?php elseif($use_js == 'settingsJS'): ?>
		$('#save-btn').click(function(e){
 		   $("#settings-form").rOkay({
				btn_load		: 	$('#save-btn'),
				bnt_load_remove	: 	true,
				asJson			:   true,
				onComplete		: 	function(data){
										rMsg(data.msg,'success');
									}
			});
			return false;
		});	
	<?php endif; ?>
});
</script>