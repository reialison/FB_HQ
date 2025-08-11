<script>
$(document).ready(function(){
	<?php if($use_js == 'loyaltyJs'): ?>
		var height = $(document).height();
		$('.div-content').height(height - 63);
		$('.div-content').rLoad({url:baseUrl+'loyalty/cards'});
		$('#cards-btn').click(function(){
			$('.div-content').rLoad({url:baseUrl+'loyalty/cards'});
			$('.ui-keyboard').remove();
			return false;
		});
		$('#card-lists-btn').click(function(){
			$('.div-content').rLoad({url:baseUrl+'loyalty/cards_list'});
			return false;
		});
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
	<?php elseif($use_js == 'cardsListJs'): ?>
		$('#loyalty_cards-tbl').rTable({
			loadFrom	: 	 'loyalty/get_cards',
			noEdit		: 	 true,			 	
			noAdd		: 	 true,			 	
		});	
	<?php elseif($use_js == 'addCardViewJs'): ?>
		var height = $(document).height();
		$('.div-content').height(height);
		$('#back-btn').click(function(){
			window.location = baseUrl+'loyalty';
			return false;
		});
	<?php elseif($use_js == 'addCardJs'): ?>
		$('#submit-btn').click(function(){
			$('#card-form').rOkay({
				btn_load 		: 	$('#submit-btn'),
				bnt_load_remove : 	false,
				asJson 			: 	true,
				onComplete 		: 	function(data){
										window.location = baseUrl+'loyalty/card_view/'+data.card_id;
										// $('.div-content').rLoad({url:baseUrl+'loyalty/card_view/'+data.card_id});
									}
			});
			return false;
		});
		$('#search-customer').focus();	
		$('#search-customer').on('keyup',function(){
			show_search();
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
												var name = val.fname+' '+val.mname+' '+val.lname+' '+val.suffix;
												var address = val.street_no+' '+val.street_address+' '+val.city+' '+val.region+' '+val.zip;
												$('#full_name').val(name);
												$('#cust_id').val(val.cust_id);
												$('#contact_no').val(val.phone);
												$('#email').val(val.email);
												$('#address').val(address);
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
		$('#add-cust-btn').click(function(){
			window.location = baseUrl+'pos_customers/customer_terminal';
		});

	<?php endif; ?>
});
</script>