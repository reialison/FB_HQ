<script>
$(document).ready(function(){
	<?php if($use_js == 'listFormJs'): ?>
		$('#customers-tbl').rTable({
			loadFrom	: 	 'pos_customers/get_customers',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('pos_customers/form');
							 },				 	
			edit		: 	 function(id){
								goTo('pos_customers/form/'+id);
							 },
			afterLoad 	: 	 function(){
								$('#customers-tbl').dataTable();
							 }				 	
		});
		// $('#customers-tbl').dataTable();
	<?php elseif($use_js == 'listTerminalFormJs'): ?>
		$('#customers-tbl').rTable({
			loadFrom	: 	 'pos_customers/get_customers/1',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('pos_customers/customer_terminal_form');
							 },				 	
			edit		: 	 function(id){
								goTo('pos_customers/customer_terminal_form/'+id);
							 }				 	
		});	
	<?php elseif($use_js == 'customerFormJs'): ?>
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var cust_id = $('#cust_id').val();

			if(cust_id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			$(tabPane).rLoad({url:baseUrl+loadUrl+cust_id});
		}
		function disEnbleTabs(id,enable){
			if(enable){
				$(id).parent().removeClass('disabled');
				$(id).removeAttr('disabled','disabled');
				$(id).attr('data-toggle','tab');
			}
			else{
				$(id).parent().addClass('disabled');
				$(id).attr('disabled','disabled');
				$(id).removeAttr('data-toggle','tab');
			}
		}
	<?php elseif($use_js == 'detailsLoadJs'): ?>
		$('#save-btn').click(function(){
			$("#customers_form").rOkay({
				btn_load		: 	$('#save-btn'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.error == ""){
											if(typeof data.msg != 'undefined' ){
												$('#cust_id').val(data.id);
												disEnbleTabs('.load-tab',true);
												rMsg(data.msg,'success');
											}
										}
										else{
											rMsg(data.msg,'error');
										}
									}
			});
			return false;
		});
		$('#save-new-btn').click(function(){
			$("#customers_form").rOkay({
				btn_load		: 	$('#save-new-btn'),
				bnt_load_remove	: 	true,
				addData			: 	'new=1',
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.error == ""){
											if(typeof data.msg != 'undefined' ){
												if($('#to_terminal').val() == 'yes'){
													window.location = baseUrl+'pos_customers/customer_terminal_form/'+data.id;
												}
												else{
													window.location = baseUrl+'pos_customers/form/'+data.id;
												}
											}
										}
										else{
											rMsg(data.msg,'error');
										}
									}
			});
			return false;
		});
		function disEnbleTabs(id,enable){
			if(enable){
				$(id).parent().removeClass('disabled');
				$(id).removeAttr('disabled','disabled');
				$(id).attr('data-toggle','tab');
			}
			else{
				$(id).parent().addClass('disabled');
				$(id).attr('disabled','disabled');
				$(id).removeAttr('data-toggle','tab');
			}
		}
	
	<?php endif; ?>
});
</script>