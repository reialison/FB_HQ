<script>
$(document).ready(function(){
	<?php if($use_js == 'loadJs'): ?>
        $(".center").center();
        $(document).resize(function() {
            $(".center").center();
        });	    
        $('#loadBar').rLoadBar({'statTxt':$('#loadTxt')});
        $.post(baseUrl+'site/go_load',function(data){
        	// alert(data);
        	// return false;
	        $('#loadBar').rLoadBarEnd({
	        	onComplete	: function(){
	        					if(data.error != ""){
		        					window.location = baseUrl+'site/login/'+data.error;	        						
	        					}
	        					else{
		        					window.location = baseUrl;	        						
	        					}
	        				  }
	        });
		},'json').fail( function(xhr, textStatus, errorThrown) {
	        // alert(xhr.responseText);
	        window.location = baseUrl;
	    });;
	        // alert(data);
		// });

	<?php endif; ?>    
});
</script>