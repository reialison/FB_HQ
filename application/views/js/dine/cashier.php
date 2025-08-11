<script>
$(document).ready(function(){
	<?php if($use_js == 'controlPanelJs'): ?>
		var docH = $('body').height();
		var colH = docH/2-160;
		$('#cashier-panel').height(colH);
		$('.cpanel-top').height(colH-70);
		$('.orders-lists').height(colH-170);
		$('#orders-search').height(colH-170);
		$('.orders-lists-load').height(colH-170);

		var scrolled=0;
		$('#sample').keyboard({ layout: 'qwerty' });
		startTime();
		terminal = $('#terminal-btn').attr('type');
		status = $('#status-btn').attr('type');
		if($('#types-btn').exists())
    		types = $('#types-btn').attr('type');
    	else
    		types = 'all';
		now = $('#now-btn').attr('type');
		search_id = 'none';
		server_id = '0';
		search_val = '';
		loadOrders(terminal,status,types,now,search_id,server_id);
		$('#manager-btn').click(function(){
			$.callManager({
				success : function(){
					window.location = baseUrl+'manager';
				}
			});
			return false;
		});
		$('#gift-card-btn').click(function(){
			window.location = baseUrl+'gift_cards/cashier_gift_cards';
			return false;
		});
		$('#loyalty-card-btn').click(function(){
			window.location = baseUrl+'loyalty';
			return false;
		});
		$('#customer-btn').click(function(){
			// window.location = baseUrl+'customers/cashier_customers';
			window.location = baseUrl+'pos_customers/customer_terminal';
			return false;
		});
		$('#cust-bank-btn').click(function(){
			window.location = baseUrl+'custs_bank';
			return false;
		});
		$('#time-clock-btn').click(function(){
			window.location = baseUrl+'shift';
			// window.location = baseUrl+'clock';
			return false;
		});
		$('#open-drawer-btn').click(function(){
			$.callManager({
				success : function(){
					var ur = baseUrl+'drawer/open_drawer';
					$.post(ur);
				}
			});
			return false;
		});
		$('#dine-in-btn').click(function(){
			window.location = baseUrl+'cashier/tables/dinein';
			return false;
		});
		$('#retail-btn').click(function(){
			window.location = baseUrl+'cashier/counter/retail'+'#retail';
			return false;
		});
		$('#delivery-btn').click(function(){
			window.location = baseUrl+'cashier/delivery';
			return false;
		});
		$('#pickup-btn').click(function(){
			window.location = baseUrl+'cashier/pickup';
			return false;
		});
		$('#counter-btn').click(function(){
			window.location = baseUrl+'cashier/counter/counter';
			return false;
		});
		$('#drive-thru-btn').click(function(){
			window.location = baseUrl+'cashier/counter/drivethru';
			return false;
		});
		$('#takeout-btn').click(function(){
			window.location = baseUrl+'cashier/counter/takeout';
			return false;
		});
		$('#back-office-btn').click(function(){
			// window.location = baseUrl;
			window.location = baseUrl+'dashboard';
			return false;
		});
		$('#logout-btn').click(function(){
			window.location = baseUrl+'site/go_logout';
			return false;
		});
		//panda order
		$('#panda-btn').click(function(){
			window.location = baseUrl+'cashier/counter/foodpanda';
			return false;
		});

		//eatigo
		$('#eatigo-btn').click(function(){
			window.location = baseUrl+'cashier/tables/eatigo';
			return false;
		});

		//bigdish
		$('#bigdish-btn').click(function(){
 			window.location = baseUrl+'cashier/tables/bigdish';
			return false;
		});

		//honestbee
		$('#honestbee-btn').click(function(){
			window.location = baseUrl+'cashier/counter/honestbee';
			return false;
		});

		$("#order-scroll-down-btn").on("click" ,function(){
		    var scrollH = parseFloat($(".orders-lists-load")[0].scrollHeight) - 260;
		    if(scrolled < scrollH){
			    scrolled=scrolled+150;
				$(".orders-lists").animate({
				        scrollTop:  scrolled
				});		    	
		    }
		});
		$("#order-scroll-up-btn").on("click" ,function(){
			if(scrolled > 0){
				scrolled=scrolled-150;
				$(".orders-lists").animate({
				        scrollTop:  scrolled
				});				
			}
		});
		$(".orders-lists").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		    scrolled=scrollTop-Math.round(delta);
		});
		$("#refresh-btn").click(function(){
			$('#server-search').hide();
			$('#orders-search').hide();
			terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		//types = $('#types-btn').attr('type');
    		if($('#types-btn').exists())
	    		types = $('#types-btn').attr('type');
	    	else
	    		types = 'all';
    		now = $('#now-btn').attr('type');
    		search_id = 'none';
    		server_id = '0';

    		loadOrders(terminal,status,types,now,search_id,server_id);

			//loadOrders();
			return false;
		});
		$("#recall-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					window.location = baseUrl+'cashier/counter/'+type+'/'+id+'#'+type;
				}else{
					rMsg(data.error,'error');
				}	
			},'json');

			return false;
		});
		$("#split-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					window.location = baseUrl+'cashier/split/'+type+'/'+id;
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			
			return false;
		});
		$("#combine-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					window.location = baseUrl+'cashier/combine/'+type+'/'+id;
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			return false;
		});
		$("#void-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var status = $('.order-view-list').attr('status');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					$.callManager({
						success : function(manager){
							var type = $('.order-view-list').attr('approver',manager.manager_id);
							loadDivs('reasons');
						}
					});			
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
				
			return false;
		});
		$("#change-to-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var status = $('.order-view-list').attr('status');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					$.callManager({
						success : function(manager){
							loadDivs('change-to');
						}
					});	
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			
			return false;
		});
		$(".change-to-btns").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var change_type = $(this).attr('ref');
			var btn = $(this);
			formData = 'type='+change_type;
			btn.goLoad();
			if(change_type == 'dinein'){
				bootbox.dialog({
				  message: baseUrl+'cashier/transfer_tables',
				  // title: 'Somthing',
				  className: 'manager-call-pop',
				  buttons: {
				    submit: {
				      label: "Transfer",
				      className: "btn  pop-manage pop-manage-green",
				      callback: function() {
				        var sales_id = id;
				        var to_table = $('#to-table').val();
				        formData = 'type='+change_type+'&tbl_id='+to_table;
				       	$.post(baseUrl+'cashier/change_order_to/'+id,formData,function(data){
				       		if(data.error == ""){
				       			$("#refresh-btn").trigger('click');
				       			rMsg('Success!  Order '+' #'+id+' Changed to '+type,'success');
				       			btn.goLoad({load:false});
				       		}
				       		else{
				       			rMsg(data.error,'error');
				       		}
				       	},'json');	
				      }
				    },
				    cancel: {
				      label: "CANCEL",
				      className: "btn pop-manage pop-manage-red",
				      callback: function() {
				        // Example.show("uh oh, look out!");
				        btn.goLoad({load:false});
				      }
				    }
				  }
				});
			}	
			else{
				$.post(baseUrl+'cashier/change_order_to/'+id,formData,function(data){
					if(data.error == ""){
						$("#refresh-btn").trigger('click');
						rMsg('Success!  Order '+' #'+id+' Changed to '+type,'success');
						btn.goLoad({load:false});
					}
					else{
						rMsg(data.error,'error');
					}
				},'json');				
			}		
			// alert(data);
			// });
			return false;
		});
		$("#cancel-other-reason-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var approver = $('.order-view-list').attr('approver');
			var prev = $('#now-btn').attr('type');
			var old = 0;
			if(prev == 'all_trans'){
				old = 1;
			}
			var reason = $(this).text();
			var btn = $(this);
			btn.goLoad();
			formData = 'approver='+approver;
			var box = bootbox.dialog({
			  message: baseUrl+'cashier/other_reason_pop',
			  title: 'Other Reason',
			  className: 'manager-call-pop',
			  buttons: {
			    submit: {
			      label: "Submit",
			      className: "btn btn-guest-submit pop-manage pop-manage-green",
			      callback: function() {
			      	formData += '&reason='+$('#other-reason-txt').val();
			      	$.post(baseUrl+'cashier/void_order/'+id+'/'+old,formData,function(data){
			      		if(data.error == ""){
			      			// $.post(baseUrl+'cashier/print_sales_receipt/'+id,function(data){
			      			// });	
			      			$("#refresh-btn").trigger('click');
			      			rMsg('Success!  Voided '+type+' #'+id,'success');
			      			btn.goLoad({load:false});
			      		}
			      		else{
			      			rMsg(data.error,'error');
			      			btn.goLoad({load:false});
			      		}
			      	},'json');
			        return true;
			      }
			    },
			    cancel: {
			      label: "CANCEL",
			      className: "btn pop-manage pop-manage-red",
			      callback: function() {
			        // Example.show("uh oh, look out!");
			        btn.goLoad({load:false});
			        return true;
			      }
			    }
			  }
			});
			return false;
		});
		$(".reason-btns").click(function(){
			var id = $('.order-view-list').attr('ref');
			var type = $('.order-view-list').attr('type');
			var approver = $('.order-view-list').attr('approver');

			var prev = $('#now-btn').attr('type');
			var old = 0;
			if(prev == 'all_trans'){
				old = 1;
			}
			var reason = $(this).text();
			var btn = $(this);
			btn.goLoad();
			formData = 'reason='+reason+'&approver='+approver;
			$.post(baseUrl+'cashier/void_order/'+id+'/'+old,formData,function(data){
				if(data.error == ""){
					// $.post(baseUrl+'cashier/print_sales_receipt/'+id,function(data){
					// });	
					$("#refresh-btn").trigger('click');
					rMsg('Success!  Voided '+type+' #'+id,'success');
					btn.goLoad({load:false});
				}
				else{
					rMsg(data.error,'error');
					btn.goLoad({load:false});
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$(".cancel-reason-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			$('#order-btn-'+id).trigger('click');
			return false;
		});
		$("#settle-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					window.location = baseUrl+'cashier/settle/'+id;
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			return false;
		});
		$("#cash-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					window.location = baseUrl+'cashier/settle/'+id+'#cash';
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			return false;
		});
		$("#credit-btn").click(function(){
			var id = $('.order-view-list').attr('ref');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					window.location = baseUrl+'cashier/settle/'+id+'#credit';
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			
			return false;
		});
		$("#receipt-btn").click(function(event){
			event.preventDefault();
			var id = $('.order-view-list').attr('ref');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					var prev = $('#now-btn').attr('type');
					var ur = baseUrl+'cashier/print_sales_receipt/'+id;
					if(prev == 'all_trans'){
						ur = baseUrl+'cashier/reprint_receipt_previous/'+id;
					}
					// alert(change_db);
					$.post(ur,'',function(data)
					{
						rMsg(data.msg,'success');
					},'json');
				}else{
					rMsg(data.error,'error');
				}	
			},'json');


			
		});
		$('#reprint-os-btn').click(function(){
			var id = $('.order-view-list').attr('ref');
			var tbl_id = $('.order-view-list').attr('table_id');
			// alert(table_id);

			//check if may activity yun table
			$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
				if(data.error == ""){
					var prev = $('#now-btn').attr('type');
					var ur = 0;
					if(prev == 'all_trans'){
						ur = 1;
					}
					$.post(baseUrl+'cashier/print_os/'+id+'/0/1/'+ur,'',function(data){
						rMsg('Order Slip Reprinted.','success');
					});
				}else{
					rMsg(data.error,'error');
				}	
			},'json');
			
			return false;
		});	
		$("#back-order-list-btn").click(function(){
			loadDivs('orders');
			return false;
		});
		function loadDivs(type){
			$('.center-loads-div').hide();
			$('.'+type+'-div').show();
		}
		var timeOut = 0;
		function loadOrders(terminal,status,types,now,search_id,server_id,search_val){
			terminal = 'my';
			$('.orders-lists-load').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');
			console.log(baseUrl+'cashier/orders/'+terminal+'/'+status+'/'+types+'/'+now+'/'+search_id+'/'+server_id);
			$.post(baseUrl+'cashier/orders/'+terminal+'/'+status+'/'+types+'/'+now+'/'+search_id+'/'+server_id,search_val,function(data){
				// alert(data);
				// console.log(data);
				clearTimeout(timeOut);
				if(data.has == 1){
					$('.orders-lists-load').html(data.code);
					$('.orders-lists').perfectScrollbar({suppressScrollX: true});
					var idd = data.ids;
					if(data.ids != null){
						// if(idd.length > 0){
						var last_id = "";
						$.each(data.ids,function(id,val){
							$('#order-btn-'+id).click(function(){
								loadDivs('orders-view');
								$('.order-view-list').attr('ref',id);
								$('.order-view-list').attr('type',val.type);
								$('.order-view-list').attr('status',val.status);
								$('.order-view-list').attr('table_id',val.table_id);
								if(val.status == 'voided'){
									$('#recall-btn').attr('disabled','disabled');
									$('#split-btn').attr('disabled','disabled');
									$('#combine-btn').attr('disabled','disabled');
									$('#settle-btn').attr('disabled','disabled');
									// $('#receipt-btn').attr('disabled','disabled');
									$('#reprint-os-btn').attr('disabled','disabled');
									$('#cash-btn').attr('disabled','disabled');
									$('#credit-btn').attr('disabled','disabled');
									$('#void-btn').attr('disabled','disabled');
									$('#change-to-btn').attr('disabled','disabled');
								}else{
									if(now == 'all_trans'){
										$('#recall-btn').attr('disabled','disabled');
										$('#split-btn').attr('disabled','disabled');
										$('#combine-btn').attr('disabled','disabled');
										$('#settle-btn').attr('disabled','disabled');
										// $('#receipt-btn').attr('disabled','disabled');
										$('#cash-btn').attr('disabled','disabled');
										$('#credit-btn').attr('disabled','disabled');
										// $('#void-btn').attr('disabled','disabled');
									}
									else{
										
										//FOR TAGUEGARAO DISABLE RECALL WHEN STETTLED
											if(val.status == 'settled'){
												$('#recall-btn').attr('disabled','disabled');
												$('#split-btn').attr('disabled','disabled');
												$('#combine-btn').attr('disabled','disabled');
												$('#settle-btn').attr('disabled','disabled');
												$('#cash-btn').attr('disabled','disabled');
												$('#credit-btn').attr('disabled','disabled');
												$('#change-to-btn').attr('disabled','disabled');
												$('#reprint-os-btn').attr('disabled','disabled');
											}
											else{
												$('#recall-btn').removeAttr('disabled');
												$('#split-btn').removeAttr('disabled');
												$('#combine-btn').removeAttr('disabled');
												$('#settle-btn').removeAttr('disabled');
												$('#receipt-btn').removeAttr('disabled');
												$('#cash-btn').removeAttr('disabled');
												$('#credit-btn').removeAttr('disabled');
												$('#void-btn').removeAttr('disabled');
												$('#change-to-btn').removeAttr('disabled');
											}

										//ORIGINAL
											// $('#recall-btn').removeAttr('disabled');
											// $('#split-btn').removeAttr('disabled');
											// $('#combine-btn').removeAttr('disabled');
											// $('#settle-btn').removeAttr('disabled');
											// $('#receipt-btn').removeAttr('disabled');
											// $('#cash-btn').removeAttr('disabled');
											// $('#credit-btn').removeAttr('disabled');
											// $('#void-btn').removeAttr('disabled');
									}
								}
								$('.order-view-list').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin"></i></div></center>');
								var prev = 0;
								if(now == 'all_trans')
									prev = 1;
								$.post(baseUrl+'cashier/order_view/'+id+'/'+prev,function(data){
									console.log(data);
									$('.order-view-list').html(data.code);
									$('.order-view-list .body').perfectScrollbar({suppressScrollX: true});
									// alert(data);
								// });
								},'json');
								return false;
							});
							last_id = id;
						});
						console.log(last_id);
						$('.orders-lists').attr('last_id',last_id);
					}
					checkNewOrder();
					loadDivs('orders');
				}
				else{
					$('.orders-lists-load').html("<center> No Orders Found. </center>");
					loadDivs('orders');
				}
			// });
			},'json');
		}
		function checkNewOrder(){
			var last_id = $('.orders-lists').attr('last_id');
			// if (typeof last_id !== typeof undefined && last_id !== false) {
			// 	last_id = "";
			// }
			console.log(last_id);
				// last_id = "";
	    		var status = $('#status-btn').attr('type');
				var terminal = $('#terminal-btn').attr('type');
				if(status == 'open'){
		    		if($('#types-btn').exists())
			    		types = $('#types-btn').attr('type');
			    	else
			    		types = 'all';
		    		now = $('#now-btn').attr('type');
		    		search_id = 'none';
		    		server_id = '0';
		    		loadNewOrders(terminal,status,types,now,search_id,server_id,last_id);
				}
			timeOut = setTimeout(function(){
		  		checkNewOrder();
			}, 1000);	
		}
		function loadNewOrders(terminal,status,types,now,search_id,server_id,last_id){
			terminal = 'my';
			// console.log('cashier/new_orders/'+terminal+'/'+status+'/'+types+'/'+now+'/'+search_id+'/'+server_id+'/'+last_id);
			$.post(baseUrl+'cashier/new_orders/'+terminal+'/'+status+'/'+types+'/'+now+'/'+search_id+'/'+server_id+'/'+last_id,function(data){
				// console.log(data);
				var ids = data.ids;
				if(!$.isEmptyObject(ids)){
					// console.log($.isEmptyObject(ids));
					if(last_id == ""){
						$('.orders-lists-load').find('.row').append(data.code);						
					}
					else{
						$('.orders-lists-load').closest('.row').find('.col-md-6').first().before(data.code);						
					}
					$.each(data.ids,function(id,val){
						$('#order-btn-'+id).click(function(){
							loadDivs('orders-view');
							$('.order-view-list').attr('ref',id);
							$('.order-view-list').attr('type',val.type);
							$('.order-view-list').attr('status',val.status);
							if(val.status == 'voided'){
								$('#recall-btn').attr('disabled','disabled');
								$('#split-btn').attr('disabled','disabled');
								$('#combine-btn').attr('disabled','disabled');
								$('#settle-btn').attr('disabled','disabled');
								// $('#receipt-btn').attr('disabled','disabled');
								$('#cash-btn').attr('disabled','disabled');
								$('#credit-btn').attr('disabled','disabled');
								$('#void-btn').attr('disabled','disabled');
							}else{
								if(now == 'all_trans'){
									$('#recall-btn').attr('disabled','disabled');
									$('#split-btn').attr('disabled','disabled');
									$('#combine-btn').attr('disabled','disabled');
									$('#settle-btn').attr('disabled','disabled');
									// $('#receipt-btn').attr('disabled','disabled');
									$('#cash-btn').attr('disabled','disabled');
									$('#credit-btn').attr('disabled','disabled');
									// $('#void-btn').attr('disabled','disabled');
								}
								else{
									
									//FOR TAGUEGARAO DISABLE RECALL WHEN STETTLED
										if(val.status == 'settled'){
											$('#recall-btn').attr('disabled','disabled');
											$('#split-btn').attr('disabled','disabled');
											$('#combine-btn').attr('disabled','disabled');
											$('#settle-btn').attr('disabled','disabled');
											$('#cash-btn').attr('disabled','disabled');
											$('#credit-btn').attr('disabled','disabled');
										}
										else{
											$('#recall-btn').removeAttr('disabled');
											$('#split-btn').removeAttr('disabled');
											$('#combine-btn').removeAttr('disabled');
											$('#settle-btn').removeAttr('disabled');
											$('#receipt-btn').removeAttr('disabled');
											$('#cash-btn').removeAttr('disabled');
											$('#credit-btn').removeAttr('disabled');
											$('#void-btn').removeAttr('disabled');
										}

									//ORIGINAL
										// $('#recall-btn').removeAttr('disabled');
										// $('#split-btn').removeAttr('disabled');
										// $('#combine-btn').removeAttr('disabled');
										// $('#settle-btn').removeAttr('disabled');
										// $('#receipt-btn').removeAttr('disabled');
										// $('#cash-btn').removeAttr('disabled');
										// $('#credit-btn').removeAttr('disabled');
										// $('#void-btn').removeAttr('disabled');
								}
							}
							$('.order-view-list').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin"></i></div></center>');
							var prev = 0;
							if(now == 'all_trans')
								prev = 1;
							$.post(baseUrl+'cashier/order_view/'+id+'/'+prev,function(data){
								$('.order-view-list').html(data.code);
								$('.order-view-list .body').perfectScrollbar({suppressScrollX: true});
								// alert(data);
							// });
							},'json');
							return false;
						});
					
						$('.orders-lists').attr('last_id',id);
					});	
				}
			},'json');	
			// });	
		}
		//timeticker
		function startTime(){
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();

            // add a zero in front of numbers<10
            m = checkTime(m);
            s = checkTime(s);

            //Check for PM and AM
            var day_or_night = (h > 11) ? "PM" : "AM";

            //Convert to 12 hours system
            if (h > 12)
                h -= 12;

            //Add time to the headline and update every 500 milliseconds
            $('#time').html(h + ":" + m + ":" + s + " " + day_or_night);
            setTimeout(function() {
                startTime();
            }, 500);
        }
        function checkTime(i){
            if (i < 10)
            {
                i = "0" + i;
            }
            return i;
        }
	    ///////////////////////////////Jed//////////////////////////////////
	    ////////////////////////////////////////////////////////////////////
    	$('#terminal-btn').click(function(){
    		$('#server-search').hide();
    		$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'my'){
    			act = 'all';
    			$(this).attr('type',act);
    			$('#terminal_text').html('<i class="fa fa-users fa-2x fa-fw"></i><br>ALL');
    		}else{
    			act = 'my';
    			$(this).attr('type',act);
    			$('#terminal_text').html('<i class="fa fa-desktop fa-2x fa-fw"></i><br>MY');
    		}

    		terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		if($('#types-btn').exists())
	    		types = $('#types-btn').attr('type');
	    	else
	    		types = 'all';
    		now = $('#now-btn').attr('type');
    		search_id = 'none';
    		server_id = '0';

    		loadOrders(terminal,status,types,now,search_id,server_id);
    	});
    	$('#status-btn').click(function(){
    		$('#server-search').hide();
    		$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'open'){
    			act = 'settled';
    			$(this).attr('type',act);
    			$('#status_text').html('<i class="fa fa-arrow-down fa-2x fa-fw"></i><br><?php echo lng("cp_btn_settled") ?>');
    		}else if(type == 'settled'){
    			act = 'cancel';
    			$(this).attr('type',act);
    			$('#status_text').html('<i class="fa fa-ban fa-2x fa-fw"></i><br><?php echo lng("cp_btn_cancelled") ?>');
    		}else if(type == 'cancel'){
    			act = 'voided';
    			$(this).attr('type',act);
    			$('#status_text').html('<i class="fa fa-times fa-2x fa-fw"></i><br><?php echo lng("cp_btn_voided") ?>');
    		}else{
    			act = 'open';
    			$(this).attr('type',act);
    			$('#status_text').html('<i class="fa fa-arrow-up fa-2x fa-fw"></i><br><?php echo lng("cp_btn_open") ?>');
    		}

    		terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		if($('#types-btn').exists())
	    		types = $('#types-btn').attr('type');
	    	else
	    		types = 'all';

    		now = $('#now-btn').attr('type');
    		search_id = 'none';
    		server_id = '0';
    		loadOrders(terminal,status,types,now,search_id,server_id);
    	});

    	$('#types-btn').click(function(){
    		$('#server-search').hide();
    		$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'all'){
    			act = 'dinein';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-sign-in fa-2x fa-fw"></i><br>DINE IN');
    		}
    		else if(type == 'dinein'){
    			act = 'delivery';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-truck fa-2x fa-fw"></i><br>DELIVERY');
    		}
    		else if(type == 'delivery'){
    			act = 'counter';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-keyboard-o fa-2x fa-fw"></i><br>COUNTER');
    		}
    		else if(type == 'counter'){
    			act = 'pickup';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-briefcase fa-2x fa-fw"></i><br>PICKUP');
    		}
    		else if(type == 'pickup'){
    			act = 'takeout';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-sign-out fa-2x fa-fw"></i><br>TAKEOUT');
    		}
    		else if(type == 'takeout'){
    			act = 'drivethru';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-road fa-2x fa-fw"></i><br>DRIVE-THRU');
    		}else if(type == 'drivethru'){
    			act = 'all';
    			$(this).attr('type',act);
    			$('#types_text').html('<i class="fa fa-book fa-2x fa-fw"></i><br>ALL TYPES');
    		}

    		terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		types = $('#types-btn').attr('type');
    		now = $('#now-btn').attr('type');
    		search_id = 'none';
    		server_id = '0';

    		loadOrders(terminal,status,types,now,search_id,server_id);
    	});
		$('#now-btn').click(function(){
			$('#server-search').hide();
			$('#orders-search').hide();
    		btn = $(this).attr('btn');
    		type = $(this).attr('type');
    		if(type == 'all_trans'){
    			act = 'now';
    			$(this).attr('type',act);
    			$('#day_text').html('<i class="fa fa-clock-o fa-2x fa-fw"></i><br><?php echo lng("cp_btn_today") ?>');
    		}else{
    			act = 'all_trans';
    			$(this).attr('type',act);
    			$('#day_text').html('<i class="fa fa-clock-o fa-2x fa-fw"></i><br><?php echo lng("cp_btn_previous") ?>');
    		}

    		terminal = $('#terminal-btn').attr('type');
    		status = $('#status-btn').attr('type');
    		if($('#types-btn').exists())
	    		types = $('#types-btn').attr('type');
	    	else
	    		types = 'all';
    		now = $('#now-btn').attr('type');
    		search_id = 'none';
    		server_id = '0';

    		loadOrders(terminal,status,types,now,search_id,server_id);
    	});
    	$('#look-btn').click(function(){
    		$('.orders-lists').hide();
    		$('.orders-view-div').hide();
    		$('#orders-search').show();
    		$('#server-search').hide();
    	});
    	$('#server-btn').click(function(){
    		$('#orders-search').hide();
    		$('.orders-lists').hide();
    		$('.orders-view-div').hide();
    		$('#server-search').show();
    	});
    	$('#search-order-btn').click(function(event){
    		search_val = $('#search-form-order').serialize();
    		$('#orders-search').hide();
    		loadOrders(terminal,status,types,now,search_id,server_id,search_val);
    		return false;
    	});	
    	$('#go-search-order').click(function(event){

    		search_id = $('#search-order').val();
    		$('#orders-search').hide();
    		loadOrders(terminal,status,types,now,search_id,server_id);
    		$('#search-order').val('');
    		event.preventDefault();
    	});
    	$('#search-server-btn').click(function(event){

    		server_id = $('#user').val();
    		if(server_id == ''){
    			rMsg('Select a food server.','warning');
    		}else{

	    		$('#server-search').hide();
	    		loadOrders(terminal,status,types,now,search_id,server_id);
	    		$('#user').val('');
    		}
    		// $('#search-order').val('');
    		event.preventDefault();
    	});
    <?php elseif($use_js == 'searchPanelJs'): ?>
    	$('#search-btn').click(function(){
    		$('#search-form').rOkay({
    			passTo 		: 'prints/order_box',	
    			onComplete 	: function (data){
    							alert(data);
    						}
    		});
    		return false;
    	});	
    	$('#back-btn').click(function(){
    		window.location = baseUrl+'cashier';
    		return false;
    	});
    	$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
    <?php elseif($use_js == 'counterJs'): ?>
		// $(':button').click(function(){
		// 	$.beep();
		// 	// alert('here');
		// });
		
		$(document).scannerDetection({
			avgTimeByChar: 40,
			onComplete: function(barcode, qty){ 
				// console.log(barcode);
				// console.log(qty);
				var formData = 'barcode='+barcode;
				// alert(formData);
				scannedRetailItem(formData);
			},
			onError: function(string){}
		});
		$('#counter').disableSelection();
		var scrolled=0;
		var transScroll=0;
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		loadMenuCategories();
		loadTransCart();
		loadTransChargeCart();
		var hashTag = window.location.hash;
		if(hashTag == '#retail'){
			remeload('retail');
		}
		$('#free-btn').click(function(){
			var id  = $('.selected').attr("ref");
			if (typeof id !== typeof undefined && id !== false) {
				$.callManager({
					success : function(manager){
						var man_user = manager.manager_username;
						var man_id = manager.manager_id;
							
						
						var formData = 'free_user_id='+man_id;
						$('body').goLoad2();
						$.post(baseUrl+'cashier/update_free_menu/'+id,formData,function(data){
							$('#trans-row-'+id+' .cost').text(0);
							var text = $('#trans-row-'+id).find('.name').text();
							// alert(text);
							$('#trans-row-'+id+' .name').html('<i class="fa fa-asterisk"></i> '+text);
							transTotal();
							rMsg('Updated Menu as free','success');
							$('body').goLoad2({load:false});
						});
					}
				});
			}
			return false;
		});
		$('#serve-no-btn').click(function(){
			$.callServerNo({
				success : function(serve_no){
					$('#ord-serve-no').text('Serve No.'+serve_no);	
					rMsg('Serve # Updated to '+serve_no,'success');
				}
			});
			return false;
		});
		$('#submit-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			var printOS = $('#print-os-btn').attr('doprint');
			var doPrintOS = false;
			if (typeof printOS !== typeof undefined && printOS !== false) {
				doPrintOS = printOS;
			}
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-submit');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/null/false/0/null/null/'+print+'/null/'+doPrintOS,function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						if(data.act == 'add'){
							newTransaction(false,data.type);
							if(btn.attr('id') == 'submit-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
						}
						else{
							newTransaction(true,data.type);
						}
					}

					$("#zero-rated-btn").removeClass('counter-btn-green');
					$("#zero-rated-btn").removeClass('zero-rated-active');
					$("#zero-rated-btn").addClass('counter-btn-red');
					$('.center-div .foot .foot-det').css({'background-color':'#fff'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
					
					$('.counter-center .body').perfectScrollbar('update');
					$(".counter-center .body").scrollTop(0);
				},'json').fail( function(xhr, textStatus, errorThrown) {
						   alert(xhr.responseText);
						});	
				// alert(data);
				// });
			}
			return false;
		});
		$('#send-trans-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			var printOS = $('#print-os-btn').attr('doprint');
			var doPrintOS = false;
			if (typeof printOS !== typeof undefined && printOS !== false) {
				doPrintOS = printOS;
			}
			if($("#trans-server-txt").text() == ""){
				rMsg('Select Food Server','error');
			}
			else{
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/null/false/0/null/null/'+print+'/null/'+doPrintOS,function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						if(data.act == 'add'){
							newTransaction(false,data.type);
							if(btn.attr('id') == 'send-trans-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
						}
						else{
							newTransaction(true,data.type);
						}
					}

					$("#zero-rated-btn").removeClass('counter-btn-green');
					$("#zero-rated-btn").removeClass('zero-rated-active');
					$("#zero-rated-btn").addClass('counter-btn-red');
					$('.center-div .foot .foot-det').css({'background-color':'#fff'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
					
					$('.counter-center .body').perfectScrollbar('update');
					$(".counter-center .body").scrollTop(0);
				},'json').fail( function(xhr, textStatus, errorThrown) {
						   alert(xhr.responseText);
						});	
				// alert(data);
				// });
			}		
			return false;	
		});
		$('#print-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i> <br>Billing');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i> <br>Billing');
			}
		});
		$('#print-os-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i><br>ORDER SLIP');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i><br>ORDER SLIP');
			}
		});
		$('#hold-all-btn').click(function(){
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans',function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					if(data.act == 'add'){
						newTransaction(false,data.type);
						if(btn.attr('id') == 'submit-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
					}
					else{
						newTransaction(true,data.type);
					}
				}
			},'json');
			return false;
		});
		$('#settle-btn').click(function(){
			var btn = $(this);
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-settle');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
						if(data.error != null){
							rMsg(data.error,'error');
							btn.prop('disabled', false);
						}
						else{
							newTransaction(false);
							if(btn.attr('id') == 'settle-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
							window.location = baseUrl+'cashier/settle/'+data.id;
						}
					},'json').fail( function(xhr, textStatus, errorThrown) {
						   alert(xhr.responseText);
						});	
				// alert(data);
				// });
			}
			return false;
		});
		$('#cash-btn').click(function(){
			//alert('aw');
			var btn = $(this);
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-cash');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						if(btn.attr('id') == 'cash-btn'){
							//rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/settle/'+data.id+'#cash';
					}
				},'json').fail( function(xhr, textStatus, errorThrown) {
						   alert(xhr.responseText);
						});	
			}
			return false;
		});
		$('#credit-btn').click(function(){
			//alert('aw');
			var btn = $(this);
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-credit');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				var btn = $(this);
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						if(btn.attr('id') == 'credit-btn'){
							//rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/settle/'+data.id+'#credit';
					}
				},'json').fail( function(xhr, textStatus, errorThrown) {
						   alert(xhr.responseText);
						});	
			}
			return false;
		});
		$('#waiter-btn').click(function(){
			loadsDiv('waiter',null,null,null);
			loadWaiters();
			return false;
		});
		$('#remove-waiter-btn').click(function(){
			$.post(baseUrl+'cashier/update_trans/waiter_id/null/true',function(data){
				$('#trans-server-txt').text('').hide();
				rMsg('Food Server Removed.','success');
			},'json');
			return false;
		});
		$('#loyalty-btn').click(function(){
			// var sel = $('.selected');
			// if(sel.exists()){
			// 	if(sel.hasClass('loaded')){
			// 		$.callManager({
			// 			success : function(){
			// 				loadsDiv('qty',null,null,null);
			// 			}	
			// 		});		
			// 	}
			// 	else	
			loadsDiv('loyalty',null,null,null);
			// }
			return false;
		});
		///FOR RETAIL
			$('#retail-btn').click(function(){
				if($(this).hasClass('counter-btn-red')){
					$(this).removeClass('counter-btn-red');
					$(this).addClass('counter-btn-green');
					loadItemCategories();
					loadsDiv('retail');
					$('#scan-code').focus();
					$('#go-scan-code').removeClass('counter-btn-orange');
					$('#go-scan-code').addClass('counter-btn-green');
				}
				else{
					$(this).removeClass('counter-btn-green');
					$(this).addClass('counter-btn-red');
					loadMenuCategories();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					$('#go-scan-code').removeClass('counter-btn-green');
					$('#go-scan-code').addClass('counter-btn-orange');
					$('#scan-code').blur();
				}
				return false;
			});
			$('#go-scan-code').click(function(){
				if($(this).hasClass('counter-btn-orange')){
					$(this).removeClass('counter-btn-orange');
					$(this).addClass('counter-btn-green');
					$('#scan-code').focus();
				}
				else{
					$(this).removeClass('counter-btn-green');
					$(this).addClass('counter-btn-orange');
					$('#scan-code').blur();
				}
				return false;
			});
			$('#scan-code').on('keyup',function(e){
				if(e.keyCode == 13){
					var code = $(this).val();
					if(code != ""){
						$.post(baseUrl+'cashier/scan_code/'+code,function(data){
							if(data.error == ""){
								var opt = data.item;
								addRetailTransCart(opt.item_id,opt);
								// $.beep();
							}
							else{
								rMsg(data.error,'error');
								// $.beep({'status':'error'});
								
							}
						},'json');
					} 
					else{
						rMsg('Item not found.','error');
						// $.beep({'status':'error'});
					    
					}
					$('#scan-code').val('');
				}
			});
			$('#go-search-item').click(function(){
				var btn = $(this);
				var search = $('#search-item').val();
				if(search != ""){
					var formData = 'search='+search;
					loadRetailItemList(formData,btn);
				}
				else{
					rMsg('Nothing to search.','error');
				}
				return false;
			});
			$('#customer-btn').click(function(){
				var btn = $(this);
				loadsDiv('customers',null,null,null);
				return false;
			});
			$('#remove-customer').click(function(){
				$.post(baseUrl+'cashier/update_trans_customer/',function(data){
					$('#trans-customer').text('').hide();
					rMsg('REMOVED Customer ID','success');
				});
			});
			$('#go-search-customer').click(function(){
				var btn = $(this);
				var search = $('#search-customer').val();
				if(search != ""){
					var formData = 'search='+search;
					loadRetailCustomerList(formData,btn);
				}
				else{
					rMsg('Nothing to search.','error');
				}
				return false;
			});
		$('#qty-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				if(sel.hasClass('loaded')){
					$.callManager({
						success : function(){
							loadsDiv('qty',null,null,null);
						}	
					});		
				}
				else	
					loadsDiv('qty',null,null,null);
			}
			return false;
		});
		$('#qty-btn-cancel,#qty-btn-done').click(function(){
			if($('#retail-btn').hasClass('counter-btn-red')){
				$('.loads-div').hide();				
				$('.menus-div').show();
			}
			else{
				remeload('retail');
			}
			return false;
		});
		$(".edit-qty-btn").click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");
			var formData = 'value='+btn.attr('value')+'&operator='+btn.attr('operator');
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/update_trans_qty/'+id,formData,function(data){
				var qty = data.qty;
				$('#trans-row-'+id+' .qty').text(qty);
				btn.prop('disabled', false);
				transTotal();
			},'json');
			return false;
		});
		$("#multiply-items").click(function(event){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");
			var val = $('#times-qty').val();
			if(val == '' || val == ' '){
				rMsg('Please input a number.','error');
			}else if(val < 0){
				rMsg('Quantity should be greater than 0','error');
				$('#times-qty').val('');
			}else{
				var formData = 'value='+val+'&operator='+btn.attr('operator');
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/update_trans_qty/'+id,formData,function(data){
					var qty = data.qty;
					$('#trans-row-'+id+' .qty').text(qty);
					// if(data.qty == 1){
					// 	$('#trans-row-'+id+' .price').text('');
					// 	$('#trans-row-'+id+' .cost').text(data.price);
					// }else{
					// 	$('#trans-row-'+id+' .price').text('@'+data.price);
					// 	$('#trans-row-'+id+' .cost').text(data.subtotal);
					// }

					btn.prop('disabled', false);
					transTotal();
					$('#times-qty').val('');
				},'json');
			}
			// $('#qty-btn-done').click();
			return false;
		});
		$("#zero-rated-btn").click(function(){
			var btn = $(this);
			$.callManager({
				success : function(manager){
							var man_user = manager.manager_username;
							var man_id = manager.manager_id;
							if(!btn.hasClass('zero-rated-active')){
								$.post(baseUrl+'cashier/update_trans/zero_rated/1',function(data){
									btn.removeClass('counter-btn-red');
									btn.addClass('counter-btn-green');
									btn.addClass('zero-rated-active');
									$('.center-div .foot .foot-det').css({'background-color':'#FDD017'});
									$('.center-div .foot .foot-det .receipt').css({'color':'#fff'});
									transTotal();
								});
							}
							else{
								$.post(baseUrl+'cashier/update_trans/zero_rated/0',function(data){
									btn.removeClass('counter-btn-green');
									btn.removeClass('zero-rated-active');
									btn.addClass('counter-btn-red');
									$('.center-div .foot .foot-det').css({'background-color':'#fff'});
									$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
									transTotal();
								});
							}
				}
			});		
			return false;
		});
		$('#add-discount-btn').click(function(){
			loadsDiv('sel-discount',null,null,null);
			loadDiscounts();
			return false;
		});
		$('#add-disc-person-btn').click(function(){
			$('#add-disc-person-btn').goLoad();
			var noError = $('#disc-form').rOkay({
		     				btn_load		: 	$(this),
		     				goSubmit		: 	false,
		     				bnt_load_remove	: 	true
						  });
			if(noError){
				var guests = $('#disc-guests').val();
				var ref = $(this).attr('ref');
				var formData = $('#disc-form').serialize();
				formData = formData+'&type='+ref+'&guests='+guests;

				$.post(baseUrl+'cashier/add_person_disc',formData,function(data){
					$('#add-disc-person-btn').goLoad({load:false});
					if(data.error==""){
						$('.disc-persons-list-div').html(data.code);
						$.each(data.items,function(code,opt){
							$("#disc-person-rem-"+code).click(function(){
								var lin = $(this).parent().parent();
								$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
									lin.remove();
									rMsg('Person Removed.','success');
									transTotal();
								});
								return false;
							});
						});
						transTotal();
					}
					else{
						rMsg(data.error,'error');
					}
				},'json');
			}
			return false;
		})
		$('.disc-btn-row').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$('.disc-btn-row').goLoad2();
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				$('.disc-btn-row').goLoad2({load:false});
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#edit-order-guest-no').click(function(){
			$.callEditGuests({
				success : function(guest){
					$('#ord-guest-no').text(guest);	
					$('#disc-guests').val(guest);	
					rMsg('Guest has been updated to'+guest,'success');
				}
			});
			return false;
		});
		$('#prcss-disc').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			console.log(ref);
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$('.disc-btn-row').goLoad2();
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				$('.disc-btn-row').goLoad2({load:false});
				$('#ord-guest-no').text(guests);
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#remove-disc-btn').click(function(){
			var disc_code = $('#disc-disc-code').val();
			$.post(baseUrl+'cashier/del_trans_disc/'+disc_code,function(data){
				rMsg('Discounts Removed','success');
				$('.disc-person').remove();
				$('#disc-form')[0].reset();
				$('#disc-guests').val('');
				transTotal();
			});
			return false;
		});
		$('#remove-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				if(sel.hasClass('loaded')){
					$.callManager({
						success : function(manager){
							var man_user = manager.manager_username;
							var man_id = manager.manager_id;
							$.callReasons({
								submit : function(reason){
									var id = sel.attr('ref');
									var cart = 'trans_cart';
									var type = 'menu';
									
									if(sel.hasClass('trans-sub-row')){
										cart = 'trans_mod_cart';
										type = 'mod';
									}
									else if(sel.hasClass('trans-charge-row')){
										cart = 'trans_charge_cart';
										type = 'charge';
									}
									var retail = false;
									if(sel.hasClass('retail-item')){
										retail = true;
										type = 'retail';
									}
									
									$.post(baseUrl+'cashier/record_delete_line/'+cart+'/'+id+'/'+type+'/'+reason+'/'+man_id+'/'+man_user,function(data){
										// alert(data);
										$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
											sel.prev().addClass('selected');
											sel.remove();
											if(cart == 'trans_cart' && retail === false){
												$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
													var cat_id = $(".category-btns:first").attr('ref');
													var cat_name = $(".category-btns:first").text();
													var val = {'name':cat_name};
													loadsDiv('menus',cat_id,val,null);
													$('.trans-sub-row[trans-id="'+id+'"]').remove();
												});
											}
											$('.counter-center .body').perfectScrollbar('update');
											transTotal();
										},'json');
									});

								}
							});
						}
					});
				}
				else{
					var id = sel.attr('ref');
					var cart = 'trans_cart';
					if(sel.hasClass('trans-sub-row'))
						cart = 'trans_mod_cart';
					else if(sel.hasClass('trans-charge-row'))
						cart = 'trans_charge_cart';
					var retail = false;
					if(sel.hasClass('retail-item'))
						retail = true;
					if(!sel.hasClass('trans-remarks-row')){
						$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
							sel.prev().addClass('selected');
							sel.remove();
							if(cart == 'trans_cart' && retail === false){
								$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
									var cat_id = $(".category-btns:first").attr('ref');
									var cat_name = $(".category-btns:first").text();
									var val = {'name':cat_name};
									loadsDiv('menus',cat_id,val,null);
									$('.trans-sub-row[trans-id="'+id+'"]').remove();
								});
							}
							$('.counter-center .body').perfectScrollbar('update');
							transTotal();
						},'json');
					}
					else{
						$.post(baseUrl+'cashier/remove_trans_remark/'+id,function(data){
							sel.prev().addClass('selected');
							$('#trans-remarks-row-'+id).remove();
							$('.counter-center .body').perfectScrollbar('update');
						// });
						},'json');
					}
				}
			}
			return false;
		});
		$('#charges-btn').click(function(){
			$('.charges-div .title').text('Select Charges');
			loadCharges();
			loadsDiv('charges',null,null,null);
			return false;
		});
		$('#remarks-btn').click(function(){
			var sel = $('.selected');
			$('#line-remarks').val('');
			if(sel.exists()){
				loadsDiv('remarks',null,null,null);
			}
			return false;
		});
		$('#add-remark-btn').click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");

			var noError = $('#remarks-form').rOkay({
				 				btn_load		: 	$(this),
				 				goSubmit		: 	false,
				 				bnt_load_remove	: 	true
							});
			if(noError){
				var formData = $('#remarks-form').serialize();		
				btn.goLoad();
				$.post(baseUrl+'cashier/add_trans_remark/'+id,formData,function(data){
					makeRemarksItemRow(id,data.remarks);
					
					btn.goLoad({load:false});
				},'json');	
			}
			return false;
		});
		$('#tax-exempt-btn').click(function(){
			$.callManager({
				success : function(){
							$.post(baseUrl+'cashier/trans_exempt_to_tax',function(data){
								alert(data);
								transTotal();
								checkWagon('trans_cart');
							// },'json');	
							});	
						  }
			});						  
			return false;
		});
		$('#manager-btn').click(function(){
			window.location = baseUrl+'manager';
			return false;
		});
		$('#logout-btn').click(function(){
			window.location = baseUrl+'site/go_logout';
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$("#menu-cat-scroll-down").on("click" ,function(){
		    var inHeight = $(".menu-cat-container")[0].scrollHeight;
		    var divHeight = $(".menu-cat-container").height();
		    var trueHeight = inHeight - divHeight;
	        if((scrolled + 150) > trueHeight){
	        	scrolled = trueHeight;
	        }
	        else{
	    	    scrolled=scrolled+150;				    	
	        }
		    // scrolled=scrolled+100;
			$(".menu-cat-container").animate({
			        scrollTop:  scrolled
			},200);
		});
		$("#menu-cat-scroll-up").on("click" ,function(){
			if(scrolled > 0){
				scrolled=scrolled-150;
				$(".menu-cat-container").animate({
				        scrollTop:  scrolled
				},200);
			}
		});
		$(".menu-cat-container").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		$(".items-lists").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		$('#search-menu').on('keyup',function(){
			var search = $(this).val();
			if(search != ""){
				remeload('menu');
				$('.scrollers-menu').remove();
				$('.loads-div').hide();
				$('.menus-div').show();
				$('.menus-div .title').text('Search: '+search);
				$('.menus-div .items-lists').html('');
				var formData = 'search='+search;
				$.post(baseUrl+'cashier/get_menus_search_sorted',formData,function(data){
					var div = $('.menus-div .items-lists').append('<div class="row"></div>');
			 		$.each(data,function(key,opt){
			 			
			 			var menu_id = opt.id;
			 			var sCol = $('<div class="col-md-3"></div>');
			 			$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-block btn-default'})
			 			.text(opt.name)
			 			.appendTo(sCol)
			 			.click(function(){
			 				if(opt.free == 1){
				 				$.callManager({
				 					success : function(){
								 				addTransCart(menu_id,opt);
				 							  }	
				 				});
			 				}
			 				else{
			 					addTransCart(menu_id,opt);
			 				}
			 				return false;
			 			});
			 			sCol.appendTo(div);
			 		});
			 		$('.menus-div .items-lists').after('<div id="scrollers-menu"><div class="row"><div class="col-md-6 text-left"><button id="menu-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="menu-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
			 		$("#menu-item-scroll-down").on("click" ,function(){
			 		 //    scrolled=scrolled+100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });

	 				    var inHeight = $(".items-lists")[0].scrollHeight;
	 				    var divHeight = $(".items-lists").height();
	 				    var trueHeight = inHeight - divHeight;
	 			        if((scrolled + 150) > trueHeight){
	 			        	scrolled = trueHeight;
	 			        }
	 			        else{
	 			    	    scrolled=scrolled+150;				    	
	 			        }
	 				    // scrolled=scrolled+100;
	 					$(".items-lists").animate({
	 					        scrollTop:  scrolled
	 					},200);
			 		});
			 		$("#menu-item-scroll-up").on("click" ,function(){
			 			// scrolled=scrolled-100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });
			 			if(scrolled > 0){
			 				scrolled=scrolled-150;
			 				$(".items-lists").animate({
			 				        scrollTop:  scrolled
			 				},200);
			 			}
			 		});
			 	},'json');
			}
			return false;
		});
		function loadMenuCategories(){
		 	// $.post(baseUrl+'cashier/get_menu_categories',function(data){
		 	$.post(baseUrl+'cashier/get_menu_cats',function(data){
		 		showMenuCategories(data,1);
		 	},'json');
		}
		function showMenuCategories(data,ctr){
			$('.category-btns').remove();

			$.each(data,function(key,val){
				var cat_id = val['id'];
				if(ctr == 1){
					var hashTag = window.location.hash;
					// alert(hashTag);
					if(hashTag != '#retail'){
						loadsDiv('menus',cat_id,val,null);
					}
				}
	 			$('<button/>')
	 			.attr({'id':'menu-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-blue double btn btn-default'})
	 			.text(val.name)
	 			.appendTo('.menu-cat-container')
	 			.click(function(){
	 				$('#search-menu').val('');
	 				loadsDiv('menus',cat_id,val,null);
	 				return false;
	 			});
				ctr++;
			});
			if(ctr < 10){
				for (var i = 0; i <= (10-ctr); i++) {
					$('<button/>')
		 			.attr({'class':'btn-block category-btns counter-btn-red-gray double btn btn-default'})
		 			.text('')
		 			.appendTo('.menu-cat-container');
				};
			}
		}
		function loadItemCategories(){
		 	$.post(baseUrl+'cashier/get_item_categories',function(data){
		 		showItemCategories(data,1);
		 	},'json');
		}
		function showItemCategories(data,ctr){
			$('.category-btns').remove();
			$.each(data,function(cat_id,val){
				if(ctr == 1){
					loadsDiv('retail');
				}
	 			$('<button/>')
	 			.attr({'id':'item-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-blue double btn btn-default'})
	 			.text(val.name)
	 			.appendTo('.menu-cat-container')
	 			.click(function(){
	 				var formData = 'cat_id='+cat_id+'&cat_name='+val.name;
	 				loadsDiv('retail');
	 				loadRetailItemList(formData,$(this));
	 				return false;
	 			});
				ctr++;
			});
			// alert(ctr);
			if(ctr < 9){
				for (var i = 0; i <= (8-ctr); i++) {
					$('<button/>')
		 			.attr({'class':'btn-block category-btns counter-btn-red-gray double btn btn-default'})
		 			.text('')
		 			.appendTo('.menu-cat-container');
				};
			}
		}
		function scannedRetailItem(formData){
			// btn.goLoad();
			$.post(baseUrl+'cashier/get_item_scanned',formData,function(data){
				if(data.error  != ""){
					rMsg(data.error,'error');
				}
				else{
					addRetailTransCart(data.item_id,data.opt);
				}
				// $('.retail-title').text(data.title).show();
				// $('.retail-loads-div').html(data.code);
				// $.each(data.items,function(item_id,opt){
				// 	$('#retail-item-'+item_id).click(function(){
				// 		addRetailTransCart(item_id,opt);
				// 		return false;
				// 	});
				// });
				// $('#search-item').val('');
				// btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function loadRetailItemList(formData,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/get_item_lists',formData,function(data){
				$('.retail-title').text(data.title).show();
				$('.retail-loads-div').html(data.code);
				$.each(data.items,function(item_id,opt){
					$('#retail-item-'+item_id).click(function(){
						addRetailTransCart(item_id,opt);
						return false;
					});
				});
				$('#search-item').val('');
				btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function loadRetailCustomerList(formData,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/get_customers_lists',formData,function(data){
				$('.customers-loads-div').html(data.code);
				$.each(data.items,function(customer_id,opt){
					$('#customer-item-'+customer_id).click(function(){
						$.post(baseUrl+'cashier/update_trans_customer/'+customer_id,function(data){
							$('#trans-customer').text('CUSTOMER ID: '+customer_id).show();
							rMsg('Added Customer ID','success');
						});
						return false;
					});
				});
				$('#search-item').val('');
				btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function remeload(type_load){
			if(type_load == 'retail'){
				$('#retail-btn').removeClass('counter-btn-red');
				$('#retail-btn').addClass('counter-btn-green');
				loadsDiv('retail');
				$('#go-scan-code').removeClass('counter-btn-orange');
				$('#go-scan-code').addClass('counter-btn-green');
				loadItemCategories();
				$('#scan-code').focus();
			}
			else{
				$('#retail-btn').removeClass('counter-btn-green');
				$('#retail-btn').addClass('counter-btn-red');
			}
		}
		function loadsDiv(type,id,opt,trans_id,other){
			if(type == 'menus'){
				remeload('menu');
				$('.scrollers-menu').remove();
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				$('.menus-div .title').text(opt.name);
				$('.menus-div .items-lists').html('');

				$.post(baseUrl+'cashier/get_menus_sorted/'+id,function(data){
					var div = $('.menus-div .items-lists').append('<div class="row"></div>');
			 		$.each(data,function(key,opt){
			 			
			 			var menu_id = opt.id;
			 			var sCol = $('<div class="col-md-3"></div>');
			 			$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-block btn-default'})
			 			.text(opt.name)
			 			.appendTo(sCol)
			 			.click(function(){
			 				var btn = $(this);
			 				btn.goLoad();
			 				if(opt.free == 1){
				 				$.callManager({
				 					success : function(){
								 				addTransCart(menu_id,opt,btn);
				 							  },
				 					fail    : function(){
				 								btn.goLoad({load:false});
				 							  },
				 					cancel  : function(){
				 								btn.goLoad({load:false});
				 							  }		  	
				 				});
			 				}
			 				else{
			 					addTransCart(menu_id,opt,btn);
			 				}
			 				return false;
			 			});
			 			sCol.appendTo(div);
			 			
			 		});
			 		

			 		$('.menus-div .items-lists').after('<div class="scrollers-menu"><div class="row"><div class="col-md-6 text-left"><button id="menu-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="menu-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
			 		$("#menu-item-scroll-down").on("click" ,function(){
			 		 //    scrolled=scrolled+100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });

	 				    var inHeight = $(".items-lists")[0].scrollHeight;
	 				    var divHeight = $(".items-lists").height();
	 				    var trueHeight = inHeight - divHeight;
	 			        if((scrolled + 150) > trueHeight){
	 			        	scrolled = trueHeight;
	 			        }
	 			        else{
	 			    	    scrolled=scrolled+150;				    	
	 			        }
	 				    // scrolled=scrolled+100;
	 					$(".items-lists").animate({
	 					        scrollTop:  scrolled
	 					},200);
			 		});
			 		$("#menu-item-scroll-up").on("click" ,function(){
			 			// scrolled=scrolled-100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });
			 			if(scrolled > 0){
			 				scrolled=scrolled-150;
			 				$(".items-lists").animate({
			 				        scrollTop:  scrolled
			 				},200);
			 			}
			 		});
			 	},'json');
			}
			else if(type=='mods'){
				remeload('menu');
				$('.mods-div .title').text(opt.name+" Modifiers");
				$('.mods-div .mods-lists').html('');
				var trans_det = opt;

				var formData = 'menu_name='+trans_det.name;
				if(other == "addModDefault"){
					formData += '&add_defaults=1';
				}	
				$.post(baseUrl+'cashier/get_menu_modifiers_wth_dflt/'+id+'/'+trans_id,formData,function(data){
					var modGRP = data.group;
					var dfltGRP = data.dflts;
					if(!$.isEmptyObject(dfltGRP)){
						$.each(dfltGRP,function(trans_mod_id,mopt){
							makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,trans_det,"","default");
						});	
					}	
					if(!$.isEmptyObject(modGRP)){
						$('.loads-div').hide();
						$('.'+type+'-div').show();
						$.each(modGRP,function(mod_group_id,opt){
							var row = $('<div/>').attr({'class':'mod-group','id':'mod-group-'+mod_group_id}).appendTo('.mods-div .mods-lists');
							$('<h4/>').text(opt.name)
									  .addClass('text-center receipt')
									  .css({'margin-bottom':'5px'})
									  .appendTo('#mod-group-'+mod_group_id);
							var mandatory = opt.mandatory;
							var multiple = opt.multiple;

							var div = $('#mod-group-'+mod_group_id);
							var divRow = $('<div/>').attr({'class':'row'});
							// var div = $('#mod-group-'+mod_group_id).append('<div class="row"></div>');
							$.each(opt.details,function(mod_id,det){
								var sCol = $('<div class="col-md-4"></div>');
								$('<button/>')
								.attr({'id':'mod-'+mod_id,'ref':mod_id,'class':'counter-btn-silver btn btn-block btn-default'})
								// .css({'margin':'5px','width':'130px'})
								.text(det.name)
								.appendTo(sCol)
								.click(function(){
									addTransModCart(trans_id,mod_group_id,mod_id,det,id,$(this),trans_det,mandatory,multiple);
									return false;
								});
				 				sCol.appendTo(divRow);
				 			});
				 			div.append(divRow);
				 			$('<hr/>').appendTo('#mod-group-'+mod_group_id);
				 		});
						$('.mods-div .mods-lists').after('<div id="scrollers-mods"><div class="row"><div class="col-md-6 text-left"><button id="mods-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="mods-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
				 		$("#mods-item-scroll-down").on("click" ,function(){
				 		 //    scrolled=scrolled+100;
				 			// $(".items-lists").animate({
				 			//         scrollTop:  scrolled
				 			// });

		 				    var inHeight = $(".mods-lists")[0].scrollHeight;
		 				    var divHeight = $(".mods-lists").height();
		 				    var trueHeight = inHeight - divHeight;
		 			        if((scrolled + 150) > trueHeight){
		 			        	scrolled = trueHeight;
		 			        }
		 			        else{
		 			    	    scrolled=scrolled+150;				    	
		 			        }
		 				    // scrolled=scrolled+100;
		 					$(".mods-lists").animate({
		 					        scrollTop:  scrolled
		 					},200);
				 		});
				 		$("#mods-item-scroll-up").on("click" ,function(){
				 			// scrolled=scrolled-100;
				 			// $(".items-lists").animate({
				 			//         scrollTop:  scrolled
				 			// });
				 			if(scrolled > 0){
				 				scrolled=scrolled-150;
				 				$(".mods-lists").animate({
				 				        scrollTop:  scrolled
				 				},200);
				 			}
				 		});
					}
			 	},'json');
			}
			else if(type=='qty'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='remarks'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='charges'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
			else if(type=='sel-discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else{
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
		}
		var promo_ctr = 0;
		function addTransCart(menu_id,opt,btn){
			var cost = opt.cost;
			var goOn = false;
			if($('#buy2take1').exists()){				
				if($('#counter').hasClass('on-promo-choose')){
					var max_qty = parseFloat($('#counter').attr('promo-qty')); 
					if(promo_ctr < max_qty){
						cost = 0;
						promo_ctr++;
						if(promo_ctr == max_qty){
							goOn = true;
						}
					}
				}
			}			
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+cost+'&no_tax='+opt.no_tax+'&qty=1';
			var submit = $('#submit-btn');
			var settle = $('#settle-btn');
			var cash = $('#cash-btn');
			var credit = $('#credit-btn');
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id,"addModDefault");
				transTotal();
				if(goOn){
					$('body').goLoad2({loadTxt:'Loading...'});
					if($('#counter').hasClass('on-promo-submit')){
						submit.trigger('click');
						$('body').goLoad2({load:false});
						$('#promo-txt').hide();
						$('#counter').removeClass('on-promo-choose');
						$('#counter').removeClass('on-promo-submit');
						$('#counter').removeAttr('promo-qty');
						promo_ctr = 0;
					}
					else if($('#counter').hasClass('on-promo-settle')){
						settle.trigger('click');
					}
					else if($('#counter').hasClass('on-promo-cash')){
						cash.trigger('click');
					}
					else if($('#counter').hasClass('on-promo-credit')){
						credit.trigger('click');
					}
				}
				btn.goLoad({load:false});
			},'json');
			
		}
		function addRetailTransCart(item_id,opt){
			var formData = 'menu_id='+item_id+'&name='+opt.name+'&cost='+opt.cost+'&no_tax=0&qty=1&retail=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,item_id,data.items);
				// loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
				$('#go-scan-code').removeClass('counter-btn-orange');
				$('#go-scan-code').addClass('counter-btn-green');
				$('#scan-code').focus();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det,mandatory,multiple){
			var formData = 'trans_id='+trans_id+'&mod_group_id='+mod_group_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name
							+'&mandatory='+mandatory
							+'&multiple='+multiple
							+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			// console.log(formData);
			if(btn != null){
				btn.prop('disabled', true);				
			}
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg(data.error,'error');
				}
				else{
					// console.log(data.id);
					// console.log(trans_id);
					// console.log(mod_id);
					// console.log(opt);
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				if(btn != null){
					btn.prop('disabled', false);
				}
				transTotal();
			},'json');
			// alert(data);
			// });
		}
		function makeItemRow(id,menu_id,opt,loaded){
			$('.sel-row').removeClass('selected');
			var retail = "";
			if (opt.hasOwnProperty('retail')) {
				retail = 'retail-item';
			}

			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected '+retail+' '+loaded})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					if (!opt.hasOwnProperty('retail')) {
						loadsDiv('mods',menu_id,opt,id);
					}
					else{
						remeload('retail');
					}
					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			var namer = opt.name;
			if (opt.hasOwnProperty('retail')) {
				namer = '<i class="fa fa-shopping-cart"></i> '+opt.name;
			}
			if (opt.hasOwnProperty('kitchen_slip_printed')) {
				if(opt.kitchen_slip_printed == 1)
					namer = ' <i class="fa fa-print"></i> '+namer;
			}
			console.log(opt);
			if (opt.hasOwnProperty('free_user_id')) {	
				if(opt.free_user_id != "")
					namer = ' <i class="fa fa-asterisk"></i> '+namer;
			}
			$('<span/>').attr('class','name').html(namer).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det,loaded,dflt){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row '+loaded})
								   .click(function(){
										selector($(this));
										loadsDiv('mods',trans_det.menu_id,trans_det,trans_id);
										return false;
									});
			var mod_name = opt.name;					   
			if (opt.hasOwnProperty('kitchen_slip_printed')) {
				if(opt.kitchen_slip_printed == 1)
					mod_name = ' <i class="fa fa-print"></i> '+mod_name;
			}

			$('<span/>').attr('class','name').css('margin-left','28px').html(mod_name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').html(opt.cost).appendTo(subRow);

			if(dflt == "default"){
				$('#trans-row-'+trans_id).after(subRow);
			}
			else{
				$('.selected').after(subRow);
			}

			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var total = data.total;
				var discount = data.discount;
				var local_tax = data.local_tax;
				$("#total-txt").number(total,2);
				$("#discount-txt").number(discount,2);
				if($("#local-tax-txt").exists()){
					$("#local-tax-txt").number(local_tax,2);
				}
				
				if(data.zero_rated > 0){
					$("#zero-rated-btn").removeClass('counter-btn-red');
					$("#zero-rated-btn").addClass('counter-btn-green');
					$("#zero-rated-btn").addClass('zero-rated-active');
					$('.center-div .foot .foot-det').css({'background-color':'#FDD017'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#fff'});		
				}
			},'json');
			// 	alert(data);
			// });
		}
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					console.log(data);
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt,'loaded');
						if(opt.remarks != "" && opt.remarks != null){
							makeRemarksItemRow(trans_id,opt.remarks);
						}
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt,'loaded');
							});
						}
						if(ctr == len)
							$('.selected').trigger('click');
						ctr++;
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function loadTransChargeCart(){
			$.post(baseUrl+'cashier/get_trans_charges/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(charge_id,opt){
						makeChargeItemRow(charge_id,opt);
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function newTransaction(redirect,type){
			$.post(baseUrl+'cashier/new_trans/true/'+type,function(data){
				if(!redirect){
					$('#trans-datetime').text(data.datetime);
					var tp = data.type;
					$('#trans-header').text(tp.toUpperCase());

					$('.trans-lists').find('li').remove();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					transTotal();
					$('.addon-texts').text('').hide();
					if(type == 'retail')
						remeload('retail');
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
				}
				else{
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
					else{
						window.location = baseUrl+'cashier/counter/'+data.type;
					}
				}
			},'json');
		}
		function loadDefault(){
			var cat_id = $(".category-btns:first").attr('ref');
			var cat_name = $(".category-btns:first").text();
			var val = {'name':cat_name};
			loadsDiv('menus',cat_id,val,null);
		}
		function loadDiscounts(){
			$.post(baseUrl+'cashier/get_discounts',function(data){
				$('.select-discounts-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#item-disc-btn-'+id).click(function(){
						var idisc = $(this);
						if(opt.disc_code == 'SNDISC' || opt.disc_code == 'PWDISC'){
							$('#prcss-disc').attr('ref','equal');
							$('#disc-guests').removeAttr('readOnly');
						}
						else{
							$('#prcss-disc').attr('ref','all');
							$('#disc-guests').attr('readOnly','true');
						}
						$.callManager({
		 					success : function(){
								loadsDiv('discount',null,null,null);
								$('.discount-div .title').text(idisc.text());
								$('.discount-div #rate-txt').number(opt.disc_rate,2);
								$('#disc-disc-id').val(opt.disc_id);
								$('#disc-disc-rate').val(opt.disc_rate);
								$('#disc-disc-code').val(opt.disc_code);
								$('#disc-no-tax').val(opt.no_tax);
								$('#disc-fix').val(opt.fix);
								$('#disc-guests').val(opt.guest);
								$.post(baseUrl+'cashier/load_disc_persons/'+opt.disc_code,function(data){
									$('.disc-persons-list-div').html(data.code);
									$.each(data.items,function(code,opt){
										$("#disc-person-rem-"+code).click(function(){
											var lin = $(this).parent().parent();
											$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
												lin.remove();
												rMsg('Person Removed.','success');
												transTotal();
											});
											return false;
										});
									});
								},'json');
								// if (typeof opt.name != 'undefined') {
								// 	$('#disc-cust-name').val(opt.name);
								// 	$('#disc-cust-guest').val(opt.guest);
								// 	$('#disc-guests').val(opt.guest);
								// 	$('#disc-cust-code').val(opt.code);
								// 	$('#disc-cust-bday').val(opt.bday);
								// }
		 					}	
		 				});
						return false;
					});
				});
			},'json');
		}
		function loadCharges(){
			$.post(baseUrl+'cashier/get_charges',function(data){
				$('.charges-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#charges-btn-'+id).click(function(){
						addChargeCart(id,opt);
						return false;
					});
				});
			},'json');
		}
		function loadWaiters(){
			$.post(baseUrl+'cashier/get_waiters',function(data){
				$('.waiters-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#waiters-btn-'+id).click(function(){
						$.callFS({
							success : function(emp){
										if(id == emp['emp_id']){
											$.post(baseUrl+'cashier/update_trans/waiter_id/'+id,function(data){
												$('#trans-server-txt').text('FS: '+opt.uname).show();
												rMsg(opt.full_name+' added as Food Server','success');
											},'json');
										}
										else{
											rMsg('Wrong Pin.','error');
										}
									  }
						});
						return false;
					});
				});
			},'json');
		}
		function addChargeCart(id,row){
			var formData = 'name='+row.charge_name+'&code='+row.charge_name+'&amount='+row.charge_amount+'&absolute='+row.absolute;
			$.post(baseUrl+'wagon/add_to_wagon/trans_charge_cart/'+id,formData,function(data){
				if(data.error == null){
					makeChargeItemRow(data.id,data.items);
					// loadsDiv('mods',menu_id,data.items,data.id);
					transTotal();
				}
				else{
					rMsg(data.error,'error');
				}
			},'json');
			// });
		}
		function makeChargeItemRow(id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-charge-row-'+id,'charge-id':id,'ref':id,'class':'sel-row trans-charge-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					loadsDiv('charges');
					return false;
				});
			$('<span/>').attr('class','qty').html('<i class="fa fa-tag"></i>').css('margin-left','10px').appendTo('#trans-charge-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-charge-row-'+id);
			var tx = opt.amount;
			if(opt.absolute == 0){
				tx = opt.amount+'%';
			}
			$('<span/>').attr('class','cost').text(tx).css('margin-right','10px').appendTo('#trans-charge-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeRemarksItemRow(id,remarks){
			$('.sel-row').removeClass('selected');
			if($('#trans-remarks-row-'+id).exists()){
				$('#trans-remarks-row-'+id).remove();
			}
			$('<li/>').attr({'id':'trans-remarks-row-'+id,'ref':id,'class':'sel-row trans-remarks-row selected'})
				.insertAfter('.trans-lists li#trans-row-'+id)
				.click(function(){
					selector($(this));
					loadsDiv('remarks');
					$('#line-remarks').val(remarks);
					return false;
				});
			// $('<span/>').attr('class','qty').html('').css('margin-left','10px').appendTo('#trans-remarks-row-'+id);
			$('<span/>').attr('class','name').css('margin-left','26px').html('<i class="fa fa-text-width"></i> '+remarks).appendTo('#trans-remarks-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				alert(data);
			});
		}
		$('#disc-cust-name,#disc-cust-code,#disc-cust-bday,#disc-cust-guest,#line-remarks,#search-item')
        .keyboard({
            alwaysOpen: false,
            usePreview: false,
			autoAccept : true
        })
        .addNavigation({
            position   : [0,0],
            toggleMode : false,
            focusClass : 'hasFocus'
        });
	<?php elseif($use_js == 'loyaltyAddJs'): ?>
		  bootbox.on('shown',function(){
			  $('#loyalty-card').focus();
		  });
	<?php elseif($use_js == 'settleJs'): ?>
		// $(':button').click(function(){
		// 	$.beep();
		// });
		var hashTag = window.location.hash;
		if(hashTag == '#cash'){
			loadDivs('cash-payment',true);
		} else if(hashTag == '#credit'){
			loadDivs('credit-payment',true);
		} else if(hashTag == '#debit'){
			loadDivs('debit-payment',true);
		} else if(hashTag == '#gc'){
			loadDivs('gc-payment',true);
		}

		$('.order-view-list .body').perfectScrollbar({suppressScrollX: true});
		$('#cancel-btn,#finished-btn').click(function(){
			if($('#settle').attr('retrack') == '1'){
					window.location = baseUrl+'cashier/retrack/';
			}
			else{
				
				if($('#settle').attr('type') == 'dinein')
					window.location = baseUrl+'cashier/tables/';
				else if($('#settle').attr('type') == 'delivery')
					window.location = baseUrl+'cashier/delivery/';
				else if($('#settle').attr('type') == 'pickup')
					window.location = baseUrl+'cashier/pickup/';
				else
					window.location = baseUrl+'cashier/counter/'+$('#settle').attr('type')+'#'+$('#settle').attr('type');

			}

			return false;
		});
		$('#recall-btn').click(function(){
			type = $(this).attr('type');
			sale = $(this).attr("sale");
			// console.log(sale);	
			$.post(baseUrl+'cashier/check_payment/'+sale,function(data){
 				if(data.error == 'paid'){
 					rMsg('Error! Transaction already paid.','error');
 				}else{
 					window.location = baseUrl+'cashier/counter/'+type+'/'+sale+'#'+type;
 				}
 			},'json');
  			return false;
		});
		$('#transactions-btn').click(function(){
			loadDivs('transactions-payment',false);
			loadTransactions();
			return false;
		});
		$('#cash-btn').click(function(){
			loadDivs('cash-payment',true);
			return false;
		});

		$('#loyalty-btn').click(function(){
			$.callLoyaltyCard();
		})

		$('#credit-card-btn').click(function(){
			loadDivs('credit-payment',true);
			var amount = $('#settle').attr('balance');
			$('#credit-amt').val(amount,2);
			return false;
		});

		document.onkeyup = KeyCheck;

		function KeyCheck(e)
		{
		   var KeyID = (window.event) ? event.keyCode : e.keyCode;
		   if(KeyID == 13)
		   {
		     	str = $('#credit-card-num').val();
				if (str.indexOf("^") !== -1) {
				    // `str` contains "geordie" *exactly* (doesn't catch "Geordie" or similar)
				    formData = 'str='+encodeURIComponent(str)+'&cut=^';
				    // alert(formData);
				    $.post(baseUrl+'cashier/credit_no_fix',formData,function(data){
				    	// alert(data);
				    	$('#credit-card-num').val(data);
				    });

				    $('#credit-app-code').focus();
				}

				// if (str.indexOf(">") !== -1) {
				//     // `str` contains "geordie" *exactly* (doesn't catch "Geordie" or similar)
				//     formData = 'str='+encodeURIComponent(str)+'&cut=>';
				//     // alert(formData);
				//     $.post(baseUrl+'cashier/credit_no_fix',formData,function(data){
				//     	// alert(data);
				//     	$('#credit-card-num').val(data);
				//     });

				//     $('#credit-app-code').focus();
				// }
		   }

		}

		document.onkeydown = KeyCheck2;
		function KeyCheck2(e)
		{
			var KeyID = (window.event) ? event.keyCode : e.keyCode;
			if(KeyID == 220){
		   		return false;
		   	}
		   // alert(KeyID);
		}

		$('#debit-card-btn').click(function(){
			loadDivs('debit-payment',true);
			var amount = $('#settle').attr('balance');
			$('#debit-amt').val(amount,2);
			return false;
		});
		$('#smac-card-btn').click(function(){
			loadDivs('smac-payment',true);
			var amount = $('#settle').attr('balance');
			$('#smac-amt').val(amount,2);
			return false;
		});
		$('#eplus-card-btn').click(function(){
			loadDivs('eplus-payment',true);
			var amount = $('#settle').attr('balance');
			$('#eplus-amt').val(amount,2);
			return false;
		});
		$('#loyalty-card-btn').click(function(){
			loadDivs('loyalty-payment',true);
			$('#loyalty-card-num').focus();
			var amount = $('#settle').attr('balance');
			$('#loyalty-amt').val("");
			return false;
		});
		$('#loyalty-card-num').keypress(function(e){
			if(e.keyCode == 13){
				e.preventDefault();
			}
		});

		$('#foodpanda-btn').click(function(){
			loadDivs('foodpanda-payment',true);
			var amount = $('#settle').attr('balance');
			$('#foodpanda-amt').val(amount,2);
			return false;
		});

		$('#check-btn').click(function(){
			loadDivs('check-payment',true);
			var amount = $('#settle').attr('balance');
			$('#check-amt').val(amount,2);
			return false;
		});

		$('#online-deal-btn').click(function(){
			loadDivs('online-deal-payment',true);
			var amount = $('#settle').attr('balance');
			$('#online-deal-amt').val(amount,2);
			return false;
		});
		$('#cust-deposits-btn').click(function(){
			loadDivs('cust-deposits-payment',true);
			return false;
		});
		$('#cust-deposits-search').on('keyup',function(){
			show_search();
		});	
		$('#cust-deposits-submit-btn').click(function(){
			var amount = $('#cust-deposits-amt').val();
			var id = $('#settle').attr('sales');
			if(amount != ""){
				console.log($.isNumeric($('#cust-deposits-amt').val().replace(/,/g ,"")));
				if (! $.isNumeric($('#cust-deposits-amt').val().replace(/,/g ,"")) ) {
					rMsg("Invalid amount","error");
				}
				else{
					var new_amt = parseFloat($('#cust-deposits-amt').val().replace(/,/g ,""));
					if(new_amt > 0){
						addPayment(id,amount,'deposit');
					}
					else{
						rMsg('Invalid Amount','error');				
					}
				}
			}
			else{
				rMsg('Invalid Amount','error');
			}
			return false;
		});	
		$('#gift-cheque-btn').click(function(){
			loadDivs('gc-payment',true);
			$('#gc-code').blur();
			return false;
		});
		$('#coupon-btn').click(function(){
			loadDivs('coupon-payment',true);
			$('#coupon-code').blur();
			return false;
		});
		$('#manager-call-pin-login').keyup(function(e){
			if(e.keyCode == 13){
		 	   $('#manager-submit-btn').trigger("click");
			}
		});	
		$('#manager-submit-btn').click(function(){
			var pin = $('#manager-call-pin-login').val();
			var formData = 'pin='+pin;
			var amount = $('#settle').attr('balance');
			var id = $('#settle').attr('sales');
			var btn = $('#manager-submit-btn');
			btn.goLoad();
			$.post(baseUrl+'cashier/manager_go_login',formData,function(data){
				if (typeof data.error_msg === 'undefined'){
			      	var man = data.manager;
			      	var man_id = man.manager_id;
			      	var type = 'chit';
			      	var formData = 'manager_id='+man_id+'&manager_username='+data.manager_username;
			      	amount = amount.replace(/,/g ,"");
			      	$.post(baseUrl+'cashier/add_payment/'+id+'/'+amount+'/'+type,formData,function(data){
						if(data.error == ""){
							rMsg('Success! Payment Submitted.','success');
							$('#amount-tendered-txt').number(data.tendered,2);
							$('#change-due-txt').number(data.change,2);
							$('#balance-due-txt').number(data.balance,2);
							$('#settle').attr('balance',data.balance);
							$('#cash-input').val('');
							loadDivs('after-payment',false);

							if(data.balance != 0){
								$('#finished-btn').attr('disabled','disabled');
								$('#print-btn').attr('disabled','disabled');
							}else{
								$('#finished-btn').removeAttr('disabled');
								$('#print-btn').removeAttr('disabled');

								$('#transactions-btn').attr('disabled','disabled');
								$('#recall-btn').attr('disabled','disabled');
								$('#cancel-btn').attr('disabled','disabled');
							}
						} else {

						}
						btn.goLoad({load:false});
					},'json');
				}
				else{
					rMsg(data.error_msg,'error');
					btn.goLoad({load:false});
				}
			},'json');
			return false;
		});	
		$('#sign-chit-btn').click(function(){
			// var amount = $('#settle').attr('balance');
			// var id = $('#settle').attr('sales');
			// $.callManager({
			// 	success : function(data){
			// 		var man_id = data.manager_id;
			// 		var type = 'chit';
			// 		var formData = 'manager_id='+man_id+'&manager_username='+data.manager_username;
			// 		amount = amount.replace(/,/g ,"");
			// 		$('.btn-enter').goLoad();
			// 		$.post(baseUrl+'cashier/add_payment/'+id+'/'+amount+'/'+type,formData,function(data){
			// 			if(data.error == ""){
			// 				rMsg('Success! Payment Submitted.','success');
			// 				$('#amount-tendered-txt').number(data.tendered,2);
			// 				$('#change-due-txt').number(data.change,2);
			// 				$('#balance-due-txt').number(data.balance,2);
			// 				$('#settle').attr('balance',data.balance);
			// 				$('#cash-input').val('');
			// 				loadDivs('after-payment',false);

			// 				if(data.balance != 0){
			// 					$('#finished-btn').attr('disabled','disabled');
			// 					$('#print-btn').attr('disabled','disabled');
			// 				}else{
			// 					$('#finished-btn').removeAttr('disabled');
			// 					$('#print-btn').removeAttr('disabled');

			// 					$('#transactions-btn').attr('disabled','disabled');
			// 					$('#recall-btn').attr('disabled','disabled');
			// 					$('#cancel-btn').attr('disabled','disabled');
			// 				}
			// 			} else {

			// 			}
			// 			$('.btn-enter').goLoad({load:false});
			// 		},'json');
			// 	}
			// });
			loadDivs('sign-chit-payment',true);
			$('#manager-call-pin-login').focus();
			return false;
		});
		$('.amounts-btn').click(function(){
			var val = $(this).attr('val');
			var cash = $('#cash-input').val();
			if(cash == ""){
				$('#cash-input').val(val);
			}
			else{
				var tot = parseFloat(val) + parseFloat(cash);
				$('#cash-input').val(tot);
			}
			return false;
		});
		$('#cash-exact-btn,#cash-next-btn').click(function(){
			$('#cash-input').val($.number($('#settle').attr('balance'),2));
			return false;
		});
		$('#exact-amount-btn').click(function(){
			$('#cash-input').val($(this).attr('amount'));
			return false;
		});
		$('#cash-enter-btn').click(function(){
			var amount = $('#cash-input').val().replace(/,/g ,"");
			if(amount != ""){
				if (! $.isNumeric(amount) ) {
					rMsg("Please enter a valid amount","error");
					return false;
				}

				var id = $('#settle').attr('sales');
				addPayment(id,amount,'cash');
			}
			return false;
		});
		/* LOYALTY PAYMENT */
		$('#loyalty-card-num,#loyalty-amt').focus(function(){
			$('#tbl-loyalty-target').attr('target','#'+$(this).attr('id'));
		});
		$('#loyalty-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#loyalty-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#loyalty-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'loyalty');
			return false;
		});
		$('#loyalty-card-num,#loyalty-app-code,#loyalty-amt').focus(function()
		{
			$('#tbl-loyalty-target').attr('target','#'+$(this).attr('id'));
		});

		//PANDA payment
		$('#foodpanda-enter-btn').on('click',function(event){
			event.preventDefault();
			
			var amount = $('#foodpanda-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'foodpanda');
			return false;
		});

		/* CHECK PAYMENT */
 		$('#check-card-num,#check-amt').focus(function(){
 			$('#tbl-check-target').attr('target','#'+$(this).attr('id'));
 		});
 		$('#check-enter-btn').on('click',function(event){
 			event.preventDefault();
 			if (! $.isNumeric($('#check-amt').val().replace(/,/g ,"")) ) {
 				rMsg("Please enter a valid amount","error");
 				return false;
 			}
 			var amount = $('#check-amt').val();
 			var id = $('#settle').attr('sales');
 			addPayment(id,amount,'check');
 			return false;
 		});
 		/* End of CHECK PAYMENT */

		//


		/* DEBIT PAYMENT */
		$('#debit-card-num,#debit-amt').focus(function(){
			$('#tbl-debit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#debit-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#debit-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#debit-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'debit');
			return false;
		});
		/* End of DEBIT PAYMENT */
		$('#smac-card-num,#smac-amt').focus(function(){
			$('#tbl-smac-target').attr('target','#'+$(this).attr('id'));
		});
		$('#smac-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#smac-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#smac-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'smac');
			return false;
		});
		$('#smac-card-num,#smac-app-code,#smac-amt').focus(function()
		{
			$('#tbl-smac-target').attr('target','#'+$(this).attr('id'));
		});
		$('#eplus-card-num,#eplus-amt').focus(function(){
			$('#tbl-eplus-target').attr('target','#'+$(this).attr('id'));
		});
		$('#eplus-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#eplus-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#eplus-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'eplus');
			return false;
		});
		$('#eplus-card-num,#eplus-app-code,#eplus-amt').focus(function()
		{
			$('#tbl-eplus-target').attr('target','#'+$(this).attr('id'));
		});
		$('#online-deal-card-num,#online-deal-amt').focus(function(){
			$('#tbl-online-deal-target').attr('target','#'+$(this).attr('id'));
		});
		$('#online-deal-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#online-deal-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#online-deal-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'online');
			return false;
		});
		$('#online-deal-card-num,#online-deal-code,#online-deal-amt').focus(function()
		{
			$('#tbl-online-deal-target').attr('target','#'+$(this).attr('id'));
		});
		/* End of eplus PAYMENT */
		/* CREDIT PAYMENT */
		$('.credit-type-btn').on('click',function(event)
		{
			event.preventDefault();
			//alert($(this).val());
			$('#credit-type-hidden').val($(this).val());
			$('.credit-type-btn').attr('style','background-color:#2daebf !important;');
			$(this).attr('style','background-color:#007d9a !important;');
		});
		$('#credit-card-num,#credit-app-code,#credit-amt').focus(function()
		{
			$('#tbl-credit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#debit-card-num,#debit-app-code,#debit-amt').focus(function()
		{
			$('#tbl-debit-target').attr('target','#'+$(this).attr('id'));
		});
		$('#credit-enter-btn').on('click',function(event){
			event.preventDefault();
			if (! $.isNumeric($('#credit-amt').val().replace(/,/g ,"")) ) {
				rMsg("Please enter a valid amount","error");
				return false;
			}
			var amount = $('#credit-amt').val();
			var id = $('#settle').attr('sales');
			addPayment(id,amount,'credit');
			return false;
		});
		/* End of CREDIT PAYMENT */
		/* GIFT CHEQUE */


		$('#gc-enter-btn').on('click',function(event)
		{
			event.preventDefault();
			var m_mode = $(this).attr('mode');
			if (m_mode == 'search') {
				var code = $('#gc-code').val();
				$.post(baseUrl + 'cashier/search_gift_card/'+code,{},function(data)
				{
					if (typeof data.error != "undefined") {
						rMsg(data.error,"error");
					} else {
						$('#hid-gc-id').val(data.gc_id);
						$('#gc-amount').val(data.amount);
						$('#gc-code').val(data.card_no);
						$('#gc-enter-btn').html("Enter");
						$('#gc-enter-btn').attr('mode','finalize');
					}
				},'json');
			} else if (m_mode == 'finalize') {
				var amount = $('#gc-amount').val();
				var id = $('#settle').attr('sales');
				addPayment(id,amount,'gc');
			}
		});
		$('#gc-code').blur(function(event)
		{
			event.preventDefault();
			$('#gc-enter-btn').html('<i class="fa fa-search fa-lg"></i> Search');
			$('#gc-enter-btn').attr('mode','search');
		});
		/* End of GIFT CHEQUE */
		/* COUPON */
		$('#coupon-enter-btn').on('click',function(event)
		{
			event.preventDefault();
			var m_mode = $(this).attr('mode');
			if (m_mode == 'search') {
				var code = $('#coupon-code').val();
				$.post(baseUrl + 'cashier/search_coupon/'+code,{},function(data)
				{
					if (typeof data.error != "undefined") {
						rMsg(data.error,"error");
					} else {
						$('#hid-coupon-id').val(data.coupon_id);
						$('#coupon-amount').val(data.amount);
						$('#coupon-code').val(data.card_no);
						$('#coupon-enter-btn').html("Enter");
						$('#coupon-enter-btn').attr('mode','finalize');
					}
				},'json');
				// alert(data);
				// });
			} else if (m_mode == 'finalize') {
				var amount = $('#coupon-amount').val();
				var id = $('#settle').attr('sales');
				addPayment(id,amount,'coupon');
			}
		});
		$('#coupon-code').blur(function(event)
		{
			event.preventDefault();
			$('#coupon-enter-btn').html('<i class="fa fa-search fa-lg"></i> Search');
			$('#coupon-enter-btn').attr('mode','search');
		});
		/* End of COUPON */
		$('#cancel-cash-btn,#trsansactions-close-btn,#cancel-debit-btn,#cancel-smac-btn,#cancel-eplus-btn,#cancel-online-deal-btn,#cancel-cust-deposits-btn,#cancel-credit-btn,#cancel-gc-btn,#cancel-coupon-btn,#cancel-loyalty-btn,#cancel-foodpanda-btn').click(function(){
			loadDivs('select-payment',false);
			return false;
		});
		$('#add-payment-btn').click(function(){
			loadDivs('select-payment',true);
			return false;
		});
		$('#print-btn').click(function(event){
			var sales_id = $(this).attr('ref');
			$.post(baseUrl+'cashier/print_sales_receipt/'+sales_id,'',function(data)
			{
				rMsg(data.msg,'success');
			},'json');
			event.preventDefault();
		});
		function addPayment(id,amount,type){
			var formData = {};
			amount = amount.replace(/,/g ,"");
			$('.btn-enter').goLoad();
			if (type == 'credit') {
				formData = 'card_type='+$('#credit-type-hidden').val()+
						'&card_number='+$('#credit-card-num').val()+
						'&approval_code='+$('#credit-app-code').val();
			} else if (type == 'debit') {
				formData = 'card_number='+$('#debit-card-num').val()+'&approval_code='+$('#debit-app-code').val();
			} else if (type == 'smac') {
				formData = 'card_number='+$('#smac-card-num').val();	
			} else if (type == 'eplus') {
				formData = 'card_number='+$('#eplus-card-num').val();	
			} else if (type == 'loyalty') {
				formData = 'card_number='+$('#loyalty-card-num').val();	
			} else if (type == 'online') {
				formData = 'card_number='+$('#online-deal-card-num').val();	
			} else if (type == 'gc') {
				formData = 'gc_id='+$('#hid-gc-id').val()+'&gc_code='+$('#gc-code').val();
			} else if (type == 'coupon') {
				formData = 'coupon_id='+$('#hid-coupon-id').val()+'&coupon_code='+$('#coupon-code').val();
			} else if (type == 'deposit') {
				formData = 'customer_id='+$('#cust-deposits-cust-id').val();
			} else if (type == 'foodpanda') {
				formData = 'card_number='+$('#order-code-num').val();
			}else if (type == 'check') {
				formData = 'check_no='+$('#check-card-num').val()+'&bank='+$('#check-bank').val()+'&cdate='+$('#check-date').val();
  			}
			
			<?php if(MALL_ENABLED && MALL == 'megamall'): ?>
				$('body').goLoad2({loadTxt:'Creating SM File...'});
			<?php else: ?>
				$('body').goLoad2();
			<?php endif; ?>

			var retake = $('#settle').attr('retrack');
			if (typeof retake !== typeof undefined && retake !== false) {
				if(formData){
					formData += '&trans_retake='+retake;
				}else{
					formData += 'trans_retake='+retake;
				}
			}
			// alert(formData);
			// return false;
			$.post(baseUrl+'cashier/add_payment/'+id+'/'+amount+'/'+type,formData,function(data){
				$('body').goLoad2({load:false});				
				if(data.error == ""){
					rMsg('Success! Payment Submitted.','success');
					$('#amount-tendered-txt').number(data.tendered,2);
					$('#change-due-txt').number(data.change,2);
					$('#balance-due-txt').number(data.balance,2);
					$('#settle').attr('balance',data.balance);
					$('#cash-input').val('');
					loadDivs('after-payment',false);

					if(data.balance != 0){
						$('#finished-btn').attr('disabled','disabled');
						$('#print-btn').attr('disabled','disabled');
					}else{
						$('#finished-btn').removeAttr('disabled');
						$('#print-btn').removeAttr('disabled');

						$('#transactions-btn').attr('disabled','disabled');
						$('#recall-btn').attr('disabled','disabled');
						$('#cancel-btn').attr('disabled','disabled');
						$('#loyalty-btn').attr('disabled','disabled');
						$('#add-payment-btn').attr('disabled','disabled');
					}
				} else {
					rMsg(data.error,'error');
				}
				$('.btn-enter').goLoad({load:false});
			},'json').fail( function(xhr, textStatus, errorThrown) {
				$('.btn-enter').goLoad({load:false});
		        alert(xhr.responseText);
		    });
		}
		function deletePayment(pay_id,sales_id){
			$('#void-payment-btn-'+pay_id).click(function(){
				$.post(baseUrl+'cashier/delete_payment/'+pay_id+'/'+sales_id,function(data){
					console.log(data);
					if(data.error == 'paid'){
  						rMsg('Error! Transaction already paid.','error');
  					}else{
  						$('#balance-due-txt').number(data.balance,2);
  						$('#settle').attr('balance',data.balance);
  						$('#pay-row-div-'+pay_id).remove();
  					}
				},'json');
				return false;
			});
		}
		function loadTransactions(){
			var id = $('#settle').attr('sales');
			$.post(baseUrl+'cashier/settle_transactions/'+id,function(data){
				$('.transactions-payment-div .body').html(data.code);
				$('.transactions-payment-div .body').perfectScrollbar({suppressScrollX: true});
				$.each(data.ids,function(key,pay_id){
					deletePayment(pay_id,id);
				});
			},'json');
		}
		function loadDivs(type,check){
			var go = true
			if(check){
				go = checkBal();
			}
			if(go){
				if(type == 'cust-deposits-payment'){
					var txt = $('#cust-deposits-search').val();
					var ul = $('#cust-search-list');
					ul.find('li').remove();
					$('#cust-deposits-cust-name').val("");
					$('#cust-deposits-amt').val("");
					$('#cust-deposits-cust-id').val("");
				}
				$('.loads-div').hide();
				$('#debit-card-num').val("");
				$('#credit-card-num').val("");
				$('#loyalty-card-num').val("");
				$('#gc-code').val("");
				$('#coupon-code').val("");
				$('.'+type+'-div').show();
			}
		}
		function checkBal(){
			var bal = $('#settle').attr('balance');
			if(bal == ""){
				balance = 0;
			}
			else
				var balance = parseFloat(bal.replace(',','.').replace(' ',''));
			if(balance < 0){
				rMsg('Error! No more to pay.','error');
				return false;
			}
			else
				return true;
		}
		function show_search(){
			var txt = $('#cust-deposits-search').val();
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
										$.post(baseUrl+'cashier/get_custs_deposit_amount/'+cust_id,function(data){											
											var cust = data.result;
											if(!$.isEmptyObject(cust)){
												$('#cust-deposits-cust-name').val(cust.full_name);
												$('#cust-deposits-amt').val($.number(cust.amount,2) );
												$('#cust-deposits-cust-id').val(cust.cust_id);
											}
											else{												
												$('#cust-deposits-cust-name').val("");
												$('#cust-deposits-amt').val("");
												$('#cust-deposits-cust-id').val("");
											}
											selDeSel(li);
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
		$('#cash-input').addClass('disable-input-enter');
	<?php elseif($use_js == 'splitJs'): ?>
		var scrolled=0;
		var transScroll=0;
		$('.counter-split-right .actions-div').perfectScrollbar({suppressScrollX: true});
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		loadTransCart();
		$('#save-split-btn').click(function(){
			var btn = $(this);
			var sales_id = $('#counter').attr('sale');
			if(btn.attr('by') == 'select-items'){
				btn.goLoad();
				$.post(baseUrl+'cashier/save_split/'+sales_id,function(data){
					if(data.error == "")
						window.location = baseUrl+'cashier';
					else{
						rMsg(data.error,'error');
						btn.goLoad({load:false});
					}
				// alert(data);
				// });
				},'json');
			}
			else if(btn.attr('by') == 'even-split'){
				var num = parseFloat($('#even-spit-num').text());
				var sales_id = $('#counter').attr('sale');
				// btn.goLoad();
				$.post(baseUrl+'cashier/even_split/'+num+'/'+sales_id,function(data){
					if(data.error != ""){
					// 	window.location = baseUrl+'cashier';
					// else{
						rMsg(data.error,'error');
					}
					// btn.goLoad({load:false});
				},'json');
				// alert(data);
				// });
			}
			else{
				rMsg('Select Split Action','error');
			}
			return false;
		});
		$('.split-bys').click(function(){
			var load = $(this).attr('ref');
			$('#save-split-btn').attr('by',load);
			var btn = $(this);
			clearTransSplitCart(btn);
			loadDivs(load);
			return false;
		});
		$('#add-sel-block-btn').click(function(){
			newSelBlock();
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$('#even-up-btn,#even-down-btn').click(function(){
			var num = parseFloat($('#even-spit-num').text());
			var go = $(this).attr('num');
			if(go == 'up'){
				num += 1;
			}
			else{
				if(num > 2){
					num -= 1;
				}
			}
			$('#even-spit-num').text(num);
			return false;
		});
		$("#refresh-btn").click(function(){
			var btn = $(this);
			clearTransSplitCart(btn);
			return false;
		});
		function loadDivs(type){
			$('.loads-div').hide();
			$('.'+type+'-div').show();
		}
		function newSelBlock(){
			if($('#hid-num').exists()){
				var num = parseFloat($('#hid-num').val());
				num += 1;
				$('#hid-num').val(num);
			}
			else{
				$('<input>').attr({'type':'hidden','id':'hid-num'}).val(0).appendTo('.select-items-div');
				var num = 0;
			}
			$.post(baseUrl+'cashier/new_split_block/'+num,function(data){
				// var num = data.num;
				$('#add-btn-div').before(data.code);
				$('.counter-split-right .actions-div').perfectScrollbar('update');
				// alert(num);
				addDelFunc(num);
			},'json');
			// });
		}
		function addDelFunc(num){
			$('#sel-div-'+num+' .add-btn').click(function(){
				var sel = $('.selected');
				if(sel.exists()){
					if(sel.hasClass('trans-sub-row')){
						selectModMenu();
					}
					else if(sel.hasClass('trans-remarks-row')){
						selectModMenu();
					}
					var id = sel.attr('trans-id');
					var btn = $(this);
					addToTransSplitCart(num,id,btn);
				}
				return false;
			});
			$('#sel-div-'+num+' .del-btn').click(function(){
				var sel = $('#sel-div-'+num+' .splicted');
				if(sel.exists()){
					var id = sel.attr('trans-id');
					var btn = $(this);
					minusToTransSplitCart(num,id,btn);
				}
				return false;
			});
			$('#sel-div-'+num+' .remove-btn').click(function(){
				var sel = $('#sel-div-'+num);
				if(sel.exists()){
					var btn = $(this);
					btn.goLoad();
					$.post(baseUrl+'cashier/remove_split_block/'+num,function(data){
						// alert(data);
						sel.parent().remove();
						$.each(data.content,function(id,qty){
							$('#trans-row-'+id).show();
							$('.trans-sub-row[trans-id="'+id+'"]').show();
							$('.trans-remarks-row[trans-id="'+id+'"]').show();
							$('#trans-row-'+id).find('.qty').text(qty);
							selector($('#trans-row-'+id));
							$('#even-spit-num').text('2');
						});
						btn.goLoad({load:false});
					// });
					},'json');
				}
				return false;
			});
		}
		function addToTransSplitCart(num,id,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/add_split_block/'+num+'/'+id,function(data){
				// alert(data);
				var sel = $('#trans-row-'+id).clone();
				if($('#trans-split-row-'+num+'-'+id).exists()){
					$('#trans-split-row-'+num+'-'+id).find('.qty').text(data.split_qty);
					splictor($('#trans-split-row-'+num+'-'+id));
				}
				else{
					var ul = $('#sel-div-'+num+' ul');
					sel.attr('id','trans-split-row-'+num+'-'+id);
					sel.attr('ref',num);
					sel.removeClass('trans-row');
					sel.removeClass('sel-row');
					sel.removeClass('selected');
					sel.addClass('trans-split-row');
					sel.addClass('split-row');
					sel.find('.qty').text(data.split_qty);
					sel.appendTo(ul).click(function(){
						splictor($(this));
						return false;
					});
					splictor($('#trans-split-row-'+num+'-'+id));
					if($('.trans-sub-row[trans-id="'+id+'"]').exists()){
						$('.trans-sub-row[trans-id="'+id+'"]').each(function(){
							var li = $(this).clone();
							li.addClass('trans-split-row-'+num+'-'+id);
							li.removeClass('trans-sub-row');
							li.removeClass('sel-row');
							li.appendTo(ul);
						});
					}
					if($('.trans-remarks-row[trans-id="'+id+'"]').exists()){
						$('.trans-remarks-row[trans-id="'+id+'"]').each(function(){
							var li = $(this).clone();
							li.addClass('trans-split-row-'+num+'-'+id);
							li.removeClass('trans-remarks-row');
							li.removeClass('sel-row');
							li.appendTo(ul);
						});
					}
				}
				if(data.from_qty <= 0){
					$('#trans-row-'+id).hide();
					$('#trans-row-'+id).removeClass('selected');
					$('.trans-sub-row[trans-id="'+id+'"]').hide();
					$('.trans-sub-row[trans-id="'+id+'"]').removeClass('selected');
					$('.trans-remarks-row[trans-id="'+id+'"]').hide();
					$('.trans-remarks-row[trans-id="'+id+'"]').removeClass('selected');
				}
				else{
					$('#trans-row-'+id).find('.qty').text(data.from_qty);
				}
				btn.goLoad({load:false});
			},'json');
			// });
		}
		function minusToTransSplitCart(num,id,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/minus_split_block/'+num+'/'+id,function(data){
				if(data.from_qty > 0){
					$('#trans-row-'+id).show();
					$('.trans-sub-row[trans-id="'+id+'"]').show();
					$('.trans-remarks-row[trans-id="'+id+'"]').show();
					$('#trans-row-'+id).find('.qty').text(data.from_qty);
					selector($('#trans-row-'+id));
				}
				if(data.split_qty <= 0){
					var sel = $('#sel-div-'+num+' .splicted');
					$('.trans-split-row-'+num+'-'+id).remove();
					sel.remove();
				}
				else{
					$('#trans-split-row-'+num+'-'+id).find('.qty').text(data.split_qty);
				}
				btn.goLoad({load:false});
			},'json');
			// });
		}
		function clearTransSplitCart(btn){
			var sel = $('.sel-div');
			btn.goLoad();
			$.post(baseUrl+'cashier/clear_split',function(data){
				sel.parent().remove();
				$('#hid-num').remove();
				$.each(data.content,function(id,qty){
					$('#trans-row-'+id).show();
					$('.trans-sub-row[trans-id="'+id+'"]').show();
					$('.trans-remarks-row[trans-id="'+id+'"]').show();
					$('#trans-row-'+id).find('.qty').text(qty);
					selector($('#trans-row-'+id));
				});
				btn.goLoad({load:false});
			},'json');
		}
		function addTransCart(menu_id,opt){
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det){
			var formData = 'trans_id='+trans_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg('Modifier is Already Added!','error');
				}
				else{
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				btn.prop('disabled', false);
				transTotal();
			},'json');
		}
		function makeItemRow(id,menu_id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row'})
								   .click(function(){
										selector($(this));

										return false;
									});
			$('<span/>').attr('class','name').css('margin-left','26px').text(opt.name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').text(opt.cost).appendTo(subRow);
			$('.selected').after(subRow);
			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeRemarksItemRow(id,remarks){
			$('.sel-row').removeClass('selected');
			if($('#trans-remarks-row-'+id).exists()){
				$('#trans-remarks-row-'+id).remove();
			}
			$('<li/>').attr({'id':'trans-remarks-row-'+id,'ref':id,'trans-id':id,'class':'sel-row trans-remarks-row selected'})
				.insertAfter('.trans-lists li#trans-row-'+id)
				.click(function(){
					selector($(this));
					return false;
				});
			// $('<span/>').attr('class','qty').html('').css('margin-left','10px').appendTo('#trans-remarks-row-'+id);
			$('<span/>').attr('class','name').css('margin-left','26px').html('<i class="fa fa-text-width"></i> '+remarks).appendTo('#trans-remarks-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function splictor(li){
			$('.split-row').removeClass('splicted');
			li.addClass('splicted');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
			if(sel.hasClass('trans-remarks-row')){
				var trans_id = sel.attr("ref");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var total = data.total;
				var discount = data.discount;

				$("#total-txt").number(total,2);
				$("#discount-txt").number(discount,2);
			},'json');
		}
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt);
						if(opt.remarks != "" && opt.remarks != null){
							makeRemarksItemRow(trans_id,opt.remarks);
						}
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt);
							});
						}
						ctr++;
					});
				}
				transTotal();
			},'json');
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				alert(data)
			// },'json');
			});
		}
	<?php elseif($use_js == 'combineJs'): ?>
		var scrolled=0;
		var transScroll=0;
		$('.counter-split-right .actions-div').perfectScrollbar({suppressScrollX: true});
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		$('.orders-list-combine').perfectScrollbar({suppressScrollX: true});
		$('.orders-to-combine').perfectScrollbar({suppressScrollX: true});
		loadTransCart();
		loadMenuCategories();
		$('#combine-btn').click(function(){
			$(this).goLoad();
			$.post(baseUrl+'cashier/save_combine',function(data){
				window.location = baseUrl+'cashier';
			});
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$('#clear-btn').click(function(){
			$('.combine-row').remove();
			$.post(baseUrl+'wagon/clear_wagon/trans_combine_cart/',function(data){
				$('#refresh-btn').trigger('click');
			});
			return false;
		});
		$('#refresh-btn').click(function(){
			var terminal = $('.orders-list-combine').attr('terminal');
			var types = $('.orders-list-combine').attr('types');
			loadOrders(terminal,types);
			return false;
		});
		$('.my-all-btns').click(function(){
			var terminal = $(this).attr('ref');
			var types = $('.orders-list-combine').attr('types');
			$('.orders-list-combine').attr('terminal',terminal);
			loadOrders(terminal,types);
			return false;
		});
		function loadMenuCategories(){
				var data = {
					"All TYPES": {"id":"all"},
					"DINE IN": {"id":"dinein"},
					"DELIVERY": {"id":"delivery"},
					"COUNTER": {"id":"counter"},
					// "RETAIL": {"id":"retail"},
					"PICKUP": {"id":"pickup"},
					"TAKEOUT": {"id":"takeout"},
					"DRIVE-THRU": {"id":"drivethru"},
				}
				var ctr = 1;
				$.each(data,function(txt,opt){
		 			$('<button/>')
		 			.attr({'id':opt.id+'-btn','ref':opt.id,'class':'types-btns btn-block category-btns counter-btn-teal double btn btn-default'})
		 			.text(txt)
		 			.appendTo('.type-container')
		 			.click(function(){
		 				var terminal = $('.orders-list-combine').attr('terminal');
		 				loadOrders(terminal,opt.id);
						$('.orders-list-combine').attr('types',opt.id);
		 				loadOrders(terminal,opt.id);		 				return false;
		 			});
					if(ctr == 1){
						$('#'+opt.id+'-btn').trigger('click');
					}
					ctr++;
				});
		}
		function loadOrders(terminal,types){
			$('.orders-list-combine').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-lg fa-fw fa-spin aw"></i></div></center>');
			$.post(baseUrl+'cashier/orders/'+terminal+'/open/'+types+'/null/none/0/combineList',function(data){
				$('.orders-list-combine').html(data.code);
				if(data.ids != null){
					$.each(data.ids,function(id,val){
						addDelFunc(id,val);
					});
					$('.orders-list-combine').perfectScrollbar('update');
				}
			},'json');
			// alert(data);
			// });
		}
		function addDelFunc(id,val){
			$('#add-to-btn-'+id).click(function(){
				var btn = $(this);
				var formData = 'sales_id='+id+'&balance='+val.amount;
				var clone = $('#order-btnish-'+id).clone();
				var orig = $('#order-btnish-'+id).clone();
				btn.goLoad();
				$.post(baseUrl+'wagon/add_to_wagon/trans_combine_cart',formData,function(data){
					var com_id = data.id;
					var btn = $('<button/>')
								.html('<i class="fa fa-times fa-lg fa-fw"></i>')
								.attr({'id':'remove-combine-btn-'+id,'ref':id,'class':'btn-block counter-btn-red'})
								.click(function(){

									var rBtn = $(this);
									rBtn.goLoad();
									$.post(baseUrl+'wagon/delete_to_wagon/trans_combine_cart/'+com_id,function(data){
										$('.orders-list-combine .orders-list-div-btnish:first-child').before(orig);
										$('#combine-row-'+com_id).remove();
										addDelFunc(id,val);
										$('.orders-list-combine').perfectScrollbar('update');
										rBtn.goLoad({load:false});
									},'json');
									return false;
								});
					clone
					.attr('id','combine-row-'+com_id)
					.addClass('combine-row')
					.find('.add-btn-row').remove();
					clone.find('.order-btn-right-container').append(btn);
					clone.appendTo('.orders-to-combine');
					$('#order-btnish-'+id).remove();
					$('.orders-to-combine.orders-to-combine').perfectScrollbar('update');
					btn.goLoad({load:false});
				},'json');
				return false;
			});
		}
		function loadDivs(type){
			$('.loads-div').hide();
			$('.'+type+'-div').show();
		}
		function addTransCart(menu_id,opt){
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det){
			var formData = 'trans_id='+trans_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg('Modifier is Already Added!','error');
				}
				else{
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				btn.prop('disabled', false);
				transTotal();
			},'json');
		}
		function makeItemRow(id,menu_id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));

					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row'})
								   .click(function(){
										selector($(this));

										return false;
									});
			$('<span/>').attr('class','name').css('margin-left','26px').text(opt.name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').text(opt.cost).appendTo(subRow);
			$('.selected').after(subRow);
			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeRemarksItemRow(id,remarks){
			$('.sel-row').removeClass('selected');
			if($('#trans-remarks-row-'+id).exists()){
				$('#trans-remarks-row-'+id).remove();
			}
			$('<li/>').attr({'id':'trans-remarks-row-'+id,'ref':id,'class':'sel-row trans-remarks-row selected'})
				.insertAfter('.trans-lists li#trans-row-'+id)
				.click(function(){
					selector($(this));
					return false;
				});
			// $('<span/>').attr('class','qty').html('').css('margin-left','10px').appendTo('#trans-remarks-row-'+id);
			$('<span/>').attr('class','name').css('margin-left','26px').html('<i class="fa fa-text-width"></i> '+remarks).appendTo('#trans-remarks-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var total = data.total;
				var discount = data.discount;

				$("#total-txt").number(total,2);
				$("#discount-txt").number(discount,2);
			},'json');
		}
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt);
						if(opt.remarks != "" && opt.remarks != null){
							makeRemarksItemRow(trans_id,opt.remarks);
						}
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt);
							});
						}
						ctr++;
					});
				}
				transTotal();
			},'json');
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				alert(data)
			// },'json');
			});
		}
	<?php elseif($use_js == 'tablesJs'): ?>
	    $('#guest-input').keypress(function(event){
          if(event.keyCode == 13){
           $('#guest-enter-btn').trigger('click');
          }
        });
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$('#back-btn,#back-occ-btn').click(function(){
			loadDivs('select-table');
			return false;
		});
		$('#guest-enter-btn').click(function(){
			var type = $('#dine_type').val();
			var tbl = $('#select-table').attr('ref');
			var tbl_name = $('#select-table').attr('ref_name');
			var guest = $('#guest-input').val();
			var formData = 'type='+type+'&table='+tbl+'&table_name='+tbl_name+'&guest='+guest;
			if($.isNumeric(guest)){
				$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
					window.location = baseUrl+'cashier/counter/'+type;
				},'json');
			}
			else{
				rMsg('Invalid guest number.','error');
			}
			return false;
		});
		$('#start-new-btn').click(function(){
			loadDivs('no-guest');
			return false;
		});
		$.post(baseUrl+'cashier/get_branch_details',function(data){
			var img = data.layout;
			if(img != "" ){
				var img_real_width=0,
				    img_real_height=0;
				$("<img/>")
				.attr("src", img)
			    .attr("id", "image-layout")
			    .load(function(){
		           img_real_width = this.width;
		           img_real_height = this.height;
		           $(this).appendTo('#image-con');
		           $("<div/>")
				    .attr("class", "rtag")
				    .attr("id", "rtag-div")
				    .css("height", img_real_height)
				    .css("width", img_real_width)
				    .appendTo('#image-con');
					loadMarks();

				});
			}
		},'json');

		
		function updateTblStatus(){
			$.post(baseUrl+'cashier/get_tbl_status',function(data){
				$.each(data,function(tbl_id,val){
					var mark = $('#mark-'+tbl_id);
					mark.removeClass('marker-green');
					mark.removeClass('marker-orange');
					mark.removeClass('marker-red');
					mark.addClass('marker-'+val.stat);
					mark.unbind('click');
					if(val.stat == 'green'){
						mark.click(function(){
							$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
								if(data.error == ""){
									$('#select-table').attr('ref',tbl_id);
			    					$('#select-table').attr('ref_name',val.name);
			    					loadDivs('no-guest');
			    					$('#guest-input').focus();
								}	
							},'json');	
						});
					}
					else{
						mark.click(function(){
							$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
								if(data.error == ""){
									$("#occ-num").text(val.name);
									$('#select-table').attr('ref',tbl_id);
									$('#select-table').attr('ref_name',val.name);
									loadDivs('occupied');
									get_table_orders(tbl_id);
								}else{
									rMsg(data.error,'error');
								}	
							},'json');	
						});
					}
				});
			},'json');	
			setTimeout(function(){
		  		updateTblStatus();
			}, 3000);	
		}	
		// checkOccupied();
		function loadMarks(){
			$.post(baseUrl+'cashier/get_tables',function(data){
				$.each(data,function(tbl_id,val){
					$('<a/>')
	    			.attr('href','#')
	    			// .attr('class','marker-red')
	    			.attr('class','markers marker-'+val.stat)
	    			.attr('id','mark-'+tbl_id)
	    			.attr('ref',tbl_id)
	    			.css('top',val.top+'px')
	    			.css('left',val.left+'px')
	    			.html('<h5 style="text-align:center;padding-top:7px;color:#fff">'+val.name+'</h5>')
	    			.appendTo('#rtag-div')
	    			.click(function(e){
	    				$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
		    				if(data.error == ""){
			    				if(val.stat == 'red'){
			    					$("#occ-num").text(val.name);
			    					$('#select-table').attr('ref',tbl_id);
			    					$('#select-table').attr('ref_name',val.name);
			    					loadDivs('occupied');
			    					get_table_orders(tbl_id);

			    				}
			    				else{
			    					$('#select-table').attr('ref',tbl_id);
			    					$('#select-table').attr('ref_name',val.name);
			    					loadDivs('no-guest');
			    					$('#guest-input').focus();
			    				}
		    				}
		    				else{
		    					rMsg(data.error,'error');
		    				}
	    				},'json');
	    				return false;
    				});
				});
				// checkOccupied();
				updateTblStatus();
			},'json');
		}
		function get_table_orders(tbl_id){
			$('.occ-orders-div').html('<br><center><i class="fa fa-spinner fa-spin fa-2x"></i></center>');
			$.post(baseUrl+'cashier/get_table_orders/true/'+tbl_id,function(data){
				$('.occ-orders-div').html(data.code);
				if(data.ids != null){
					$.each(data.ids,function(id,val){
						$('#order-btn-'+id).click(function(){
							$.post(baseUrl+'cashier/check_tbl_activity/'+tbl_id,function(data){
								if(data.error == ""){
									window.location = baseUrl+'cashier/counter/dinein/'+id;
								}else{
									rMsg(data.error,'error');
								}	
							},'json');	
							return false;
						});
						$("#transfer-btn-"+id).click(function(){
							bootbox.dialog({
							  message: baseUrl+'cashier/transfer_tables',
							  // title: 'Somthing',
							  className: 'manager-call-pop',
							  buttons: {
							    submit: {
							      label: "Transfer",
							      className: "btn  pop-manage pop-manage-green",
							      callback: function() {
							        var sales_id = id;
							        var to_table = $('#to-table').val();
							       	$.post(baseUrl+'cashier/go_transfer_table/'+sales_id+'/'+to_table,function(data){
							       		if(data == ""){
							       			location.reload();
							       		}
							       	});
							        // return true;
							      }
							    },
							    cancel: {
							      label: "CANCEL",
							      className: "btn pop-manage pop-manage-red",
							      callback: function() {
							        // Example.show("uh oh, look out!");
							      }
							    }
							  }
							});
							return false;
						});
						$('#print-btn-'+id).click(function(){
							$.post(baseUrl+'cashier/print_sales_receipt/'+id,'',function(data){
								rMsg(data.msg,'success');
							},'json');
							return false;
						});	
						$('#print-os-btn-'+id).click(function(){
							$.post(baseUrl+'cashier/print_os/'+id+'/0/1','',function(data){
								rMsg('Order Slip Reprinted.','success');
							});
							return false;
						});	
					});
				}
			},'json');
		}
		function checkOccupied(){
			// alert('here');
			$.post(baseUrl+'cashier/check_occupied_tables',function(data){
				console.log(data);
				var occ = data.occ;
				var ucc = data.ucc;
				$.each(occ,function(key,tbl){
					var mark = $('#mark-'+tbl['id']);
					if(mark.hasClass('marker-green')){
						// alert(tbl['id']);
						mark.removeClass('marker-green');
						mark.addClass('marker-red');
						mark.unbind('click');
						mark.click(function(e){
							$("#occ-num").text(tbl['name']);
							$('#select-table').attr('ref',tbl['id']);
							$('#select-table').attr('ref_name',tbl['name']);
							loadDivs('occupied');
							get_table_orders(tbl['id']);
							return false;
						});
					}
				});
				$.each(ucc,function(key,tbl){
					var mark = $('#mark-'+tbl['id']);
					if(mark.hasClass('marker-red')){
						// alert(tbl['id']);
						mark.removeClass('marker-red');
						mark.addClass('marker-green');
						mark.unbind('click');
						mark.click(function(e){
							$('#select-table').attr('ref',tbl['id']);
							$('#select-table').attr('ref_name',tbl['name']);
							loadDivs('no-guest');
							return false;
						});
					}
				});
			},'json');	
				// alert(data);
			// });
			setTimeout(function(){
		  		checkOccupied();
			}, 1000);	
		}
		function checkUnOccupied(){
		}
		function loadDivs(type){
			$('.loads-div').hide();
			$('.'+type+'-div').show();
		}
	<?php elseif($use_js == 'tableTransferJs'): ?>
		$('.reason-btns').click(function(){
			var reason = $(this).attr('ref');
			$('.reason-btns').attr('style','background-color:#D12027 !important;');
			$(this).attr('style','background-color:#d45500 !important;');
			$('#to-table').val(reason);
			return false;
		});	
	<?php elseif($use_js == 'deliveryJs'): ?>
		$('.listings').perfectScrollbar({suppressScrollX: true});
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});

		$('.key-ins').on('keydown', function(e) {
		    if (e.keyCode === 9) {
		        e.preventDefault();
		        // do work

	        	ref = $(this).attr('ref');
				to_focus = Number(ref) + Number(1);

				$('.'+to_focus).focus();
		    }
		});


		// $('#search-customer,.key-ins')
		// 	.keyboard({
		// 		alwaysOpen: true,
		// 		usePreview: false,
		// 		autoAccept : true
		// 	})
		// 	.addNavigation({
		// 		position   : [0,0],
		// 		toggleMode : false,
		// 		focusClass : 'hasFocus'
		// 	});
		$('#search-customer').on('blur',function(){
			var txt = $(this).val();
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
										$.post(baseUrl+'cashier/get_customers/'+cust_id,function(cust){
											$.each(cust,function(id,col){
												$.each(col, function(field,val) {
													$('#'+field).val(val);
												});
											});
										},'json');
									});
						$('<h4/>').html(val.name).appendTo(li);
						$('<h5/>').html(val.phone).appendTo(li);
						li.appendTo(ul);
					});
					$('.listings').perfectScrollbar('update');
				}
				ul.goLoad({load:false});
			},'json');
		});
		$('#continue-btn').click(function(){
			$('#customer-form').rOkay({
				btn_load 	: 	$('#continue-btn'),
				bnt_load_remove : 	false,
				asJson 		: 	true,
				onComplete 	: 	function(data){
					var id = data.id;
					var type = $('#trans_type').val();
					var formData = 'type='+type+'&customer_id='+id;
					$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
						window.location = baseUrl+'cashier/counter/'+type;
					},'json');
				}
			});
			return false;
		});
		$('#clear-btn').click(function(){
			$('.cust-form').find("input[type=text],input[type=hidden]").val("");
			return false;
		});
	//###############################################################################################
	<?php elseif($use_js == 'retrackJs'): ?>
		$('#search-btn').click(function(){
			var btn = $(this);
			btn.goLoad();
			$.rProgressBar();
			var otbody = $('#orders-tbl tbody');
			var stbody = $('#shifts-tbl tbody');
			otbody.html('');
			stbody.html('');
			var formData = $('#search-form').serialize();
			$.post(baseUrl+'cashier/retrack_load',formData,function(data){
				$.rProgressBarEnd({
					onComplete : function(){
						otbody.html(data.sales_rows);
						stbody.html(data.shift_rows);
						$('.add-shift-order').each(function(){
							$(this).click(function(){
								var shift_id = $(this).attr('ref');
								var user_id = $(this).attr('user');
								var type = $('#type-shift-'+shift_id).val();
								if(type == 'dine-in'){
									bootbox.dialog({
									  message: baseUrl+'cashier/transfer_tables',
									  className: 'manager-call-pop',
									  buttons: {
									    submit: {
									      label: "CONTINUE",
									      className: "btn  pop-manage pop-manage-green",
									      callback: function() {
									        var to_table = $('#to-table').val();
									      	var formData = 'type=dinein&table='+to_table+'&table_name='+to_table+'&guest=1&re_shift_id='+shift_id+'&re_user_id='+user_id;
								      		$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
								      			window.location = baseUrl+'cashier/counter_retrack/dinein';
								      		},'json');
									      }
									    },
									    cancel: {
									      label: "CANCEL",
									      className: "btn pop-manage pop-manage-red",
									      callback: function() {									        
									        btn.goLoad({load:false});
									      }
									    }
									  }
									});
								}
								else{
							      	var formData = 'type='+type+'&guest=1&re_shift_id='+shift_id+'&re_user_id='+user_id;
							      	// alert(formData);
						      		$.post(baseUrl+'wagon/add_to_wagon/trans_type_cart',formData,function(data){
						      			window.location = baseUrl+'cashier/counter_retrack/'+type;
						      			// console.log(data);
						      		},'json');
								}


								return false;
							});
						});
						btn.goLoad({load:false});
					 }
				});
			},'json').fail( function(xhr, textStatus, errorThrown) {
				btn.goLoad({load:false});
		        alert(xhr.responseText);
		    });
			return false;
		});
    <?php elseif($use_js == 'editDatetimeJs'): ?>		
    	 $('#datetime').datetimepicker();
    <?php elseif($use_js == 'counterRetrackJs'): ?>		
		$(document).scannerDetection({
			avgTimeByChar: 40,
			onComplete: function(barcode, qty){ 
				// console.log(barcode);
				// console.log(qty);
				var formData = 'barcode='+barcode;
				// alert(formData);
				scannedRetailItem(formData);
			},
			onError: function(string){}
		});
		$('#counter').disableSelection();
		var scrolled=0;
		var transScroll=0;
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		loadMenuCategories();
		loadTransCart();
		loadTransChargeCart();
		var hashTag = window.location.hash;
		if(hashTag == '#retail'){
			remeload('retail');
		}
		$('#edit-datetime').click(function(){
			bootbox.dialog({
			  message: baseUrl+'cashier/edit_datetime',
			  className: 'manager-call-pop',
			  buttons: {
			    submit: {
			      label: "USE",
			      className: "btn  pop-manage pop-manage-green",
			      callback: function() {
			        var formData = 'datetime='+$('#datetime').val();
			        $.post(baseUrl+'cashier/update_datetime_trans',formData,function(data){
			        	$('#trans-datetime-txt').text(data);
			        });
			        return true;
			      }
			    },
			    cancel: {
			      label: "CANCEL",
			      className: "btn pop-manage pop-manage-red",
			      callback: function() {									        
			        return true;
			      }
			    }
			  }
			});  
			return false;
		});
		$('#free-btn').click(function(){
			var id  = $('.selected').attr("ref");
			if (typeof id !== typeof undefined && id !== false) {
				$.callManager({
					success : function(manager){
						var man_user = manager.manager_username;
						var man_id = manager.manager_id;
							
						
						var formData = 'free_user_id='+man_id;
						$('body').goLoad2();
						$.post(baseUrl+'cashier/update_free_menu/'+id,formData,function(data){
							$('#trans-row-'+id+' .cost').text(0);
							var text = $('#trans-row-'+id).find('.name').text();
							// alert(text);
							$('#trans-row-'+id+' .name').html('<i class="fa fa-asterisk"></i> '+text);
							transTotal();
							rMsg('Updated Menu as free','success');
							$('body').goLoad2({load:false});
						});
					}
				});
			}
			return false;
		});
		$('#submit-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			var printOS = $('#print-os-btn').attr('doprint');
			var doPrintOS = false;
			if (typeof printOS !== typeof undefined && printOS !== false) {
				doPrintOS = printOS;
			}
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-submit');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/null/false/0/null/null/'+print+'/null/'+doPrintOS,function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						if(data.act == 'add'){
							newTransaction(false,data.type);
							if(btn.attr('id') == 'submit-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
						}
						else{
							newTransaction(true,data.type);
						}
					}

					$("#zero-rated-btn").removeClass('counter-btn-green');
					$("#zero-rated-btn").removeClass('zero-rated-active');
					$("#zero-rated-btn").addClass('counter-btn-red');
					$('.center-div .foot .foot-det').css({'background-color':'#fff'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
					
					$('.counter-center .body').perfectScrollbar('update');
					$(".counter-center .body").scrollTop(0);
				},'json');
				// alert(data);
				// });
			}
			return false;
		});
		$('#send-trans-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			var printOS = $('#print-os-btn').attr('doprint');
			var doPrintOS = false;
			if (typeof printOS !== typeof undefined && printOS !== false) {
				doPrintOS = printOS;
			}
			if($("#trans-server-txt").text() == ""){
				rMsg('Select Food Server','error');
			}
			else{
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/null/false/0/null/null/'+print+'/null/'+doPrintOS,function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						if(data.act == 'add'){
							newTransaction(false,data.type);
							if(btn.attr('id') == 'send-trans-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
						}
						else{
							newTransaction(true,data.type);
						}
					}

					$("#zero-rated-btn").removeClass('counter-btn-green');
					$("#zero-rated-btn").removeClass('zero-rated-active');
					$("#zero-rated-btn").addClass('counter-btn-red');
					$('.center-div .foot .foot-det').css({'background-color':'#fff'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
					
					$('.counter-center .body').perfectScrollbar('update');
					$(".counter-center .body").scrollTop(0);
				},'json');
				// alert(data);
				// });
			}		
			return false;	
		});
		$('#print-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i> <br>Billing');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i> <br>Billing');
			}
		});
		$('#print-os-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i><br>ORDER SLIP');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i><br>ORDER SLIP');
			}
		});
		$('#hold-all-btn').click(function(){
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans',function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					if(data.act == 'add'){
						newTransaction(false,data.type);
						if(btn.attr('id') == 'submit-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
					}
					else{
						newTransaction(true,data.type);
					}
				}
			},'json');
			return false;
		});
		$('#settle-btn').click(function(){
			var btn = $(this);
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-settle');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
						if(data.error != null){
							rMsg(data.error,'error');
							btn.prop('disabled', false);
						}
						else{
							newTransaction(false);
							if(btn.attr('id') == 'settle-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
							window.location = baseUrl+'cashier/settle/'+data.id+'?trans_retake=1';
						}
					},'json');
				// alert(data);
				// });
			}
			return false;
		});
		$('#cash-btn').click(function(){
			//alert('aw');
			var btn = $(this);
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-cash');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						if(btn.attr('id') == 'cash-btn'){
							//rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/settle/'+data.id+'#cash?trans_retake=1';
					}
				},'json');
			}
			return false;
		});
		$('#credit-btn').click(function(){
			//alert('aw');
			var btn = $(this);
			var go = true;
			if($('#buy2take1').exists()){
				if(!$('#counter').hasClass('on-promo-choose')){
					$('#counter').addClass('on-promo-choose');
					$('#counter').addClass('on-promo-credit');
					$.post(baseUrl+'cashier/buy2take1_qty',function(data){
						$('#counter').attr('promo-qty',data.qty);
						$('#promo-qty').text(data.qty);
						$('#promo-txt').show();
					},'json');
					go = false;
				}
				else{
					$('#counter').removeClass('on-promo-choose');
					go = true;
				}				
			}
			if(go){
				var btn = $(this);
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans/true/settle',function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						if(btn.attr('id') == 'credit-btn'){
							//rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/settle/'+data.id+'#credit?trans_retake=1';
					}
				},'json');
			}
			return false;
		});
		$('#waiter-btn').click(function(){
			loadsDiv('waiter',null,null,null);
			loadWaiters();
			return false;
		});
		$('#remove-waiter-btn').click(function(){
			$.post(baseUrl+'cashier/update_trans/waiter_id/null/true',function(data){
				$('#trans-server-txt').text('').hide();
				rMsg('Food Server Removed.','success');
			},'json');
			return false;
		});
		$('#loyalty-btn').click(function(){
			// var sel = $('.selected');
			// if(sel.exists()){
			// 	if(sel.hasClass('loaded')){
			// 		$.callManager({
			// 			success : function(){
			// 				loadsDiv('qty',null,null,null);
			// 			}	
			// 		});		
			// 	}
			// 	else	
			loadsDiv('loyalty',null,null,null);
			// }
			return false;
		});
		///FOR RETAIL
			$('#retail-btn').click(function(){
				if($(this).hasClass('counter-btn-red')){
					$(this).removeClass('counter-btn-red');
					$(this).addClass('counter-btn-green');
					loadItemCategories();
					loadsDiv('retail');
					$('#scan-code').focus();
					$('#go-scan-code').removeClass('counter-btn-orange');
					$('#go-scan-code').addClass('counter-btn-green');
				}
				else{
					$(this).removeClass('counter-btn-green');
					$(this).addClass('counter-btn-red');
					loadMenuCategories();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					$('#go-scan-code').removeClass('counter-btn-green');
					$('#go-scan-code').addClass('counter-btn-orange');
					$('#scan-code').blur();
				}
				return false;
			});
			$('#go-scan-code').click(function(){
				if($(this).hasClass('counter-btn-orange')){
					$(this).removeClass('counter-btn-orange');
					$(this).addClass('counter-btn-green');
					$('#scan-code').focus();
				}
				else{
					$(this).removeClass('counter-btn-green');
					$(this).addClass('counter-btn-orange');
					$('#scan-code').blur();
				}
				return false;
			});
			$('#scan-code').on('keyup',function(e){
				if(e.keyCode == 13){
					var code = $(this).val();
					if(code != ""){
						$.post(baseUrl+'cashier/scan_code/'+code,function(data){
							if(data.error == ""){
								var opt = data.item;
								addRetailTransCart(opt.item_id,opt);
								// $.beep();
							}
							else{
								rMsg(data.error,'error');
								// $.beep({'status':'error'});
								
							}
						},'json');
					} 
					else{
						rMsg('Item not found.','error');
						// $.beep({'status':'error'});
					    
					}
					$('#scan-code').val('');
				}
			});
			$('#go-search-item').click(function(){
				var btn = $(this);
				var search = $('#search-item').val();
				if(search != ""){
					var formData = 'search='+search;
					loadRetailItemList(formData,btn);
				}
				else{
					rMsg('Nothing to search.','error');
				}
				return false;
			});
			$('#customer-btn').click(function(){
				var btn = $(this);
				loadsDiv('customers',null,null,null);
				return false;
			});
			$('#remove-customer').click(function(){
				$.post(baseUrl+'cashier/update_trans_customer/',function(data){
					$('#trans-customer').text('').hide();
					rMsg('REMOVED Customer ID','success');
				});
			});
			$('#go-search-customer').click(function(){
				var btn = $(this);
				var search = $('#search-customer').val();
				if(search != ""){
					var formData = 'search='+search;
					loadRetailCustomerList(formData,btn);
				}
				else{
					rMsg('Nothing to search.','error');
				}
				return false;
			});
		$('#qty-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				if(sel.hasClass('loaded')){
					$.callManager({
						success : function(){
							loadsDiv('qty',null,null,null);
						}	
					});		
				}
				else	
					loadsDiv('qty',null,null,null);
			}
			return false;
		});
		$('#qty-btn-cancel,#qty-btn-done').click(function(){
			if($('#retail-btn').hasClass('counter-btn-red')){
				$('.loads-div').hide();				
				$('.menus-div').show();
			}
			else{
				remeload('retail');
			}
			return false;
		});
		$(".edit-qty-btn").click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");
			var formData = 'value='+btn.attr('value')+'&operator='+btn.attr('operator');
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/update_trans_qty/'+id,formData,function(data){
				var qty = data.qty;
				$('#trans-row-'+id+' .qty').text(qty);
				btn.prop('disabled', false);
				transTotal();
			},'json');
			return false;
		});
		$("#zero-rated-btn").click(function(){
			var btn = $(this);
			$.callManager({
				success : function(manager){
							var man_user = manager.manager_username;
							var man_id = manager.manager_id;
							if(!btn.hasClass('zero-rated-active')){
								$.post(baseUrl+'cashier/update_trans/zero_rated/1',function(data){
									btn.removeClass('counter-btn-red');
									btn.addClass('counter-btn-green');
									btn.addClass('zero-rated-active');
									$('.center-div .foot .foot-det').css({'background-color':'#FDD017'});
									$('.center-div .foot .foot-det .receipt').css({'color':'#fff'});
									transTotal();
								});
							}
							else{
								$.post(baseUrl+'cashier/update_trans/zero_rated/0',function(data){
									btn.removeClass('counter-btn-green');
									btn.removeClass('zero-rated-active');
									btn.addClass('counter-btn-red');
									$('.center-div .foot .foot-det').css({'background-color':'#fff'});
									$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
									transTotal();
								});
							}
				}
			});		
			return false;
		});
		$('#add-discount-btn').click(function(){
			loadsDiv('sel-discount',null,null,null);
			loadDiscounts();
			return false;
		});
		$('#add-disc-person-btn').click(function(){
			$('#add-disc-person-btn').goLoad();
			var noError = $('#disc-form').rOkay({
		     				btn_load		: 	$(this),
		     				goSubmit		: 	false,
		     				bnt_load_remove	: 	true
						  });
			if(noError){
				var guests = $('#disc-guests').val();
				var ref = $(this).attr('ref');
				var formData = $('#disc-form').serialize();
				formData = formData+'&type='+ref+'&guests='+guests;

				$.post(baseUrl+'cashier/add_person_disc',formData,function(data){
					$('#add-disc-person-btn').goLoad({load:false});
					if(data.error==""){
						$('.disc-persons-list-div').html(data.code);
						$.each(data.items,function(code,opt){
							$("#disc-person-rem-"+code).click(function(){
								var lin = $(this).parent().parent();
								$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
									lin.remove();
									rMsg('Person Removed.','success');
									transTotal();
								});
								return false;
							});
						});
						transTotal();
					}
					else{
						rMsg(data.error,'error');
					}
				},'json');
			}
			return false;
		})
		$('.disc-btn-row').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$('.disc-btn-row').goLoad2();
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				$('.disc-btn-row').goLoad2({load:false});
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#edit-order-guest-no').click(function(){
			$.callEditGuests({
				success : function(guest){
					$('#ord-guest-no').text(guest);	
					$('#disc-guests').val(guest);	
					rMsg('Guest has been updated to'+guest,'success');
				}
			});
			return false;
		});
		$('#prcss-disc').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			console.log(ref);
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$('.disc-btn-row').goLoad2();
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				$('.disc-btn-row').goLoad2({load:false});
				$('#ord-guest-no').text(guests);
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#remove-disc-btn').click(function(){
			var disc_code = $('#disc-disc-code').val();
			$.post(baseUrl+'cashier/del_trans_disc/'+disc_code,function(data){
				rMsg('Discounts Removed','success');
				$('.disc-person').remove();
				$('#disc-form')[0].reset();
				$('#disc-guests').val('');
				transTotal();
			});
			return false;
		});
		$('#remove-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				if(sel.hasClass('loaded')){
					$.callManager({
						success : function(manager){
							var man_user = manager.manager_username;
							var man_id = manager.manager_id;
							$.callReasons({
								submit : function(reason){
									var id = sel.attr('ref');
									var cart = 'trans_cart';
									var type = 'menu';
									
									if(sel.hasClass('trans-sub-row')){
										cart = 'trans_mod_cart';
										type = 'mod';
									}
									else if(sel.hasClass('trans-charge-row')){
										cart = 'trans_charge_cart';
										type = 'charge';
									}
									var retail = false;
									if(sel.hasClass('retail-item')){
										retail = true;
										type = 'retail';
									}
									
									$.post(baseUrl+'cashier/record_delete_line/'+cart+'/'+id+'/'+type+'/'+reason+'/'+man_id+'/'+man_user,function(data){
										// alert(data);
										$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
											sel.prev().addClass('selected');
											sel.remove();
											if(cart == 'trans_cart' && retail === false){
												$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
													var cat_id = $(".category-btns:first").attr('ref');
													var cat_name = $(".category-btns:first").text();
													var val = {'name':cat_name};
													loadsDiv('menus',cat_id,val,null);
													$('.trans-sub-row[trans-id="'+id+'"]').remove();
												});
											}
											$('.counter-center .body').perfectScrollbar('update');
											transTotal();
										},'json');
									});

								}
							});
						}
					});
				}
				else{
					var id = sel.attr('ref');
					var cart = 'trans_cart';
					if(sel.hasClass('trans-sub-row'))
						cart = 'trans_mod_cart';
					else if(sel.hasClass('trans-charge-row'))
						cart = 'trans_charge_cart';
					var retail = false;
					if(sel.hasClass('retail-item'))
						retail = true;
					if(!sel.hasClass('trans-remarks-row')){
						$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
							sel.prev().addClass('selected');
							sel.remove();
							if(cart == 'trans_cart' && retail === false){
								$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
									var cat_id = $(".category-btns:first").attr('ref');
									var cat_name = $(".category-btns:first").text();
									var val = {'name':cat_name};
									loadsDiv('menus',cat_id,val,null);
									$('.trans-sub-row[trans-id="'+id+'"]').remove();
								});
							}
							$('.counter-center .body').perfectScrollbar('update');
							transTotal();
						},'json');
					}
					else{
						$.post(baseUrl+'cashier/remove_trans_remark/'+id,function(data){
							sel.prev().addClass('selected');
							$('#trans-remarks-row-'+id).remove();
							$('.counter-center .body').perfectScrollbar('update');
						// });
						},'json');
					}
				}
			}
			return false;
		});
		$('#charges-btn').click(function(){
			$('.charges-div .title').text('Select Charges');
			loadCharges();
			loadsDiv('charges',null,null,null);
			return false;
		});
		$('#remarks-btn').click(function(){
			var sel = $('.selected');
			$('#line-remarks').val('');
			if(sel.exists()){
				loadsDiv('remarks',null,null,null);
			}
			return false;
		});
		$('#add-remark-btn').click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");

			var noError = $('#remarks-form').rOkay({
				 				btn_load		: 	$(this),
				 				goSubmit		: 	false,
				 				bnt_load_remove	: 	true
							});
			if(noError){
				var formData = $('#remarks-form').serialize();		
				btn.goLoad();
				$.post(baseUrl+'cashier/add_trans_remark/'+id,formData,function(data){
					makeRemarksItemRow(id,data.remarks);
					
					btn.goLoad({load:false});
				},'json');	
			}
			return false;
		});
		$('#tax-exempt-btn').click(function(){
			$.callManager({
				success : function(){
							$.post(baseUrl+'cashier/trans_exempt_to_tax',function(data){
								alert(data);
								transTotal();
								checkWagon('trans_cart');
							// },'json');	
							});	
						  }
			});						  
			return false;
		});
		$('#manager-btn').click(function(){
			window.location = baseUrl+'manager';
			return false;
		});
		$('#logout-btn').click(function(){
			window.location = baseUrl+'site/go_logout';
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$("#menu-cat-scroll-down").on("click" ,function(){
		    var inHeight = $(".menu-cat-container")[0].scrollHeight;
		    var divHeight = $(".menu-cat-container").height();
		    var trueHeight = inHeight - divHeight;
	        if((scrolled + 150) > trueHeight){
	        	scrolled = trueHeight;
	        }
	        else{
	    	    scrolled=scrolled+150;				    	
	        }
		    // scrolled=scrolled+100;
			$(".menu-cat-container").animate({
			        scrollTop:  scrolled
			},200);
		});
		$("#menu-cat-scroll-up").on("click" ,function(){
			if(scrolled > 0){
				scrolled=scrolled-150;
				$(".menu-cat-container").animate({
				        scrollTop:  scrolled
				},200);
			}
		});
		$(".menu-cat-container").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		$(".items-lists").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		$('#search-menu').on('keyup',function(){
			var search = $(this).val();
			if(search != ""){
				remeload('menu');
				$('.scrollers-menu').remove();
				$('.loads-div').hide();
				$('.menus-div').show();
				$('.menus-div .title').text('Search: '+search);
				$('.menus-div .items-lists').html('');
				var formData = 'search='+search;
				$.post(baseUrl+'cashier/get_menus_search_sorted',formData,function(data){
					var div = $('.menus-div .items-lists').append('<div class="row"></div>');
			 		$.each(data,function(key,opt){
			 			
			 			var menu_id = opt.id;
			 			var sCol = $('<div class="col-md-3"></div>');
			 			$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-block btn-default'})
			 			.text(opt.name)
			 			.appendTo(sCol)
			 			.click(function(){
			 				if(opt.free == 1){
				 				$.callManager({
				 					success : function(){
								 				addTransCart(menu_id,opt);
				 							  }	
				 				});
			 				}
			 				else{
			 					addTransCart(menu_id,opt);
			 				}
			 				return false;
			 			});
			 			sCol.appendTo(div);
			 		});
			 		$('.menus-div .items-lists').after('<div id="scrollers-menu"><div class="row"><div class="col-md-6 text-left"><button id="menu-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="menu-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
			 		$("#menu-item-scroll-down").on("click" ,function(){
			 		 //    scrolled=scrolled+100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });

	 				    var inHeight = $(".items-lists")[0].scrollHeight;
	 				    var divHeight = $(".items-lists").height();
	 				    var trueHeight = inHeight - divHeight;
	 			        if((scrolled + 150) > trueHeight){
	 			        	scrolled = trueHeight;
	 			        }
	 			        else{
	 			    	    scrolled=scrolled+150;				    	
	 			        }
	 				    // scrolled=scrolled+100;
	 					$(".items-lists").animate({
	 					        scrollTop:  scrolled
	 					},200);
			 		});
			 		$("#menu-item-scroll-up").on("click" ,function(){
			 			// scrolled=scrolled-100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });
			 			if(scrolled > 0){
			 				scrolled=scrolled-150;
			 				$(".items-lists").animate({
			 				        scrollTop:  scrolled
			 				},200);
			 			}
			 		});
			 	},'json');
			}
			return false;
		});
		function loadMenuCategories(){
		 	// $.post(baseUrl+'cashier/get_menu_categories',function(data){
		 	$.post(baseUrl+'cashier/get_menu_cats',function(data){
		 		showMenuCategories(data,1);
		 	},'json');
		}
		function showMenuCategories(data,ctr){
			$('.category-btns').remove();

			$.each(data,function(key,val){
				var cat_id = val['id'];
				if(ctr == 1){
					var hashTag = window.location.hash;
					// alert(hashTag);
					if(hashTag != '#retail'){
						loadsDiv('menus',cat_id,val,null);
					}
				}
	 			$('<button/>')
	 			.attr({'id':'menu-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-blue double btn btn-default'})
	 			.text(val.name)
	 			.appendTo('.menu-cat-container')
	 			.click(function(){
	 				$('#search-menu').val('');
	 				loadsDiv('menus',cat_id,val,null);
	 				return false;
	 			});
				ctr++;
			});
			if(ctr < 10){
				for (var i = 0; i <= (10-ctr); i++) {
					$('<button/>')
		 			.attr({'class':'btn-block category-btns counter-btn-red-gray double btn btn-default'})
		 			.text('')
		 			.appendTo('.menu-cat-container');
				};
			}
		}
		function loadItemCategories(){
		 	$.post(baseUrl+'cashier/get_item_categories',function(data){
		 		showItemCategories(data,1);
		 	},'json');
		}
		function showItemCategories(data,ctr){
			$('.category-btns').remove();
			$.each(data,function(cat_id,val){
				if(ctr == 1){
					loadsDiv('retail');
				}
	 			$('<button/>')
	 			.attr({'id':'item-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-blue double btn btn-default'})
	 			.text(val.name)
	 			.appendTo('.menu-cat-container')
	 			.click(function(){
	 				var formData = 'cat_id='+cat_id+'&cat_name='+val.name;
	 				loadsDiv('retail');
	 				loadRetailItemList(formData,$(this));
	 				return false;
	 			});
				ctr++;
			});
			// alert(ctr);
			if(ctr < 9){
				for (var i = 0; i <= (8-ctr); i++) {
					$('<button/>')
		 			.attr({'class':'btn-block category-btns counter-btn-red-gray double btn btn-default'})
		 			.text('')
		 			.appendTo('.menu-cat-container');
				};
			}
		}
		function scannedRetailItem(formData){
			// btn.goLoad();
			$.post(baseUrl+'cashier/get_item_scanned',formData,function(data){
				if(data.error  != ""){
					rMsg(data.error,'error');
				}
				else{
					addRetailTransCart(data.item_id,data.opt);
				}
				// $('.retail-title').text(data.title).show();
				// $('.retail-loads-div').html(data.code);
				// $.each(data.items,function(item_id,opt){
				// 	$('#retail-item-'+item_id).click(function(){
				// 		addRetailTransCart(item_id,opt);
				// 		return false;
				// 	});
				// });
				// $('#search-item').val('');
				// btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function loadRetailItemList(formData,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/get_item_lists',formData,function(data){
				$('.retail-title').text(data.title).show();
				$('.retail-loads-div').html(data.code);
				$.each(data.items,function(item_id,opt){
					$('#retail-item-'+item_id).click(function(){
						addRetailTransCart(item_id,opt);
						return false;
					});
				});
				$('#search-item').val('');
				btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function loadRetailCustomerList(formData,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/get_customers_lists',formData,function(data){
				$('.customers-loads-div').html(data.code);
				$.each(data.items,function(customer_id,opt){
					$('#customer-item-'+customer_id).click(function(){
						$.post(baseUrl+'cashier/update_trans_customer/'+customer_id,function(data){
							$('#trans-customer').text('CUSTOMER ID: '+customer_id).show();
							rMsg('Added Customer ID','success');
						});
						return false;
					});
				});
				$('#search-item').val('');
				btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function remeload(type_load){
			if(type_load == 'retail'){
				$('#retail-btn').removeClass('counter-btn-red');
				$('#retail-btn').addClass('counter-btn-green');
				loadsDiv('retail');
				$('#go-scan-code').removeClass('counter-btn-orange');
				$('#go-scan-code').addClass('counter-btn-green');
				loadItemCategories();
				$('#scan-code').focus();
			}
			else{
				$('#retail-btn').removeClass('counter-btn-green');
				$('#retail-btn').addClass('counter-btn-red');
			}
		}
		function loadsDiv(type,id,opt,trans_id,other){
			if(type == 'menus'){
				remeload('menu');
				$('.scrollers-menu').remove();
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				$('.menus-div .title').text(opt.name);
				$('.menus-div .items-lists').html('');

				$.post(baseUrl+'cashier/get_menus_sorted/'+id,function(data){
					var div = $('.menus-div .items-lists').append('<div class="row"></div>');
			 		$.each(data,function(key,opt){
			 			
			 			var menu_id = opt.id;
			 			var sCol = $('<div class="col-md-3"></div>');
			 			$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-block btn-default'})
			 			.text(opt.name)
			 			.appendTo(sCol)
			 			.click(function(){
			 				var btn = $(this);
			 				btn.goLoad();
			 				if(opt.free == 1){
				 				$.callManager({
				 					success : function(){
								 				addTransCart(menu_id,opt,btn);
				 							  },
				 					fail    : function(){
				 								btn.goLoad({load:false});
				 							  },
				 					cancel  : function(){
				 								btn.goLoad({load:false});
				 							  }		  	
				 				});
			 				}
			 				else{
			 					addTransCart(menu_id,opt,btn);
			 				}
			 				return false;
			 			});
			 			sCol.appendTo(div);
			 			
			 		});
			 		

			 		$('.menus-div .items-lists').after('<div class="scrollers-menu"><div class="row"><div class="col-md-6 text-left"><button id="menu-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="menu-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
			 		$("#menu-item-scroll-down").on("click" ,function(){
			 		 //    scrolled=scrolled+100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });

	 				    var inHeight = $(".items-lists")[0].scrollHeight;
	 				    var divHeight = $(".items-lists").height();
	 				    var trueHeight = inHeight - divHeight;
	 			        if((scrolled + 150) > trueHeight){
	 			        	scrolled = trueHeight;
	 			        }
	 			        else{
	 			    	    scrolled=scrolled+150;				    	
	 			        }
	 				    // scrolled=scrolled+100;
	 					$(".items-lists").animate({
	 					        scrollTop:  scrolled
	 					},200);
			 		});
			 		$("#menu-item-scroll-up").on("click" ,function(){
			 			// scrolled=scrolled-100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });
			 			if(scrolled > 0){
			 				scrolled=scrolled-150;
			 				$(".items-lists").animate({
			 				        scrollTop:  scrolled
			 				},200);
			 			}
			 		});
			 	},'json');
			}
			else if(type=='mods'){
				remeload('menu');
				$('.mods-div .title').text(opt.name+" Modifiers");
				$('.mods-div .mods-lists').html('');
				var trans_det = opt;

				var formData = 'menu_name='+trans_det.name;
				if(other == "addModDefault"){
					formData += '&add_defaults=1';
				}	
				$.post(baseUrl+'cashier/get_menu_modifiers_wth_dflt/'+id+'/'+trans_id,formData,function(data){
					var modGRP = data.group;
					var dfltGRP = data.dflts;
					if(!$.isEmptyObject(dfltGRP)){
						$.each(dfltGRP,function(trans_mod_id,mopt){
							makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,trans_det,"","default");
						});	
					}	
					if(!$.isEmptyObject(modGRP)){
						$('.loads-div').hide();
						$('.'+type+'-div').show();
						$.each(modGRP,function(mod_group_id,opt){
							var row = $('<div/>').attr({'class':'mod-group','id':'mod-group-'+mod_group_id}).appendTo('.mods-div .mods-lists');
							$('<h4/>').text(opt.name)
									  .addClass('text-center receipt')
									  .css({'margin-bottom':'5px'})
									  .appendTo('#mod-group-'+mod_group_id);
							var mandatory = opt.mandatory;
							var multiple = opt.multiple;

							var div = $('#mod-group-'+mod_group_id);
							var divRow = $('<div/>').attr({'class':'row'});
							// var div = $('#mod-group-'+mod_group_id).append('<div class="row"></div>');
							$.each(opt.details,function(mod_id,det){
								var sCol = $('<div class="col-md-4"></div>');
								$('<button/>')
								.attr({'id':'mod-'+mod_id,'ref':mod_id,'class':'counter-btn-silver btn btn-block btn-default'})
								// .css({'margin':'5px','width':'130px'})
								.text(det.name)
								.appendTo(sCol)
								.click(function(){
									addTransModCart(trans_id,mod_group_id,mod_id,det,id,$(this),trans_det,mandatory,multiple);
									return false;
								});
				 				sCol.appendTo(divRow);
				 			});
				 			div.append(divRow);
				 			$('<hr/>').appendTo('#mod-group-'+mod_group_id);
				 		});
						$('.mods-div .mods-lists').after('<div id="scrollers-mods"><div class="row"><div class="col-md-6 text-left"><button id="mods-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="mods-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
				 		$("#mods-item-scroll-down").on("click" ,function(){
				 		 //    scrolled=scrolled+100;
				 			// $(".items-lists").animate({
				 			//         scrollTop:  scrolled
				 			// });

		 				    var inHeight = $(".mods-lists")[0].scrollHeight;
		 				    var divHeight = $(".mods-lists").height();
		 				    var trueHeight = inHeight - divHeight;
		 			        if((scrolled + 150) > trueHeight){
		 			        	scrolled = trueHeight;
		 			        }
		 			        else{
		 			    	    scrolled=scrolled+150;				    	
		 			        }
		 				    // scrolled=scrolled+100;
		 					$(".mods-lists").animate({
		 					        scrollTop:  scrolled
		 					},200);
				 		});
				 		$("#mods-item-scroll-up").on("click" ,function(){
				 			// scrolled=scrolled-100;
				 			// $(".items-lists").animate({
				 			//         scrollTop:  scrolled
				 			// });
				 			if(scrolled > 0){
				 				scrolled=scrolled-150;
				 				$(".mods-lists").animate({
				 				        scrollTop:  scrolled
				 				},200);
				 			}
				 		});
					}
			 	},'json');
			}
			else if(type=='qty'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='remarks'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='charges'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
			else if(type=='sel-discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else{
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
		}
		var promo_ctr = 0;
		function addTransCart(menu_id,opt,btn){
			var cost = opt.cost;
			var goOn = false;
			if($('#buy2take1').exists()){				
				if($('#counter').hasClass('on-promo-choose')){
					var max_qty = parseFloat($('#counter').attr('promo-qty')); 
					if(promo_ctr < max_qty){
						cost = 0;
						promo_ctr++;
						if(promo_ctr == max_qty){
							goOn = true;
						}
					}
				}
			}			
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+cost+'&no_tax='+opt.no_tax+'&qty=1';
			var submit = $('#submit-btn');
			var settle = $('#settle-btn');
			var cash = $('#cash-btn');
			var credit = $('#credit-btn');
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id,"addModDefault");
				transTotal();
				if(goOn){
					$('body').goLoad2({loadTxt:'Loading...'});
					if($('#counter').hasClass('on-promo-submit')){
						submit.trigger('click');
						$('body').goLoad2({load:false});
						$('#promo-txt').hide();
						$('#counter').removeClass('on-promo-choose');
						$('#counter').removeClass('on-promo-submit');
						$('#counter').removeAttr('promo-qty');
						promo_ctr = 0;
					}
					else if($('#counter').hasClass('on-promo-settle')){
						settle.trigger('click');
					}
					else if($('#counter').hasClass('on-promo-cash')){
						cash.trigger('click');
					}
					else if($('#counter').hasClass('on-promo-credit')){
						credit.trigger('click');
					}
				}
				btn.goLoad({load:false});
			},'json');
			
		}
		function addRetailTransCart(item_id,opt){
			var formData = 'menu_id='+item_id+'&name='+opt.name+'&cost='+opt.cost+'&no_tax=0&qty=1&retail=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,item_id,data.items);
				// loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
				$('#go-scan-code').removeClass('counter-btn-orange');
				$('#go-scan-code').addClass('counter-btn-green');
				$('#scan-code').focus();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det,mandatory,multiple){
			var formData = 'trans_id='+trans_id+'&mod_group_id='+mod_group_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name
							+'&mandatory='+mandatory
							+'&multiple='+multiple
							+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			// console.log(formData);
			if(btn != null){
				btn.prop('disabled', true);				
			}
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg(data.error,'error');
				}
				else{
					// console.log(data.id);
					// console.log(trans_id);
					// console.log(mod_id);
					// console.log(opt);
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				if(btn != null){
					btn.prop('disabled', false);
				}
				transTotal();
			},'json');
			// alert(data);
			// });
		}
		function makeItemRow(id,menu_id,opt,loaded){
			$('.sel-row').removeClass('selected');
			var retail = "";
			if (opt.hasOwnProperty('retail')) {
				retail = 'retail-item';
			}

			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected '+retail+' '+loaded})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					if (!opt.hasOwnProperty('retail')) {
						loadsDiv('mods',menu_id,opt,id);
					}
					else{
						remeload('retail');
					}
					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			var namer = opt.name;
			if (opt.hasOwnProperty('retail')) {
				namer = '<i class="fa fa-shopping-cart"></i> '+opt.name;
			}
			if (opt.hasOwnProperty('kitchen_slip_printed')) {
				if(opt.kitchen_slip_printed == 1)
					namer = ' <i class="fa fa-print"></i> '+namer;
			}
			console.log(opt);
			if (opt.hasOwnProperty('free_user_id')) {	
				if(opt.free_user_id != "")
					namer = ' <i class="fa fa-asterisk"></i> '+namer;
			}
			$('<span/>').attr('class','name').html(namer).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det,loaded,dflt){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row '+loaded})
								   .click(function(){
										selector($(this));
										loadsDiv('mods',trans_det.menu_id,trans_det,trans_id);
										return false;
									});
			var mod_name = opt.name;					   
			if (opt.hasOwnProperty('kitchen_slip_printed')) {
				if(opt.kitchen_slip_printed == 1)
					mod_name = ' <i class="fa fa-print"></i> '+mod_name;
			}

			$('<span/>').attr('class','name').css('margin-left','28px').html(mod_name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').html(opt.cost).appendTo(subRow);

			if(dflt == "default"){
				$('#trans-row-'+trans_id).after(subRow);
			}
			else{
				$('.selected').after(subRow);
			}

			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var total = data.total;
				var discount = data.discount;
				var local_tax = data.local_tax;
				$("#total-txt").number(total,2);
				$("#discount-txt").number(discount,2);
				if($("#local-tax-txt").exists()){
					$("#local-tax-txt").number(local_tax,2);
				}
				
				if(data.zero_rated > 0){
					$("#zero-rated-btn").removeClass('counter-btn-red');
					$("#zero-rated-btn").addClass('counter-btn-green');
					$("#zero-rated-btn").addClass('zero-rated-active');
					$('.center-div .foot .foot-det').css({'background-color':'#FDD017'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#fff'});		
				}
			},'json');
			// 	alert(data);
			// });
		}
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					console.log(data);
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt,'loaded');
						if(opt.remarks != "" && opt.remarks != null){
							makeRemarksItemRow(trans_id,opt.remarks);
						}
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt,'loaded');
							});
						}
						if(ctr == len)
							$('.selected').trigger('click');
						ctr++;
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function loadTransChargeCart(){
			$.post(baseUrl+'cashier/get_trans_charges/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(charge_id,opt){
						makeChargeItemRow(charge_id,opt);
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function newTransaction(redirect,type){
			$.post(baseUrl+'cashier/new_trans/true/'+type,function(data){
				if(!redirect){
					$('#trans-datetime').text(data.datetime);
					var tp = data.type;
					$('#trans-header').text(tp.toUpperCase());

					$('.trans-lists').find('li').remove();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					transTotal();
					$('.addon-texts').text('').hide();
					if(type == 'retail')
						remeload('retail');
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
				}
				else{
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
					else{
						window.location = baseUrl+'cashier/counter/'+data.type;
					}
				}
			},'json');
		}
		function loadDefault(){
			var cat_id = $(".category-btns:first").attr('ref');
			var cat_name = $(".category-btns:first").text();
			var val = {'name':cat_name};
			loadsDiv('menus',cat_id,val,null);
		}
		function loadDiscounts(){
			$.post(baseUrl+'cashier/get_discounts',function(data){
				$('.select-discounts-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#item-disc-btn-'+id).click(function(){
						var idisc = $(this);
						if(opt.disc_code == 'SNDISC' || opt.disc_code == 'PWDISC'){
							$('#prcss-disc').attr('ref','equal');
						}
						else{
							$('#prcss-disc').attr('ref','all');
						}
						$.callManager({
		 					success : function(){
								loadsDiv('discount',null,null,null);
								$('.discount-div .title').text(idisc.text());
								$('.discount-div #rate-txt').number(opt.disc_rate,2);
								$('#disc-disc-id').val(opt.disc_id);
								$('#disc-disc-rate').val(opt.disc_rate);
								$('#disc-disc-code').val(opt.disc_code);
								$('#disc-no-tax').val(opt.no_tax);
								$('#disc-fix').val(opt.fix);
								$('#disc-guests').val(opt.guest);
								$.post(baseUrl+'cashier/load_disc_persons/'+opt.disc_code,function(data){
									$('.disc-persons-list-div').html(data.code);
									$.each(data.items,function(code,opt){
										$("#disc-person-rem-"+code).click(function(){
											var lin = $(this).parent().parent();
											$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
												lin.remove();
												rMsg('Person Removed.','success');
												transTotal();
											});
											return false;
										});
									});
								},'json');
								// if (typeof opt.name != 'undefined') {
								// 	$('#disc-cust-name').val(opt.name);
								// 	$('#disc-cust-guest').val(opt.guest);
								// 	$('#disc-guests').val(opt.guest);
								// 	$('#disc-cust-code').val(opt.code);
								// 	$('#disc-cust-bday').val(opt.bday);
								// }
		 					}	
		 				});
						return false;
					});
				});
			},'json');
		}
		function loadCharges(){
			$.post(baseUrl+'cashier/get_charges',function(data){
				$('.charges-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#charges-btn-'+id).click(function(){
						addChargeCart(id,opt);
						return false;
					});
				});
			},'json');
		}
		function loadWaiters(){
			$.post(baseUrl+'cashier/get_waiters',function(data){
				$('.waiters-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#waiters-btn-'+id).click(function(){
						$.callFS({
							success : function(emp){
										if(id == emp['emp_id']){
											$.post(baseUrl+'cashier/update_trans/waiter_id/'+id,function(data){
												$('#trans-server-txt').text('FS: '+opt.uname).show();
												rMsg(opt.full_name+' added as Food Server','success');
											},'json');
										}
										else{
											rMsg('Wrong Pin.','error');
										}
									  }
						});
						return false;
					});
				});
			},'json');
		}
		function addChargeCart(id,row){
			var formData = 'name='+row.charge_name+'&code='+row.charge_name+'&amount='+row.charge_amount+'&absolute='+row.absolute;
			$.post(baseUrl+'wagon/add_to_wagon/trans_charge_cart/'+id,formData,function(data){
				if(data.error == null){
					makeChargeItemRow(data.id,data.items);
					// loadsDiv('mods',menu_id,data.items,data.id);
					transTotal();
				}
				else{
					rMsg(data.error,'error');
				}
			},'json');
			// });
		}
		function makeChargeItemRow(id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-charge-row-'+id,'charge-id':id,'ref':id,'class':'sel-row trans-charge-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					loadsDiv('charges');
					return false;
				});
			$('<span/>').attr('class','qty').html('<i class="fa fa-tag"></i>').css('margin-left','10px').appendTo('#trans-charge-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-charge-row-'+id);
			var tx = opt.amount;
			if(opt.absolute == 0){
				tx = opt.amount+'%';
			}
			$('<span/>').attr('class','cost').text(tx).css('margin-right','10px').appendTo('#trans-charge-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeRemarksItemRow(id,remarks){
			$('.sel-row').removeClass('selected');
			if($('#trans-remarks-row-'+id).exists()){
				$('#trans-remarks-row-'+id).remove();
			}
			$('<li/>').attr({'id':'trans-remarks-row-'+id,'ref':id,'class':'sel-row trans-remarks-row selected'})
				.insertAfter('.trans-lists li#trans-row-'+id)
				.click(function(){
					selector($(this));
					loadsDiv('remarks');
					$('#line-remarks').val(remarks);
					return false;
				});
			// $('<span/>').attr('class','qty').html('').css('margin-left','10px').appendTo('#trans-remarks-row-'+id);
			$('<span/>').attr('class','name').css('margin-left','26px').html('<i class="fa fa-text-width"></i> '+remarks).appendTo('#trans-remarks-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				alert(data);
			});
		}
		$('#disc-cust-name,#disc-cust-code,#disc-cust-bday,#disc-cust-guest,#line-remarks,#search-item')
        .keyboard({
            alwaysOpen: false,
            usePreview: false,
			autoAccept : true
        })
        .addNavigation({
            position   : [0,0],
            toggleMode : false,
            focusClass : 'hasFocus'
        });
	//###############################################################################################
	//###############################################################################################
	<?php elseif($use_js == 'counterJs2'): ?>
		// $(':button').click(function(){
		// 	$.beep();
		// 	// alert('here');
		// });
		var scrolled=0;
		var transScroll=0;
		$('.counter-center .body').perfectScrollbar({suppressScrollX: true});
		loadMenuCategories();
		loadTransCart();
		loadTransChargeCart();
		var hashTag = window.location.hash;
		if(hashTag == '#retail'){
			remeload('retail');
		}
		$('#submit-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			var printOS = $('#print-os-btn').attr('doprint');
			var doPrintOS = false;
			if (typeof printOS !== typeof undefined && printOS !== false) {
				doPrintOS = printOS;
			}
			
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans2/true/null/false/0/null/null/'+print+'/null/'+doPrintOS,function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					if(data.act == 'add'){
						newTransaction(false,data.type);
						if(btn.attr('id') == 'submit-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
					}
					else{
						newTransaction(true,data.type);
					}
				}

				$("#zero-rated-btn").removeClass('counter-btn-green');
				$("#zero-rated-btn").removeClass('zero-rated-active');
				$("#zero-rated-btn").addClass('counter-btn-red');
				$('.center-div .foot .foot-det').css({'background-color':'#fff'});
				$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
				
				$('.counter-center .body').perfectScrollbar('update');
				$(".counter-center .body").scrollTop(0);
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#send-trans-btn').click(function(){
			var btn = $(this);
			var print = $('#print-btn').attr('doprint');
			var printOS = $('#print-os-btn').attr('doprint');
			var doPrintOS = false;
			if (typeof printOS !== typeof undefined && printOS !== false) {
				doPrintOS = printOS;
			}
			if($("#trans-server-txt").text() == ""){
				rMsg('Select Food Server','error');
			}
			else{
				btn.prop('disabled', true);
				$.post(baseUrl+'cashier/submit_trans2/true/null/false/0/null/null/'+print+'/null/'+doPrintOS,function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						if(data.act == 'add'){
							newTransaction(false,data.type);
							if(btn.attr('id') == 'send-trans-btn'){
								rMsg('Success! Transaction Submitted.','success');
							}
							else{
								rMsg('Transaction Hold.','warning');
							}
							btn.prop('disabled', false);
						}
						else{
							newTransaction(true,data.type);
						}
					}

					$("#zero-rated-btn").removeClass('counter-btn-green');
					$("#zero-rated-btn").removeClass('zero-rated-active');
					$("#zero-rated-btn").addClass('counter-btn-red');
					$('.center-div .foot .foot-det').css({'background-color':'#fff'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
					
					$('.counter-center .body').perfectScrollbar('update');
					$(".counter-center .body").scrollTop(0);
				},'json');
				// alert(data);
				// });
			}		
			return false;	
		});
		$('#print-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i> <br>Billing');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i> <br>Billing');
			}
		});
		$('#print-os-btn').click(function(event){
			var current =  $(this).attr('doprint');
			if (current == 'true'){
				$(this).attr('doprint','false');
				$(this).html('<i class="fa fa-fw fa-ban fa-lg"></i><br>ORDER SLIP');
			} else {
				$(this).attr('doprint','true');
				$(this).html('<i class="fa fa-fw fa-print fa-lg"></i><br>ORDER SLIP');
			}
		});
		$('#hold-all-btn').click(function(){
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans2',function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					if(data.act == 'add'){
						newTransaction(false,data.type);
						if(btn.attr('id') == 'submit-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
					}
					else{
						newTransaction(true,data.type);
					}
				}
			},'json');
			return false;
		});
		$('#settle-btn').click(function(){
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans2/true/settle',function(data){
					if(data.error != null){
						rMsg(data.error,'error');
						btn.prop('disabled', false);
					}
					else{
						newTransaction(false);
						if(btn.attr('id') == 'settle-btn'){
							rMsg('Success! Transaction Submitted.','success');
						}
						else{
							rMsg('Transaction Hold.','warning');
						}
						btn.prop('disabled', false);
						window.location = baseUrl+'cashier/settle/'+data.id;
					}
				},'json');
			// alert(data);
			// });
			return false;
		});
		$('#cash-btn').click(function(){
			//alert('aw');
			var btn = $(this);
			btn.prop('disabled', true);
			var formData = 'must_ref='+$('#must_ref').val()+'&must_datetime='+$('#must_trans_date').val();
			$.post(baseUrl+'cashier/submit_trans2/true/settle',formData,function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					window.location.reload();
					// newTransaction(false);
					// if(btn.attr('id') == 'cash-btn'){
					// 	//rMsg('Success! Transaction Submitted.','success');
					// }
					// else{
					// 	rMsg('Transaction Hold.','warning');
					// }
					// btn.prop('disabled', false);
					// window.location = baseUrl+'cashier/settle/'+data.id+'#cash';
				}
			},'json');
			return false;
		});
		$('#credit-btn').click(function(){
			//alert('aw');
			var btn = $(this);
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/submit_trans2/true/settle',function(data){
				if(data.error != null){
					rMsg(data.error,'error');
					btn.prop('disabled', false);
				}
				else{
					newTransaction(false);
					if(btn.attr('id') == 'credit-btn'){
						//rMsg('Success! Transaction Submitted.','success');
					}
					else{
						rMsg('Transaction Hold.','warning');
					}
					btn.prop('disabled', false);
					window.location = baseUrl+'cashier/settle/'+data.id+'#credit';
				}
			},'json');
			return false;
		});
		$('#waiter-btn').click(function(){
			loadsDiv('waiter',null,null,null);
			loadWaiters();
			return false;
		});
		$('#remove-waiter-btn').click(function(){
			$.post(baseUrl+'cashier/update_trans/waiter_id/null/true',function(data){
				$('#trans-server-txt').text('').hide();
				rMsg('Food Server Removed.','success');
			},'json');
			return false;
		});
		///FOR RETAIL
			$('#retail-btn').click(function(){
				if($(this).hasClass('counter-btn-red')){
					$(this).removeClass('counter-btn-red');
					$(this).addClass('counter-btn-green');
					loadItemCategories();
					loadsDiv('retail');
					$('#scan-code').focus();
					$('#go-scan-code').removeClass('counter-btn-orange');
					$('#go-scan-code').addClass('counter-btn-green');
				}
				else{
					$(this).removeClass('counter-btn-green');
					$(this).addClass('counter-btn-red');
					loadMenuCategories();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					$('#go-scan-code').removeClass('counter-btn-green');
					$('#go-scan-code').addClass('counter-btn-orange');
					$('#scan-code').blur();
				}
				return false;
			});
			$('#go-scan-code').click(function(){
				if($(this).hasClass('counter-btn-orange')){
					$(this).removeClass('counter-btn-orange');
					$(this).addClass('counter-btn-green');
					$('#scan-code').focus();
				}
				else{
					$(this).removeClass('counter-btn-green');
					$(this).addClass('counter-btn-orange');
					$('#scan-code').blur();
				}
				return false;
			});
			$('#scan-code').on('keyup',function(e){
				if(e.keyCode == 13){
					var code = $(this).val();
					if(code != ""){
						$.post(baseUrl+'cashier/scan_code/'+code,function(data){
							if(data.error == ""){
								var opt = data.item;
								addRetailTransCart(opt.item_id,opt);
								// $.beep();
							}
							else{
								rMsg(data.error,'error');
								// $.beep({'status':'error'});
								
							}
						},'json');
					} 
					else{
						rMsg('Item not found.','error');
						// $.beep({'status':'error'});
					    
					}
					$('#scan-code').val('');
				}
			});
			$('#go-search-item').click(function(){
				var btn = $(this);
				var search = $('#search-item').val();
				if(search != ""){
					var formData = 'search='+search;
					loadRetailItemList(formData,btn);
				}
				else{
					rMsg('Nothing to search.','error');
				}
				return false;
			});
		$('#qty-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				if(sel.hasClass('loaded')){
					$.callManager({
						success : function(){
							loadsDiv('qty',null,null,null);
						}	
					});		
				}
				else	
					loadsDiv('qty',null,null,null);
			}
			return false;
		});
		$('#qty-btn-cancel,#qty-btn-done').click(function(){
			if($('#retail-btn').hasClass('counter-btn-red')){
				$('.loads-div').hide();				
				$('.menus-div').show();
			}
			else{
				remeload('retail');
			}
			return false;
		});
		$(".edit-qty-btn").click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");
			var formData = 'value='+btn.attr('value')+'&operator='+btn.attr('operator');
			btn.prop('disabled', true);
			$.post(baseUrl+'cashier/update_trans_qty/'+id,formData,function(data){
				var qty = data.qty;
				$('#trans-row-'+id+' .qty').text(qty);
				btn.prop('disabled', false);
				transTotal();
			},'json');
			return false;
		});
		$("#zero-rated-btn").click(function(){
			var btn = $(this);
			if(!btn.hasClass('zero-rated-active')){
				$.post(baseUrl+'cashier/update_trans/zero_rated/1',function(data){
					btn.removeClass('counter-btn-red');
					btn.addClass('counter-btn-green');
					btn.addClass('zero-rated-active');
					$('.center-div .foot .foot-det').css({'background-color':'#FDD017'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#fff'});
					transTotal();
				});
			}
			else{
				$.post(baseUrl+'cashier/update_trans/zero_rated/0',function(data){
					btn.removeClass('counter-btn-green');
					btn.removeClass('zero-rated-active');
					btn.addClass('counter-btn-red');
					$('.center-div .foot .foot-det').css({'background-color':'#fff'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#000'});
					transTotal();
				});
			}
			return false;
		});
		$('#add-discount-btn').click(function(){
			loadsDiv('sel-discount',null,null,null);
			loadDiscounts();
			return false;
		});
		$('#add-disc-person-btn').click(function(){
			$('#add-disc-person-btn').goLoad();
			var noError = $('#disc-form').rOkay({
		     				btn_load		: 	$(this),
		     				goSubmit		: 	false,
		     				bnt_load_remove	: 	true
						  });
			if(noError){
				var guests = $('#disc-guests').val();
				var ref = $(this).attr('ref');
				var formData = $('#disc-form').serialize();
				formData = formData+'&type='+ref+'&guests='+guests;

				$.post(baseUrl+'cashier/add_person_disc',formData,function(data){
					$('#add-disc-person-btn').goLoad({load:false});
					if(data.error==""){
						$('.disc-persons-list-div').html(data.code);
						$.each(data.items,function(code,opt){
							$("#disc-person-"+code).click(function(){
								var lin = $(this);
								$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
									lin.remove();
									rMsg('Person Removed.','success');
									transTotal();
								});
								return false;
							});
						});
						transTotal();
					}
					else{
						rMsg(data.error,'error');
					}
				},'json');
			}
			return false;
		})
		$('.disc-btn-row').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$('.disc-btn-row').goLoad2();
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				$('.disc-btn-row').goLoad2({load:false});
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#edit-order-guest-no').click(function(){
			$.callEditGuests({
				success : function(guest){
					$('#ord-guest-no').text(guest);	
					$('#disc-guests').val(guest);	
					rMsg('Guest has been updated to'+guest,'success');
				}
			});
			return false;
		});
		$('#prcss-disc').click(function(){
			var guests = $('#disc-guests').val();
			var ref = $(this).attr('ref');
			console.log(ref);
			var formData = $('#disc-form').serialize();
			formData = formData+'&type='+ref+'&guests='+guests;
			$('.disc-btn-row').goLoad2();
			$.post(baseUrl+'cashier/add_trans_disc',formData,function(data){
				$('.disc-btn-row').goLoad2({load:false});
				$('#ord-guest-no').text(guests);
				if(data.error != ""){
					rMsg(data.error,'error');
				}
				else{
					rMsg('Added Discount.','success');
					transTotal();
				}
			},'json');
			// alert(data);
			// });
			return false;
		});
		$('#remove-disc-btn').click(function(){
			var disc_code = $('#disc-disc-code').val();
			$.post(baseUrl+'cashier/del_trans_disc/'+disc_code,function(data){
				rMsg('Discounts Removed','success');
				$('.disc-person').remove();
				$('#disc-form')[0].reset();
				$('#disc-guests').val('');
				transTotal();
			});
			return false;
		});
		$('#remove-btn').click(function(){
			var sel = $('.selected');
			if(sel.exists()){
				if(sel.hasClass('loaded')){
					$.callManager({
						success : function(manager){
							var man_user = manager.manager_username;
							var man_id = manager.manager_id;
							$.callReasons({
								submit : function(reason){
									var id = sel.attr('ref');
									var cart = 'trans_cart';
									var type = 'menu';
									
									if(sel.hasClass('trans-sub-row')){
										cart = 'trans_mod_cart';
										type = 'mod';
									}
									else if(sel.hasClass('trans-charge-row')){
										cart = 'trans_charge_cart';
										type = 'charge';
									}
									var retail = false;
									if(sel.hasClass('retail-item')){
										retail = true;
										type = 'retail';
									}
									
									$.post(baseUrl+'cashier/record_delete_line/'+cart+'/'+id+'/'+type+'/'+reason+'/'+man_id+'/'+man_user,function(data){
										// alert(data);
										$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
											sel.prev().addClass('selected');
											sel.remove();
											if(cart == 'trans_cart' && retail === false){
												$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
													var cat_id = $(".category-btns:first").attr('ref');
													var cat_name = $(".category-btns:first").text();
													var val = {'name':cat_name};
													loadsDiv('menus',cat_id,val,null);
													$('.trans-sub-row[trans-id="'+id+'"]').remove();
												});
											}
											$('.counter-center .body').perfectScrollbar('update');
											transTotal();
										},'json');
									});

								}
							});
						}
					});
				}
				else{
					var id = sel.attr('ref');
					var cart = 'trans_cart';
					if(sel.hasClass('trans-sub-row'))
						cart = 'trans_mod_cart';
					else if(sel.hasClass('trans-charge-row'))
						cart = 'trans_charge_cart';
					var retail = false;
					if(sel.hasClass('retail-item'))
						retail = true;
					if(!sel.hasClass('trans-remarks-row')){
						$.post(baseUrl+'wagon/delete_to_wagon/'+cart+'/'+id,function(data){
							sel.prev().addClass('selected');
							sel.remove();
							if(cart == 'trans_cart' && retail === false){
								$.post(baseUrl+'cashier/delete_trans_menu_modifier/'+id,function(data){
									var cat_id = $(".category-btns:first").attr('ref');
									var cat_name = $(".category-btns:first").text();
									var val = {'name':cat_name};
									loadsDiv('menus',cat_id,val,null);
									$('.trans-sub-row[trans-id="'+id+'"]').remove();
								});
							}
							$('.counter-center .body').perfectScrollbar('update');
							transTotal();
						},'json');
					}
					else{
						$.post(baseUrl+'cashier/remove_trans_remark/'+id,function(data){
							sel.prev().addClass('selected');
							$('#trans-remarks-row-'+id).remove();
							$('.counter-center .body').perfectScrollbar('update');
						// });
						},'json');
					}
				}
			}
			return false;
		});
		$('#charges-btn').click(function(){
			$('.charges-div .title').text('Select Charges');
			loadCharges();
			loadsDiv('charges',null,null,null);
			return false;
		});
		$('#remarks-btn').click(function(){
			var sel = $('.selected');
			$('#line-remarks').val('');
			if(sel.exists()){
				loadsDiv('remarks',null,null,null);
			}
			return false;
		});
		$('#add-remark-btn').click(function(){
			var sel = $('.selected');
			var btn = $(this);
			var id = sel.attr("ref");

			var noError = $('#remarks-form').rOkay({
				 				btn_load		: 	$(this),
				 				goSubmit		: 	false,
				 				bnt_load_remove	: 	true
							});
			if(noError){
				var formData = $('#remarks-form').serialize();		
				btn.goLoad();
				$.post(baseUrl+'cashier/add_trans_remark/'+id,formData,function(data){
					makeRemarksItemRow(id,data.remarks);
					
					btn.goLoad({load:false});
				},'json');	
			}
			return false;
		});
		$('#tax-exempt-btn').click(function(){
			$.callManager({
				success : function(){
							$.post(baseUrl+'cashier/trans_exempt_to_tax',function(data){
								alert(data);
								transTotal();
								checkWagon('trans_cart');
							// },'json');	
							});	
						  }
			});						  
			return false;
		});
		$('#manager-btn').click(function(){
			window.location = baseUrl+'manager';
			return false;
		});
		$('#logout-btn').click(function(){
			window.location = baseUrl+'site/go_logout';
			return false;
		});
		$('#cancel-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		$("#menu-cat-scroll-down").on("click" ,function(){
		    var inHeight = $(".menu-cat-container")[0].scrollHeight;
		    var divHeight = $(".menu-cat-container").height();
		    var trueHeight = inHeight - divHeight;
	        if((scrolled + 150) > trueHeight){
	        	scrolled = trueHeight;
	        }
	        else{
	    	    scrolled=scrolled+150;				    	
	        }
		    // scrolled=scrolled+100;
			$(".menu-cat-container").animate({
			        scrollTop:  scrolled
			},200);
		});
		$("#menu-cat-scroll-up").on("click" ,function(){
			if(scrolled > 0){
				scrolled=scrolled-150;
				$(".menu-cat-container").animate({
				        scrollTop:  scrolled
				},200);
			}
		});
		$(".menu-cat-container").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		$(".items-lists").bind("mousewheel",function(ev, delta) {
		    var scrollTop = $(this).scrollTop();
		    $(this).scrollTop(scrollTop-Math.round(delta));
		});
		$('#search-menu').on('keyup',function(){
			var search = $(this).val();
			if(search != ""){
				remeload('menu');
				$('.scrollers-menu').remove();
				$('.loads-div').hide();
				$('.menus-div').show();
				$('.menus-div .title').text('Search: '+search);
				$('.menus-div .items-lists').html('');
				var formData = 'search='+search;
				$.post(baseUrl+'cashier/get_menus_search_sorted',formData,function(data){
					var div = $('.menus-div .items-lists').append('<div class="row"></div>');
			 		$.each(data,function(key,opt){
			 			
			 			var menu_id = opt.id;
			 			var sCol = $('<div class="col-md-3"></div>');
			 			$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-block btn-default'})
			 			.text(opt.name)
			 			.appendTo(sCol)
			 			.click(function(){
			 				if(opt.free == 1){
				 				$.callManager({
				 					success : function(){
								 				addTransCart(menu_id,opt);
				 							  }	
				 				});
			 				}
			 				else{
			 					addTransCart(menu_id,opt);
			 				}
			 				return false;
			 			});
			 			sCol.appendTo(div);
			 		});
			 		$('.menus-div .items-lists').after('<div id="scrollers-menu"><div class="row"><div class="col-md-6 text-left"><button id="menu-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="menu-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
			 		$("#menu-item-scroll-down").on("click" ,function(){
			 		 //    scrolled=scrolled+100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });

	 				    var inHeight = $(".items-lists")[0].scrollHeight;
	 				    var divHeight = $(".items-lists").height();
	 				    var trueHeight = inHeight - divHeight;
	 			        if((scrolled + 150) > trueHeight){
	 			        	scrolled = trueHeight;
	 			        }
	 			        else{
	 			    	    scrolled=scrolled+150;				    	
	 			        }
	 				    // scrolled=scrolled+100;
	 					$(".items-lists").animate({
	 					        scrollTop:  scrolled
	 					},200);
			 		});
			 		$("#menu-item-scroll-up").on("click" ,function(){
			 			// scrolled=scrolled-100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });
			 			if(scrolled > 0){
			 				scrolled=scrolled-150;
			 				$(".items-lists").animate({
			 				        scrollTop:  scrolled
			 				},200);
			 			}
			 		});
			 	},'json');
			}
			return false;
		});
		function loadMenuCategories(){
		 	// $.post(baseUrl+'cashier/get_menu_categories',function(data){
		 	$.post(baseUrl+'cashier/get_menu_cats',function(data){
		 		showMenuCategories(data,1);
		 	},'json');
		}
		function showMenuCategories(data,ctr){
			$('.category-btns').remove();

			$.each(data,function(key,val){
				var cat_id = val['id'];
				if(ctr == 1){
					var hashTag = window.location.hash;
					// alert(hashTag);
					if(hashTag != '#retail'){
						loadsDiv('menus',cat_id,val,null);
					}
				}
	 			$('<button/>')
	 			.attr({'id':'menu-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-blue double btn btn-default'})
	 			.text(val.name)
	 			.appendTo('.menu-cat-container')
	 			.click(function(){
	 				$('#search-menu').val('');
	 				loadsDiv('menus',cat_id,val,null);
	 				return false;
	 			});
				ctr++;
			});
			if(ctr < 9){
				for (var i = 0; i <= (8-ctr); i++) {
					$('<button/>')
		 			.attr({'class':'btn-block category-btns counter-btn-red-gray double btn btn-default'})
		 			.text('')
		 			.appendTo('.menu-cat-container');
				};
			}
		}
		function loadItemCategories(){
		 	$.post(baseUrl+'cashier/get_item_categories',function(data){
		 		showItemCategories(data,1);
		 	},'json');
		}
		function showItemCategories(data,ctr){
			$('.category-btns').remove();
			$.each(data,function(cat_id,val){
				if(ctr == 1){
					loadsDiv('retail');
				}
	 			$('<button/>')
	 			.attr({'id':'item-cat-'+cat_id,'ref':cat_id,'class':'btn-block category-btns counter-btn-blue double btn btn-default'})
	 			.text(val.name)
	 			.appendTo('.menu-cat-container')
	 			.click(function(){
	 				var formData = 'cat_id='+cat_id+'&cat_name='+val.name;
	 				loadsDiv('retail');
	 				loadRetailItemList(formData,$(this));
	 				return false;
	 			});
				ctr++;
			});
			// alert(ctr);
			if(ctr < 9){
				for (var i = 0; i <= (8-ctr); i++) {
					$('<button/>')
		 			.attr({'class':'btn-block category-btns counter-btn-red-gray double btn btn-default'})
		 			.text('')
		 			.appendTo('.menu-cat-container');
				};
			}
		}
		function loadRetailItemList(formData,btn){
			btn.goLoad();
			$.post(baseUrl+'cashier/get_item_lists',formData,function(data){
				$('.retail-title').text(data.title).show();
				$('.retail-loads-div').html(data.code);
				$.each(data.items,function(item_id,opt){
					$('#retail-item-'+item_id).click(function(){
						addRetailTransCart(item_id,opt);
						return false;
					});
				});
				$('#search-item').val('');
				btn.goLoad({load:false});
			},'json');
			// alert(data);
			// });
		}
		function remeload(type_load){
			if(type_load == 'retail'){
				$('#retail-btn').removeClass('counter-btn-red');
				$('#retail-btn').addClass('counter-btn-green');
				loadsDiv('retail');
				$('#go-scan-code').removeClass('counter-btn-orange');
				$('#go-scan-code').addClass('counter-btn-green');
				loadItemCategories();
				$('#scan-code').focus();
			}
			else{
				$('#retail-btn').removeClass('counter-btn-green');
				$('#retail-btn').addClass('counter-btn-red');
			}
		}
		function loadsDiv(type,id,opt,trans_id,other){
			if(type == 'menus'){
				remeload('menu');
				$('.scrollers-menu').remove();
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				$('.menus-div .title').text(opt.name);
				$('.menus-div .items-lists').html('');
				$.post(baseUrl+'cashier/get_menus_sorted/'+id,function(data){
					var div = $('.menus-div .items-lists').append('<div class="row"></div>');
			 		$.each(data,function(key,opt){
			 			
			 			var menu_id = opt.id;
			 			var sCol = $('<div class="col-md-3"></div>');
			 			$('<button/>')
			 			.attr({'id':'menu-'+menu_id,'ref':menu_id,'class':'counter-btn-silver btn btn-block btn-default'})
			 			.text(opt.name)
			 			.appendTo(sCol)
			 			.click(function(){
			 				if(opt.free == 1){
				 				$.callManager({
				 					success : function(){
								 				addTransCart(menu_id,opt);
				 							  }	
				 				});
			 				}
			 				else{
			 					addTransCart(menu_id,opt);
			 				}
			 				return false;
			 			});
			 			sCol.appendTo(div);
			 			
			 		});

			 		$('.menus-div .items-lists').after('<div id="scrollers-menu"><div class="row"><div class="col-md-6 text-left"><button id="menu-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="menu-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
			 		$("#menu-item-scroll-down").on("click" ,function(){
			 		 //    scrolled=scrolled+100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });

	 				    var inHeight = $(".items-lists")[0].scrollHeight;
	 				    var divHeight = $(".items-lists").height();
	 				    var trueHeight = inHeight - divHeight;
	 			        if((scrolled + 150) > trueHeight){
	 			        	scrolled = trueHeight;
	 			        }
	 			        else{
	 			    	    scrolled=scrolled+150;				    	
	 			        }
	 				    // scrolled=scrolled+100;
	 					$(".items-lists").animate({
	 					        scrollTop:  scrolled
	 					},200);
			 		});
			 		$("#menu-item-scroll-up").on("click" ,function(){
			 			// scrolled=scrolled-100;
			 			// $(".items-lists").animate({
			 			//         scrollTop:  scrolled
			 			// });
			 			if(scrolled > 0){
			 				scrolled=scrolled-150;
			 				$(".items-lists").animate({
			 				        scrollTop:  scrolled
			 				},200);
			 			}
			 		});
			 	},'json');
			}
			else if(type=='mods'){
				remeload('menu');
				$('.mods-div .title').text(opt.name+" Modifiers");
				$('.mods-div .mods-lists').html('');
				var trans_det = opt;

				var formData = 'menu_name='+trans_det.name;
				if(other == "addModDefault"){
					formData += '&add_defaults=1';
				}	
				$.post(baseUrl+'cashier/get_menu_modifiers_wth_dflt/'+id+'/'+trans_id,formData,function(data){
					var modGRP = data.group;
					var dfltGRP = data.dflts;
					if(!$.isEmptyObject(dfltGRP)){
						$.each(dfltGRP,function(trans_mod_id,mopt){
							makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,trans_det,"","default");
						});	
					}	
					if(!$.isEmptyObject(modGRP)){
						$('.loads-div').hide();
						$('.'+type+'-div').show();
						$.each(modGRP,function(mod_group_id,opt){
							var row = $('<div/>').attr({'class':'mod-group','id':'mod-group-'+mod_group_id}).appendTo('.mods-div .mods-lists');
							$('<h4/>').text(opt.name)
									  .addClass('text-center receipt')
									  .css({'margin-bottom':'5px'})
									  .appendTo('#mod-group-'+mod_group_id);
							var mandatory = opt.mandatory;
							var multiple = opt.multiple;

							var div = $('#mod-group-'+mod_group_id);
							var divRow = $('<div/>').attr({'class':'row'});
							// var div = $('#mod-group-'+mod_group_id).append('<div class="row"></div>');
							$.each(opt.details,function(mod_id,det){
								var sCol = $('<div class="col-md-4"></div>');
								$('<button/>')
								.attr({'id':'mod-'+mod_id,'ref':mod_id,'class':'counter-btn-silver btn btn-block btn-default'})
								// .css({'margin':'5px','width':'130px'})
								.text(det.name)
								.appendTo(sCol)
								.click(function(){
									addTransModCart(trans_id,mod_group_id,mod_id,det,id,$(this),trans_det,mandatory,multiple);
									return false;
								});
				 				sCol.appendTo(divRow);
				 			});
				 			div.append(divRow);
				 			$('<hr/>').appendTo('#mod-group-'+mod_group_id);
				 		});
						$('.mods-div .mods-lists').after('<div id="scrollers-mods"><div class="row"><div class="col-md-6 text-left"><button id="mods-item-scroll-up" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-up fa-2x fa-fw"></i></button></div><div class="col-md-6 text-left"><button id="mods-item-scroll-down" class="btn-block counter-btn double btn btn-default "><i class="fa fa-fw fa-chevron-circle-down fa-2x fa-fw"></i></button></div></div></div>');
				 		$("#mods-item-scroll-down").on("click" ,function(){
				 		 //    scrolled=scrolled+100;
				 			// $(".items-lists").animate({
				 			//         scrollTop:  scrolled
				 			// });

		 				    var inHeight = $(".mods-lists")[0].scrollHeight;
		 				    var divHeight = $(".mods-lists").height();
		 				    var trueHeight = inHeight - divHeight;
		 			        if((scrolled + 150) > trueHeight){
		 			        	scrolled = trueHeight;
		 			        }
		 			        else{
		 			    	    scrolled=scrolled+150;				    	
		 			        }
		 				    // scrolled=scrolled+100;
		 					$(".mods-lists").animate({
		 					        scrollTop:  scrolled
		 					},200);
				 		});
				 		$("#mods-item-scroll-up").on("click" ,function(){
				 			// scrolled=scrolled-100;
				 			// $(".items-lists").animate({
				 			//         scrollTop:  scrolled
				 			// });
				 			if(scrolled > 0){
				 				scrolled=scrolled-150;
				 				$(".mods-lists").animate({
				 				        scrollTop:  scrolled
				 				},200);
				 			}
				 		});
					}
			 	},'json');
			}
			else if(type=='qty'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='remarks'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else if(type=='charges'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
			else if(type=='sel-discount'){
				$('.loads-div').hide();
				$('.'+type+'-div').show();
				selectModMenu();
			}
			else{
				$('.loads-div').hide();
				$('.'+type+'-div').show();
			}
		}
		function addTransCart(menu_id,opt){
			var formData = 'menu_id='+menu_id+'&name='+opt.name+'&cost='+opt.cost+'&no_tax='+opt.no_tax+'&qty=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,menu_id,data.items);
				loadsDiv('mods',menu_id,data.items,data.id,"addModDefault");
				transTotal();
			},'json');
		}
		function addRetailTransCart(item_id,opt){
			var formData = 'menu_id='+item_id+'&name='+opt.name+'&cost='+opt.cost+'&no_tax=0&qty=1&retail=1';
			$.post(baseUrl+'wagon/add_to_wagon/trans_cart',formData,function(data){
				makeItemRow(data.id,item_id,data.items);
				// loadsDiv('mods',menu_id,data.items,data.id);
				transTotal();
				$('#go-scan-code').removeClass('counter-btn-orange');
				$('#go-scan-code').addClass('counter-btn-green');
				$('#scan-code').focus();
			},'json');
		}
		function addTransModCart(trans_id,mod_group_id,mod_id,opt,menu_id,btn,trans_det,mandatory,multiple){
			var formData = 'trans_id='+trans_id+'&mod_group_id='+mod_group_id+'&mod_id='+mod_id+'&menu_id='+menu_id+'&menu_name='+trans_det.name
							+'&mandatory='+mandatory
							+'&multiple='+multiple
							+'&name='+opt.name+'&cost='+opt.cost+'&qty=1';
			// console.log(formData);
			if(btn != null){
				btn.prop('disabled', true);				
			}
			$.post(baseUrl+'cashier/add_trans_modifier',formData,function(data){
				if(data.error != null){
					rMsg(data.error,'error');
				}
				else{
					// console.log(data.id);
					// console.log(trans_id);
					// console.log(mod_id);
					// console.log(opt);
					makeItemSubRow(data.id,trans_id,mod_id,opt,trans_det)
				}
				if(btn != null){
					btn.prop('disabled', false);
				}
				transTotal();
			},'json');
			// alert(data);
			// });
		}
		function makeItemRow(id,menu_id,opt,loaded){
			$('.sel-row').removeClass('selected');
			var retail = "";
			if (opt.hasOwnProperty('retail')) {
				retail = 'retail-item';
			}

			$('<li/>').attr({'id':'trans-row-'+id,'trans-id':id,'ref':id,'class':'sel-row trans-row selected '+retail+' '+loaded})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					if (!opt.hasOwnProperty('retail')) {
						loadsDiv('mods',menu_id,opt,id);
					}
					else{
						remeload('retail');
					}
					return false;
				});
			$('<span/>').attr('class','qty').text(opt.qty).css('margin-left','10px').appendTo('#trans-row-'+id);
			var namer = opt.name;
			if (opt.hasOwnProperty('retail')) {
				namer = '<i class="fa fa-shopping-cart"></i> '+opt.name;
			}
			if (opt.hasOwnProperty('kitchen_slip_printed')) {
				if(opt.kitchen_slip_printed == 1)
					namer = ' <i class="fa fa-print"></i> '+namer;
			}
			$('<span/>').attr('class','name').html(namer).appendTo('#trans-row-'+id);
			$('<span/>').attr('class','cost').text(opt.cost).css('margin-right','10px').appendTo('#trans-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeItemSubRow(id,trans_id,mod_id,opt,trans_det,loaded,dflt){
			var subRow = $('<li/>').attr({'id':'trans-sub-row-'+id,'trans-id':trans_id,'trans-mod-id':id,'ref':id,'class':'trans-sub-row sel-row '+loaded})
								   .click(function(){
										selector($(this));
										loadsDiv('mods',trans_det.menu_id,trans_det,trans_id);
										return false;
									});
			var mod_name = opt.name;					   
			if (opt.hasOwnProperty('kitchen_slip_printed')) {
				if(opt.kitchen_slip_printed == 1)
					mod_name = ' <i class="fa fa-print"></i> '+mod_name;
			}

			$('<span/>').attr('class','name').css('margin-left','28px').html(mod_name).appendTo(subRow);
			if(parseFloat(opt.cost) > 0)
				$('<span/>').attr('class','cost').css('margin-right','10px').html(opt.cost).appendTo(subRow);

			if(dflt == "default"){
				$('#trans-row-'+trans_id).after(subRow);
			}
			else{
				$('.selected').after(subRow);
			}

			$('.sel-row').removeClass('selected');
			selector($('#trans-sub-row-'+id));
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function selector(li){
			$('.sel-row').removeClass('selected');
			li.addClass('selected');
		}
		function selectModMenu(){
			var sel = $('.selected');
			if(sel.hasClass('trans-sub-row')){
				var trans_id = sel.attr("trans-id");
				selector($('#trans-row-'+trans_id));
			}
		}
		function transTotal(){
			$.post(baseUrl+'cashier/total_trans',function(data){
				var total = data.total;
				var discount = data.discount;
				var local_tax = data.local_tax;
				$("#total-txt").number(total,2);
				$("#discount-txt").number(discount,2);
				if($("#local-tax-txt").exists()){
					$("#local-tax-txt").number(local_tax,2);
				}
				
				if(data.zero_rated > 0){
					$("#zero-rated-btn").removeClass('counter-btn-red');
					$("#zero-rated-btn").addClass('counter-btn-green');
					$("#zero-rated-btn").addClass('zero-rated-active');
					$('.center-div .foot .foot-det').css({'background-color':'#FDD017'});
					$('.center-div .foot .foot-det .receipt').css({'color':'#fff'});		
				}
			},'json');
			// 	alert(data);
			// });
		}
		function loadTransCart(){
			$.post(baseUrl+'cashier/get_trans_cart/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					console.log(data);
					$.each(data,function(trans_id,opt){
						makeItemRow(trans_id,opt.menu_id,opt,'loaded');
						if(opt.remarks != "" && opt.remarks != null){
							makeRemarksItemRow(trans_id,opt.remarks);
						}
						var modifiers = opt.modifiers;
						if(!$.isEmptyObject(modifiers)){
							$.each(modifiers,function(trans_mod_id,mopt){
								makeItemSubRow(trans_mod_id,mopt.trans_id,mopt.mod_id,mopt,mopt,'loaded');
							});
						}
						if(ctr == len)
							$('.selected').trigger('click');
						ctr++;
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function loadTransChargeCart(){
			$.post(baseUrl+'cashier/get_trans_charges/',function(data){
				if(!$.isEmptyObject(data)){
					var len = data.length;
					var ctr = 1;
					$.each(data,function(charge_id,opt){
						makeChargeItemRow(charge_id,opt);
					});
				}
				transTotal();
				// checkWagon('trans_cart');
			},'json');
		}
		function newTransaction(redirect,type){
			$.post(baseUrl+'cashier/new_trans/true/'+type,function(data){
				if(!redirect){
					$('#trans-datetime').text(data.datetime);
					var tp = data.type;
					$('#trans-header').text(tp.toUpperCase());

					$('.trans-lists').find('li').remove();
					var cat_id = $(".category-btns:first").attr('ref');
					var cat_name = $(".category-btns:first").text();
					var val = {'name':cat_name};
					loadsDiv('menus',cat_id,val,null);
					transTotal();
					$('.addon-texts').text('').hide();
					if(type == 'retail')
						remeload('retail');
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
				}
				else{
					if(type=='dinein')
						window.location = baseUrl+'cashier/tables';
					else if(type=='delivery')
						window.location = baseUrl+'cashier/delivery';
					else if(type=='pickup')
						window.location = baseUrl+'cashier/pickup';
					else{
						window.location = baseUrl+'cashier/counter/'+data.type;
					}
				}
			},'json');
		}
		function loadDefault(){
			var cat_id = $(".category-btns:first").attr('ref');
			var cat_name = $(".category-btns:first").text();
			var val = {'name':cat_name};
			loadsDiv('menus',cat_id,val,null);
		}
		function loadDiscounts(){
			$.post(baseUrl+'cashier/get_discounts',function(data){
				$('.select-discounts-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#item-disc-btn-'+id).click(function(){
						var idisc = $(this);
						if(opt.disc_code == 'SNDISC' || opt.disc_code == 'PWDISC'){
							$('#prcss-disc').attr('ref','equal');
						}
						else{
							$('#prcss-disc').attr('ref','all');
						}
						$.callManager({
		 					success : function(){
								loadsDiv('discount',null,null,null);
								$('.discount-div .title').text(idisc.text());
								$('.discount-div #rate-txt').number(opt.disc_rate,2);
								$('#disc-disc-id').val(opt.disc_id);
								$('#disc-disc-rate').val(opt.disc_rate);
								$('#disc-disc-code').val(opt.disc_code);
								$('#disc-no-tax').val(opt.no_tax);
								$('#disc-fix').val(opt.fix);
								$('#disc-guests').val(opt.guest);
								$.post(baseUrl+'cashier/load_disc_persons/'+opt.disc_code,function(data){
									$('.disc-persons-list-div').html(data.code);
									$.each(data.items,function(code,opt){
										$("#disc-person-"+code).click(function(){
											var lin = $(this);
											$.post(baseUrl+'cashier/remove_person_disc/'+opt.disc+'/'+code,function(data){
												lin.remove();
												rMsg('Person Removed.','success');
												transTotal();
											});
											return false;
										});
									});
								},'json');
								// if (typeof opt.name != 'undefined') {
								// 	$('#disc-cust-name').val(opt.name);
								// 	$('#disc-cust-guest').val(opt.guest);
								// 	$('#disc-guests').val(opt.guest);
								// 	$('#disc-cust-code').val(opt.code);
								// 	$('#disc-cust-bday').val(opt.bday);
								// }
		 					}	
		 				});
						return false;
					});
				});
			},'json');
		}
		function loadCharges(){
			$.post(baseUrl+'cashier/get_charges',function(data){
				$('.charges-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#charges-btn-'+id).click(function(){
						addChargeCart(id,opt);
						return false;
					});
				});
			},'json');
		}
		function loadWaiters(){
			$.post(baseUrl+'cashier/get_waiters',function(data){
				$('.waiters-lists').html(data.code);
				$.each(data.ids,function(id,opt){
					$('#waiters-btn-'+id).click(function(){
						$.callFS({
							success : function(emp){
										if(id == emp['emp_id']){
											$.post(baseUrl+'cashier/update_trans/waiter_id/'+id,function(data){
												$('#trans-server-txt').text('FS: '+opt.uname).show();
												rMsg(opt.full_name+' added as Food Server','success');
											},'json');
										}
										else{
											rMsg('Wrong Pin.','error');
										}
									  }
						});
						return false;
					});
				});
			},'json');
		}
		function addChargeCart(id,row){
			var formData = 'name='+row.charge_name+'&code='+row.charge_name+'&amount='+row.charge_amount+'&absolute='+row.absolute;
			$.post(baseUrl+'wagon/add_to_wagon/trans_charge_cart/'+id,formData,function(data){
				if(data.error == null){
					makeChargeItemRow(data.id,data.items);
					// loadsDiv('mods',menu_id,data.items,data.id);
					transTotal();
				}
				else{
					rMsg(data.error,'error');
				}
			},'json');
			// });
		}
		function makeChargeItemRow(id,opt){
			$('.sel-row').removeClass('selected');
			$('<li/>').attr({'id':'trans-charge-row-'+id,'charge-id':id,'ref':id,'class':'sel-row trans-charge-row selected'})
				.appendTo('.trans-lists')
				.click(function(){
					selector($(this));
					loadsDiv('charges');
					return false;
				});
			$('<span/>').attr('class','qty').html('<i class="fa fa-tag"></i>').css('margin-left','10px').appendTo('#trans-charge-row-'+id);
			$('<span/>').attr('class','name').text(opt.name).appendTo('#trans-charge-row-'+id);
			var tx = opt.amount;
			if(opt.absolute == 0){
				tx = opt.amount+'%';
			}
			$('<span/>').attr('class','cost').text(tx).css('margin-right','10px').appendTo('#trans-charge-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function makeRemarksItemRow(id,remarks){
			$('.sel-row').removeClass('selected');
			if($('#trans-remarks-row-'+id).exists()){
				$('#trans-remarks-row-'+id).remove();
			}
			$('<li/>').attr({'id':'trans-remarks-row-'+id,'ref':id,'class':'sel-row trans-remarks-row selected'})
				.insertAfter('.trans-lists li#trans-row-'+id)
				.click(function(){
					selector($(this));
					loadsDiv('remarks');
					$('#line-remarks').val(remarks);
					return false;
				});
			// $('<span/>').attr('class','qty').html('').css('margin-left','10px').appendTo('#trans-remarks-row-'+id);
			$('<span/>').attr('class','name').css('margin-left','26px').html('<i class="fa fa-text-width"></i> '+remarks).appendTo('#trans-remarks-row-'+id);
			$('.counter-center .body').perfectScrollbar('update');
			$(".counter-center .body").scrollTop($(".counter-center .body")[0].scrollHeight);
		}
		function checkWagon(name){
			$.post(baseUrl+'wagon/get_wagon/'+name,function(data){
				alert(data);
			});
		}
		$('#disc-cust-name,#disc-cust-code,#disc-cust-bday,#disc-cust-guest,#line-remarks,#search-item')
        .keyboard({
            alwaysOpen: false,
            usePreview: false,
			autoAccept : true
        })
        .addNavigation({
            position   : [0,0],
            toggleMode : false,
            focusClass : 'hasFocus'
        });
	<?php endif; ?>
});
</script>