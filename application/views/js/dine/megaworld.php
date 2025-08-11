<script>
$(document).ready(function(){
	<?php if($use_js == 'mainPageJs'): ?>
		$('#content').rLoad({url:'megaworld/daily_files'});
		$('#daily-files-btn').click(function(){
			$('#content').rLoad({url:'megaworld/daily_files'});
			return false;
		});
		$('#settings-btn').click(function(){
			$('#content').rLoad({url:'megaworld/settings'});
			return false;
		});
	<?php elseif($use_js == 'dailyFilesJS'): ?>
		$('#show-file-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'megaworld/get_file','file_date='+date,function(data){
				$('#sales-div').html(data.sales);								
				$('#hourly-div').html(data.hour);								
				$('#discount-div').html(data.disc);								
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
			$.post(baseUrl+'megaworld/regen_file','file_date='+date,function(data){
				if(data.error == 0){
					rMsg(data.msg,'success');
					$.post(baseUrl+'megaworld/get_file','file_date='+date,function(data2){
						$('#sales-div').html(data2.sales);								
						$('#hourly-div').html(data2.hour);								
						$('#discount-div').html(data2.disc);											
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