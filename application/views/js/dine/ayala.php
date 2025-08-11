<script>
$(document).ready(function(){
	<?php if($use_js == 'ayalaPageJs'): ?>
		$('#content').rLoad({url:'ayala/daily_files'});
		$('#daily-files-btn').click(function(){
			$('#content').rLoad({url:'ayala/daily_files'});
			return false;
		});
		$('#settings-btn').click(function(){
			$('#content').rLoad({url:'ayala/settings'});
			return false;
		});
		$('#backtrack-btn').click(function(){
			$('#content').rLoad({url:'ayala/back_track_files'});
			return false;
		});
	<?php elseif($use_js == 'dailyFilesJS'): ?>
		$('#show-file-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'ayala/get_file','date='+date,function(data){
				var prints = data;
				$('#daily-div').html(prints['daily']);					
				$('#hourly-div').html(prints['hourly']);				
				btn.goLoad2({load:false});
			},'json').fail( function(xhr, textStatus, errorThrown) {
		        alert(xhr.responseText);
		    });
		});	
		$('#regen-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'ayala/regen_file','date='+date,function(data){
				if(data.error == 0){
					rMsg(data.msg,'success');
					$.post(baseUrl+'ayala/get_file','date='+date,function(data2){
						var prints = data2;
						$('#daily-div').html(prints['daily']);					
						$('#hourly-div').html(prints['hourly']);					
						btn.goLoad2({load:false});
					},'json').fail( function(xhr, textStatus, errorThrown) {
				        alert(xhr.responseText);
				    });
					
				}
				else{
					rMsg(data.msg,'error');
					btn.goLoad2({load:false});
				}
			},'json').fail( function(xhr, textStatus, errorThrown) {
		        alert(xhr.responseText);
		    });
		});	
	<?php elseif($use_js == 'backtrackFilesJS'): ?>
		$('#regen-btn').click(function(){
			var btn = $(this);
			var formData = 'start_date='+$('#file_start_date').val()+'&end_date='+$('#file_end_date').val();
			btn.goLoad2();
			$.post(baseUrl+'ayala/back_track_file',formData,function(data){
				rMsg(data.msg,'success');
				btn.goLoad2({load:false});
			},'json').fail( function(xhr, textStatus, errorThrown) {
		        alert(xhr.responseText);
		    });
			return false;	
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