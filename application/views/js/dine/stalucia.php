<script>
$(document).ready(function(){
	<?php if($use_js == 'staluciaPageJs'): ?>
		$('#content').rLoad({url:'stalucia/daily_files'});
		$('#daily-files-btn').click(function(){
			$('#content').rLoad({url:'stalucia/daily_files'});
			return false;
		});
		$('#settings-btn').click(function(){
			$('#content').rLoad({url:'stalucia/settings'});
			return false;
		});
	<?php elseif($use_js == 'dailyFilesJS'): ?>
		$('#show-file-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'stalucia/get_file','date='+date,function(data){
				$('#daily-div').html(data);					
				btn.goLoad2({load:false});
			});
		});	
		$('#regen-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'stalucia/regen_file','date='+date,function(data){
				if(data.error == 0){
					rMsg(data.msg,'success');
					$.post(baseUrl+'stalucia/get_file','date='+date,function(data2){
						$('#daily-div').html(data2);					
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