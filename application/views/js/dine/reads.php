<script>
$(document).ready(function(){
	<?php if($use_js == 'recoverJs'): ?>
		$('#xprocess-btn').click(function(){
			var db = $('#xdatabase').val();
			var date = $('#xdate').val();
			var formData = 'xdb='+db+'&xdate='+date;
			$.loadPage();
			$.post(baseUrl+'reads/xinsert',formData,function(data){
				// alert(data);
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('XREAD RECOVERED!','success');
					$('#xdatabase').val('');
					$('#xdate').val('');
					$.loadPage({load:false});
				}
			},'json').fail( function(xhr, textStatus, errorThrown) {
			   alert(xhr.responseText);
			});
			return false;
		});
		$('#zprocess-btn').click(function(){
			var date = $('#zdate').val();
			var formData = 'zdate='+date;
			$.loadPage();
			$.post(baseUrl+'reads/zinsert',formData,function(data){
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('ZREAD PROCESSED!','success');
					$('#zdate').val('');
					$.loadPage({load:false});
				}
			},'json').fail( function(xhr, textStatus, errorThrown) {
			   alert(xhr.responseText);
			});
			return false;
		});
	<?php endif; ?>
});
</script>