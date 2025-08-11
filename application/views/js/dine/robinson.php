<script>
$(document).ready(function(){
	<?php if($use_js == 'robPageJs'): ?>
		$('#content').rLoad({url:'robinsons/daily_files'});
		$('#daily-files-btn').click(function(){
			$('#content').rLoad({url:'robinsons/daily_files'});
			return false;
		});
		$('#settings-btn').click(function(){
			$('#content').rLoad({url:'robinsons/settings'});
			return false;
		});
	<?php elseif($use_js == 'dailyFilesJS'): ?>
		$('#resend-file-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'robinsons/get_z_read','date='+date,function(data){
				if(data['zread_id'] == 0){
					rMsg('No zread found.','error');
					btn.goLoad2({load:false});
				}
				else{
					$.post(baseUrl+'reads/resend_to_rob_two/'+data['zread_id'],function(data){
						location.reload();
						// alert(data)
					}).fail( function(xhr, textStatus, errorThrown) {
				        alert(xhr.responseText);
						btn.goLoad2({load:false});
				    });
				}
			},'json').fail( function(xhr, textStatus, errorThrown) {
		        alert(xhr.responseText);
				btn.goLoad2({load:false});
		    });
		});
		$('#show-file-btn').click(function(){
			var date = $('#file_date').val();
			var btn = $(this);
			btn.goLoad2();
			$.post(baseUrl+'robinsons/get_file','date='+date,function(data){
				var prints = data;
				$('#daily-div').html(prints['daily']);					
				btn.goLoad2({load:false});
			},'json').fail( function(xhr, textStatus, errorThrown) {
		        alert(xhr.responseText);
		    });
		});
	<?php elseif($use_js == 'detailsJs'): ?>
		$('#save-btn').click(function(event){
			var formData = $('#details_form').serialize();
			var dtype = 'json';
			$.post(baseUrl+'robinsons/details_db',formData,function(data){
				rMsg(data.msg,'success');
			},'json');
			return false;
		});

	<?php endif; ?>
});
</script>