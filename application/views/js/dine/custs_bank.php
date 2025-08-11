<script>
$(document).ready(function(){
	<?php if($use_js == 'indexJs'): ?>
		var height = $(document).height();
		$('.div-content').height(height - 63);
		$('.div-content').rLoad({url:baseUrl+'custs_bank/deposit'});
		$('#deposit-money-btn').click(function(){
			$('.div-content').rLoad({url:baseUrl+'custs_bank/deposit'});
			$('.ui-keyboard').remove();
			return false;
		});
		$('#deposit-lists-btn').click(function(){
			$('.div-content').rLoad({url:baseUrl+'custs_bank/customers_money'});
			return false;
		});
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
	<?php elseif($use_js == 'customersMoneyList'): ?>
		$('#customers-tbl').rTable({
			loadFrom	: 	 'custs_bank/get_custs_deposits',
			noEdit		: 	 true,			 	
			noAdd		: 	 true,			 	
		});
	<?php elseif($use_js == 'depositJs'): ?>
		var height = $(document).height();
		$('#search-div').height(height - 63 -265);
		$('#details-div').height(height - 63 -327);
		// $('.listings').perfectScrollbar({suppressScrollX: true});
		// $('#search-customer,.key-ins')
		// 	.keyboard({
		// 		alwaysOpen: true,
		// 		usePreview: false,
		// 		autoAccept : true
		// 	});
		$('#search-customer').focus();	
		$('#search-customer').on('keyup',function(){
			show_search();
		});	
		// $('#search-customer').keypress(function (e) {
		// 	var key = e.which;
		// 	if(key == 13){
		// 		$('#search-customer').blur();
		// 	}
		// });
		$('#add-cust-btn').click(function(){
			window.location = baseUrl+'pos_customers/customer_terminal';
		});
		$('#submit-btn').click(function(){
			// alert('ere');
			$('#deposit-form').rOkay({
				btn_load 		: 	$('#submit-btn'),
				bnt_load_remove : 	false,
				asJson 			: 	false,
				onComplete 		: 	function(data){
										location.reload();
										// alert(data);
										// var id = data.id;
										// var type = $('#trans_type').val();
										// var formData = 'type='+type+'&customer_id='+id;
										// $.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
										// 	window.location = baseUrl+'cashier/counter/'+type;
										// },'json');
									}
			});
			return false;
		});   	
		function show_search(){
			var txt = $('#search-customer').val();
			var ul = $('#cust-search-list');
			ul.find('li').remove();
			ul.goLoad();
			$.post(baseUrl+'cashier/search_customers/'+txt,function(data){
				if(!$.isEmptyObject(data)){
					$.each(data,function(cust_id,val){
						var li = $('<li/>')
									.attr({'class':'cust-row','id':'cust-row-'+cust_id})
									.css({'cursor':'pointer','border-bottom':'1px solid #ddd'})
									.click(function(){
										var li = $(this);
										$.post(baseUrl+'cashier/get_customers/'+cust_id,function(cust){
											$.each(cust,function(key,val){
												var name = val.fname+' '+val.mname+' '+val.lname+' '+val.suffix
												$('#full_name').val(name);
												$('#cust_id').val(val.cust_id);
												$('#contact_no').val(val.phone);
												$('#email').val(val.email);
												selDeSel(li);
											});
										},'json');
									});
						$('<h4/>').css({'font-size':'14px','padding':'3px','margin':'3px'}).html(val.name).appendTo(li);
						$('<h4/>').css({'font-size':'12px','padding':'3px','margin':'3px'}).html(val.email).appendTo(li);
						li.appendTo(ul);
					});
					// $('.listings').perfectScrollbar('update');
				}
				ul.goLoad({load:false});
			},'json');
		}
		function selDeSel(li){
			var par = li.parent();
			par.find('li').removeClass('selected');
			li.addClass('selected');
		}	
		disEnCard();
		$('#amount_type').change(function(){
			disEnCard();			
		});
		function disEnCard(){
			var type = $('#amount_type').val();
			if(type == 'cash'){
				// $('.for-cards').attr('readOnly','readOnly');
				$('.for-cards').parent().hide();
				$('.for-cards').removeClass('rOkay');
			}
			else{
				$('.for-cards').parent().show();
				$('.for-cards').addClass('rOkay');
				// $('.for-cards').removeAttr('readOnly');
			}
			$('.for-cards').val('');
		}	
	<?php endif; ?>
});
</script>