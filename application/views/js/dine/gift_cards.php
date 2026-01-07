<script>
$(document).ready(function(){
	<?php if($use_js == 'giftCardFormContainerJs'): ?>
		$('.tab_link').click(function(event)
		{
			event.preventDefault();
			var id = $(this).attr('id');
			loader('#'+id);
		});
		loader('#details_link');
		function loader(btn)
		{
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var selected = $('#gc_idx').val();
			if (selected == '') {
				selected = 'add';
				disableTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			} else {
				disableTabs('.load-tab',true);
			}
			var item_id = $('#gc_idx').val();
			$(tabPane).rLoad({url:baseUrl+loadUrl+'/'+item_id});
		}
		function disableTabs(id,enable)
		{
			if (enable) {
				$(id).parent().removeClass('disabled');
				$(id).removeAttr('disabled','disabled');
				$(id).attr('data-toggle','tab');
			} else {
				$(id).parent().addClass('disabled');
				$(id).attr('disabled','disabled');
				$(id).removeAttr('data-toggle','tab');
			}
		}
		<?php elseif($use_js == 'listFormJs'): ?>
		$('#gift-tbl').rTable({
			loadFrom	: 	 'gift_cards/get_gc',
			noEdit		: 	 true,
			noAdd		: 	 false,
			add			: 	 function(){
								goTo('gift_cards/add_gift_card');
					 		},
			noBtn1 		:   false,
			btn1Txt		: 	"<i class='fa fa-upload '></i> Upload",				 				 	
			btn1 		: 	function(data){
								bootbox.dialog({
								  message: baseUrl+'gift_cards/upload_excel_form',
								  title: "Select Excel Gift Cheque File",
								  buttons: {
								    submit: {
								      label: "<i class='fa fa-check'></i> Upload",
								      className: "btn-success rFormSubmitBtn",
								      callback: function() {
								      		var noError = $('#upload-form').rOkay({
								      			goSubmit 	: 	false
								      		});
								      		if(noError){
								      			$.loadPage();
								      			$('#upload-form').submit();
								      		}
								      		return false;
								      }
								    },
								    close:{
								    	label: "Close",
								    	className: "btn-default",
								    	callback: function() {
								    			return true;
								    	}
								    }
								  }
								});
							},

			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									$('#edit-'+id).click(function(){
										goTo('gift_cards/gift_cards_setup/'+id);
									});
								});
								var table = $('#gift-tbl');
						        var oTable = table.dataTable({

								

						            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
						            "language": {
						                "aria": {
						                    "sortAscending": ": activate to sort column ascending",
						                    "sortDescending": ": activate to sort column descending"
						                },
						                "emptyTable": "No data available in table",
						                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
						                "infoEmpty": "No entries found",
						                "infoFiltered": "(filtered1 from _MAX_ total entries)",
						                "lengthMenu": "_MENU_ entries",
						                "search": "Search:",
						                "zeroRecords": "No matching records found"
						            },

						            // Or you can use remote translation file
						            //"language": {
						            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
						            //},


						            buttons: [
						                { extend: 'print', className: 'btn dark btn-outline'},
						                { extend: 'copy', className: 'btn red btn-outline' },
						                { extend: 'pdf', className: 'btn green btn-outline' },
						                { extend: 'excel', className: 'btn yellow btn-outline ' },
						                { extend: 'csv', className: 'btn purple btn-outline ' },
						                { extend: 'colvis', className: 'btn dark btn-outline', text: 'Columns'}
						            ],

						            // setup responsive extension: http://datatables.net/extensions/responsive/
						            responsive: true,

						            //"ordering": false, disable column ordering 
						            //"paging": false, disable pagination

						            "order": [
						                [0, 'asc']
						            ],
						            
						            "lengthMenu": [
						                [5, 10, 15, 20, -1],
						                [5, 10, 15, 20, "All"] // change per page values here
						            ],
						            // set the initial value
						            "pageLength": 10,

						            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

						            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
						            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
						            // So when dropdowns used the scrollable div should be removed. 
						            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
						        });
								
								// $('#subcategories-tbl').dataTable();
							 }				 				 	 	
			});	
			$('#rtable-btn1-btn').on('click',function(){
				bootbox.dialog({
								  message: baseUrl+'gift_cards/upload_excel_form',
								  title: "Select CSV File",
								  buttons: {
								    submit: {
								      label: "<i class='fa fa-check'></i> Upload",
								      className: "btn-success rFormSubmitBtn",
								      callback: function() {
								      		var noError = $('#upload-form').rOkay({
								      			goSubmit 	: 	false
								      		});
								      		if(noError){
								      			$.loadPage();
								      			$('#upload-form').submit();
								      		}
								      		return false;
								      }
								    },
								    close:{
								    	label: "Close",
								    	className: "btn-default",
								    	callback: function() {
								    			return true;
								    	}
								    }
								  }
								});
			});
			$('#rtable-btn2-btn').on('click',function(){

				bootbox.dialog({
								  message: baseUrl+'gift_cards/filter_gift_card',
								  title: "<i class='fa fa-filter'></i> Filter Gift Cards",
								  buttons: {
								    submit: {
								      label: "<i class='fa fa-check'></i> Submit",
								      className: "btn-success rFormSubmitBtn",
								      callback: function() {
								      		var noError = $('#filter-form').rOkay({
								      			goSubmit 	: 	false
								      		});
								      		if(noError){
								      			$.loadPage();
								      			$('#filter-form').submit();
								      		}
								      		return false;
								      }
								      // return false;
								    },
								    close:{
								    	label: "Close",
								    	className: "btn-default",
								    	callback: function() {
								    			return true;
								    	}
								    }
								  }
								});
								      preventDefault();
			});
			$(document).on('click','#gift_card_template',function(e){
				e.preventDefault();
				console.log('testx');
				window.location = baseUrl+'gift_cards/download_template';
				// $.get(baseUrl+'gift_cards/download_template',function(){

				// });
			});

	<?php elseif($use_js == 'giftCardDetailsJs'): ?>
		$('#save-btn').click(function(e){
			e.preventDefault(); 

			var isValid = true;
			var errorMsg = [];

			if($('#card_no').val().trim() == '') {
				errorMsg.push('Card Number is required');
				isValid = false;
			}
			if($('#amount').val().trim() == '') {
				errorMsg.push('Amount is required');
				isValid = false;
			}
			if($('#description_id').val().trim() == '') {
				errorMsg.push('Description Code is required');
				isValid = false;
			}
			if($('#brand_id').val().trim() == '') {
				errorMsg.push('Brand is required');
				isValid = false;
			}

			if(!isValid) {
				rMsg(errorMsg.join(', '), 'error');
				return false;
			}

			$.ajax({
				url: baseUrl + 'gift_cards/gift_cards_details_db',
				type: 'POST',
				data: $('#gift_cards_details_form').serialize(),
				dataType: 'json',
				beforeSend: function() {
					$('#save-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
				},
				success: function(data) {
					if(data.error) {
						rMsg(data.msg, 'error');
						$('#save-btn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Gift Card Details');
					} else {
						rMsg(data.msg, 'success');
						setTimeout(function(){
							goTo('gift_cards');
						}, 1500);
					}
				},
				error: function() {
					rMsg('An error occurred while saving', 'error');
					$('#save-btn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Gift Card Details');
				}
			});

			return false;
		});

		$('#card_no, #amount')
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
	<?php elseif($use_js == 'addGiftCardJs'): ?>
		$('#add-save-btn').click(function(e){
			e.preventDefault();

			var isValid = true;
			var errorMsg = [];

			$('.form-control').removeClass('error-border').css('border-color', '');

			var cardNo = $('#card_no').val().trim();
			var amount = $('#amount').val().trim();
			var descId = $('#description_id').val().trim();
			var brandId = $('#brand_id').val().trim();

			if(cardNo == '' || cardNo == '0') {
				errorMsg.push('Card Number is required');
				$('#card_no').css('border-color', 'red');
				isValid = false;
			}
			if(amount == '' || amount == '0' || parseFloat(amount) <= 0) {
				errorMsg.push('Amount must be greater than 0');
				$('#amount').css('border-color', 'red');
				isValid = false;
			}
			if(descId == '' || descId == '0') {
				errorMsg.push('Description Code is required');
				$('#description_id').css('border-color', 'red');
				isValid = false;
			}
			if(brandId == '' || brandId == '0') {
				errorMsg.push('Brand is required');
				$('#brand_id').css('border-color', 'red');
				isValid = false;
			}

			if(!isValid) {
				rMsg(errorMsg.join('<br>'), 'error');
				return false;
			}

			$.ajax({
				url: baseUrl + 'gift_cards/add_gift_card_db',
				type: 'POST',
				data: $('#add_gift_card_form').serialize(),
				dataType: 'json',
				beforeSend: function() {
					$('#add-save-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
				},
				success: function(data) {
					$('#add-save-btn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Gift Card');

					if(data.error) {
						rMsg(data.msg, 'error');
					} else {
						rMsg(data.msg, 'success');
						setTimeout(function(){
							goTo('gift_cards');
						}, 1500);
					}
				},
				error: function(xhr, status, error) {
					console.log('Error:', error);
					rMsg('An error occurred while saving', 'error');
					$('#add-save-btn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Gift Card');
				}
			});

			return false;
		});

		$('#card_no, #amount')
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
	<?php elseif($use_js == 'giftCardsJs'): ?>
		$('#new-gift-card-btn').click(function(){
			// alert('New Customer Button');
			window.location = baseUrl+'gift_cards/cashier_gift_cards';
			return false;
		});
		
		$('#look-up-btn').click(function(){
			// alert('Look up button');
			
			var this_url =  baseUrl+'gift_cards/gift_cards_list';
			$.post(this_url, {},function(data){
				$('.gc_content_div').html(data);
				// $('#cardno').attr({'value' : ''}).val('');
				
				$('.edit-line').click(function(){
					var line_id = $(this).attr('ref');
					var cardno = $(this).attr('cardnoref');
					var thisurl = baseUrl+'gift_cards/load_gift_cards_details';
					// alert('edit to : '+line_id);
					
					$.post(thisurl, {'cardno' : cardno}, function(data1){
						$('.gc_content_div').html(data1);
					});
					
					return false;
				});
				
			});
			return false;
		});
		
		$('#exit-btn').click(function(){
			window.location = baseUrl+'cashier';
			return false;
		});
		
		$('#pin')
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
		
		$('#cardno').focus(function(){
			$('#cardno').attr({'value' : ''}).val('');
			return false;
		});
		
		$('#cardno-login').click(function(){
			var cardno = $('#cardno').val();
			var this_url =  baseUrl+'gift_cards/validate_card_number';
			// alert('asdfg---'+cardno);
			
			$.post(this_url, {'cardno' : cardno}, function(data){
				// alert(data);
				var parts = data.split('||');
				// alert('eto:'+parts[1]);
				if(parts[0] == 'empty'){
					rMsg('Text field is empty!','error');
					// setTimeout(function(){
						// window.location.reload();
					// },1500);
				}else if(parts[0] == 'none'){
					rMsg('Gift Card does not exist.','error');
					// setTimeout(function(){
						// window.location.reload();
					// },1500);
				}else if(parts[0] == 'success'){
					rMsg('Loading Gift Card Details...','success');
					
					setTimeout(function(){
						var this_url2 =  baseUrl+'gift_cards/load_gift_cards_details';
						$.post(this_url2, {'cardno' : parts[1]},function(data){
							$('.gc_content_div').html(data);
							// $('#cardno').attr({'value' : ''}).val('');
							return false;
						});
					},1500);
						
				}
			});
			
			return false;
		});
		
		function loadGiftCardDetails(){
			var this_url =  baseUrl+'gift_cards/gift_cards_load';
			
			$.post(this_url, {},function(data){
				$('.gc_content_div').html(data);
				$('#cardno').focus();
				return false;
			});
		}
		
		loadGiftCardDetails();
		
	<?php endif; ?>
});
</script>