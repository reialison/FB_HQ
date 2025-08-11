<script>
$(document).ready(function(){
	<?php if($use_js == 'listFormJs'): ?>
		$('#menus-tbl').rTable({
			loadFrom	: 	 'menu/get_menus',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('menu/form');
							 },				 	
			edit		: 	 function(id){
				// console.log($(this));
				console.log(id);
								goTo('menu/form/'+id);
							 },
			noBtn1 		:   false,
			btn1Txt		: 	"<i class='fa fa-upload '></i> Upload",				 				 	
			btn1 		: 	function(data){
								bootbox.dialog({
								  message: baseUrl+'menu/upload_excel_form',
								  title: "Select Excel Menu File",
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
			dataExport 		:   true,
			afterLoad 	: 	 function(d){
				console.log(d);
								var table = $('#menus-tbl');
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
						            responsive: false,

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
							 }						
		});
	<?php elseif($use_js == 'subcategoryListJs'): ?>
		$('#subcategories-tbl').rTable({
			loadFrom	: 	 'menu/get_subcategories',
			noEdit		: 	 true,
			add			: 	 function(){
								$.rPopForm({
									loadUrl : 'menu/subcategories_form',
									passTo	: 'menu/subcategories_form_db',
									title	: 'Menu Types',
									rform 	: 'subcategories_form',
									onComplete : function(){
													goTo('menu/subcategories');
												 }
								});
							 },
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var branch = $(this).attr('branch');
									$('#edit-'+id+'-'+branch).click(function(){
										$.rPopForm({
											loadUrl : 'menu/subcategories_form/'+id+'/'+branch,
											passTo	: 'menu/subcategories_form_db',
											title	: 'Edit SubCategory',
											rform 	: 'subcategories_form',
											onComplete : function(data){
															// alert(data);
															goTo('menu/subcategories/');
														 }
										});
										return false;
									});
								});
								var table = $('#subcategories-tbl');
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
	<?php elseif($use_js == 'categoryListJs'): ?>
		$('#categories-tbl').rTable({
			loadFrom	: 	 'menu/get_menu_categories',
			noEdit		: 	 true,
			add			: 	 function(){
								$.rPopForm({
									loadUrl : 'menu/categories_form',
									passTo	: 'menu/categories_form_db',
									title	: 'Add New Category',
									rform 	: 'categories_form',
									onComplete : function(){
													goTo('menu/categories');
												 }
								});
							 },
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var branch = $(this).attr('branch');
									$('#edit-'+id+'-'+branch).click(function(data){
										$.rPopForm({
											loadUrl : 'menu/categories_form/'+id+'/'+branch,
											passTo	: 'menu/categories_form_db',
											title	: 'Edit Category',
											rform 	: 'categories_form',
											onComplete : function(){
															goTo('menu/categories/');
														 }
										});
										return false;
									});
								});
								var table = $('#categories-tbl');
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

								// $('#categories-tbl').dataTable();
							 }				 				 	
			// edit		: 	 function(id){

			// 					goTo('menu/form/'+id);
			// 				 }				 	
		});	
	<?php elseif($use_js == 'menuFormJs'): ?>
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});

		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var menu_id = $('#menu_id').val();
			var branch_code = $('#branch_code').val();

			if(menu_id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			$(tabPane).rLoad({url:baseUrl+loadUrl+menu_id+'/'+branch_code});
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
		function readURL(input) {
        	if (input.files && input.files[0]) {
	            var reader = new FileReader();
	            reader.onload = function (ev) {
	               
		            $('#pic-form').submit(function(e){
					    var formObj = $(this);
					    var formURL = formObj.attr("action");
					    var formData = new FormData(this);
					    $.ajax({
					        url: baseUrl+formURL,
					        type: 'POST',
					        data:  formData,
					        dataType:  'json',
					        mimeType:"multipart/form-data",
					        contentType: false,
					        cache: false,
					        processData:false,
					        success: function(data, textStatus, jqXHR){
								if(data.msg == ""){
					                $('#item-pic').attr('src',ev.target.result);
									rMsg('Item Image Uploaded','success');
								}
								else{
									rMsg(data.msg,'error');
								}
					        },
					        error: function(jqXHR, textStatus, errorThrown){
								alert(textStatus);
					        }         
					    });
					    e.preventDefault();
					//     e.unbind();
					});
					$('#pic-form').submit();
	            }
	            reader.readAsDataURL(input.files[0]);
	        }
	    }
    	$("#fileUpload").change(function(){
	        readURL(this);        			
	    });
	    $('#target').click(function(e){
	    	$('#fileUpload').trigger('click');
	    }).css('cursor', 'pointer');
	<?php elseif($use_js == 'menuImageJs'): ?>
		$('#save-image').click(function(){
			
			var noError = $('#images_form').rOkay({
    			asJson			: 	false,
				btn_load		: 	null,
				goSubmit		: 	false,
				bnt_load_remove	: 	true
    		});
    		
    		if(noError){
    			$("#images_form").submit(function(e){
				    var formObj = $(this);
				    var formURL = formObj.attr("action");
				    var formData = new FormData(this);
				    $.ajax({
				        url: baseUrl+formURL,
				        type: 'POST',
				        data:  formData,
				//         dataType:  'json',
				        mimeType:"multipart/form-data",
				        contentType: false,
				        cache: false,
				        processData:false,
				        success: function(data, textStatus, jqXHR){
							if(data != ""){
								rMsg(data,'error');
							}
							else{
								rMsg('Image uploaded.','success');
							}
				        },
				        error: function(jqXHR, textStatus, errorThrown){
				        }         
				    });
				    e.preventDefault();
				    e.unbind();
				});
				$("#images_form").submit();
    		}
    		return false;
		});
		function readURL(input) {
        	if (input.files && input.files[0]) {
	            var reader = new FileReader();
	            reader.onload = function (e) {
	            	// alert(e.target.result);
	                $('#target').attr('src', e.target.result);
	                // $('#target').html(e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);
	        }
	    }
    	$("#fileUpload").change(function(){
	        readURL(this);
	    });
	    $('#target').click(function(e){
	    	$('#fileUpload').trigger('click');
	    }).css('cursor', 'pointer');
	<?php elseif($use_js == 'detailsLoadJs'): ?>
		$('#menu_cat_id').rAddOpt({
			text 	: 'Add New Menu Category',
			loadUrl : 'menu/categories_form',
			passTo 	: 'menu/categories_form_db',
			form 	: 'categories_form'
		});
		$('#menu_sub_cat_id').rAddOpt({
			text 	: 'Add New Menu SubCategory',
			loadUrl : 'menu/subcategories_form',
			passTo 	: 'menu/subcategories_form_db',
			form 	: 'subcategories_form'
		});
		$('#menu_sub_id').rAddOpt({
			text 	: 'Add New SubCategory',
			loadUrl : 'menu/subcategories_form_new',
			passTo 	: 'menu/subcategories_form_new_db',
			form 	: 'subcategories_form_new'
		});
		$('#save-menu').click(function(){
			// var branch = $('select[name^=b_code]').val();
			var branch = $('select[name^=b_code]').val();
			if(branch == null) { 
				rMsg("Error! Branch must not be empty.",'error');
				return false;
			}
			$("#details_form").rOkay({
				btn_load		: 	$('#save-menu'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// console.log(data);
										// alert(data);

										if(data.act == "update"){
											window.location = baseUrl+'menu/';
										}
										else{
											window.location = baseUrl+'menu/form/';
										}
										// if(typeof data.msg != 'undefined' ){
										// 	window.location = baseUrl+'menu/form/';
										// 		console.log(data);
										// 	// // $('#menu_id').val(data.id);
										// 	// window.location = baseUrl+'menu/form/'+data.id+'/'+data.branch_code;
										// 	// // $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
										// 	// // disEnbleTabs('.load-tab',true);
										// 	// // rMsg(data.msg,'success');
										// }
									}
			});
			return false;
		});
		$('#save-new-menu').click(function(){
			$("#details_form").rOkay({
				btn_load		: 	$('#save-new-menu'),
				bnt_load_remove	: 	true,
				addData			: 	'new=1',
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										if(typeof data.msg != 'undefined' ){
											window.location = baseUrl+'menu/form/'+data.id;
											// $('#menu_id').val(data.id);
											// $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
											// disEnbleTabs('.load-tab',true);
											// rMsg(data.msg,'success');
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
	<?php elseif($use_js == 'scheduleJs'): ?>

		$(".timepicker").timepicker({
	       showInputs: false,
	       minuteStep: 1
	    });
	    var schd_id = $('#menu_sched_id').val();
	    if(schd_id == ''){
	    	$('#add-schedule').attr('disabled','disabled');
	    }
	    $('#add-schedule').click(function(){
	    	$("#schedules_details_form").rOkay({
					btn_load		: 	$('#add-schedule'),
					bnt_load_remove	: 	true,
					asJson			: 	true,
					onComplete		:	function(data){
											// alert(data.id);
											if(data.msg == 'error'){
												msg = 'Day already duplicated in this schedule.';
												rMsg(msg,'error');
											}
											else if(typeof data.msg != 'undefined' ){
												var sel = $('#promo-drop').val();
												$('#group-detail-con').rLoad({url:baseUrl+'menu/schedules_form/'+data.id});
												rMsg(data.msg,'success');
											}
										}
				});
	    	return false;
	    });
	    $('.del-sched').each(function(){
			var id = $(this).attr('ref');
			deleteSched(id);
		});
		function deleteSched(id){
			$('#del-sched-'+id).click(function(){
				// alert(id);
				var formData = 'pr_sched_id='+id;
				var li = $(this).parent().parent();
				// alert(baseUrl+'settings/remove_promo_details');
				$.post(baseUrl+'menu/remove_schedule_promo_details',formData,function(data){
					// alert('zxc');
					rMsg(data.msg,'success');
					li.remove();
					var noLi = $('#staff-list li').length;
					if(noLi == 0){
						$('ul#staff-list').append('<li class="no-staff"><span class="handle"><i class="fa fa-fw fa-ellipsis-v"></i></span><span class="text">No Staffs found.</span></li>');
					}
					// });
				},'json').fail( function(xhr, textStatus, errorThrown) {
				   alert(xhr.responseText);
				});
				return false;
			});
		}
		$(".timepicker").timepicker({
			    showInputs: false,
			    minuteStep: 1
			});
			$('#save-list-form').click(function(){
				// alert('zxczxc');
				return false;
			});
			// $('#add-schedule').click(function(){
			// 	var sched_id = $('#menu_sched_id').val();
			// 	var timeon = $('#time-on').val();
			// 	var timeoff = $('#time-off').val();
			// 	var day = $('#day').val();
			// 	var formData = 'sched_id='+sched_id+'&time_on='+encodeURIComponent(timeon)+'&time_off='+encodeURIComponent(timeoff)+'&day='+day;
			// 	// alert(formData);
			// 	$.post(baseUrl+'menu/menu_sched_details_db',formData,function(data){

			// 		rMsg(data.msg,'success');
			// 	},'json');
			// 	// });
			// 	// return false;

			// 	return false;
			// });
	<?php elseif($use_js == 'recipeLoadJs'): ?>
		// $('#item-search').typeaheadmap({
		// 	"source": function(search, process) {
		// 		var url = $('#item-search').attr('search-url');
		// 		var formData = 'search='+search;
		// 		$.post(baseUrl+url,formData,function(data){
		// 			process(data);
		// 		},'json');
		// 	},
		//     "key": "key",
		//     "value": "value",
		//     "listener": function(k, v) {
		// 		$('#item-id-hid').val(v);
		// 		get_item_details(v);
		// 	}
		// });
	 		$('#all-branch-print').click(function(){
	 			// alert(baseUrl);
				// date = $('#date').val();
				// branch_id = $('#branch_id').val();
				$.rProgressBar();
					// alert(data);
					// console.log(data);
				$.rProgressBarEnd({
					onComplete : function(){
						window.location =baseUrl+'menu/recipe_listing/1';
					// alert(asd);
					}
				});
			});
		$('#item-search').change(function(){
			var item = $(this).val();
			$('#item-id-hid').val(item);
			get_item_details(item);
		});

		$('#add-btn').click(function(event)
		{
			event.preventDefault();
			$('#recipe-details-form').rOkay({
				btn_load	         : $('#add-btn'),
				btn_load_remove 	 : true,
				asJson            	 : true,
				onComplete        	 : function(data) {
											// alert(data);
											if (data.act == 'add') {
												$('#details-tbl').find('tr:last').prev().after(data.row);
												rMsg(data.msg,'success');
											} else {
												var i = $('#row-'+data.id);
												$('#row-'+data.id).remove();
												$('#details-tbl').find('tr:last').prev().after(data.row);
												rMsg(data.msg,'success');
											}
											$('#item-cost').val('');
											$('#item-uom-hid').val('');
											$('#uom-txt').text('');
											$('#qty').val('');
											$('#item-search').val('').selectpicker('refresh')
											get_recipe_total();
											remove_row(data.id);
											$("#recipe-details-form")[0].reset();
										}
			});
		});
		function get_item_details(v){
			var branch = $('#recipe-branch-hid').val();
			$.post(baseUrl+'menu/recipe_item_details/'+v+'/'+branch,function(data){
				$('#item-cost').val(data.cost);
				$('#item-uom-hid').val(data.uom);
				$('#item-branch-hid').val(data.branch_code);
				$('#uom-txt').text(data.uom);
				
			},'json');
		}

		function get_recipe_total()
		{
			var mid = $( '#menu-id-hid' ).val();
			var branch = $('#recipe-branch-hid').val();
			if ( mid != "" ) {
				$.post( baseUrl+'menu/get_recipe_total','menu_id='+mid+'&branch_code='+branch,function(data){
					// alert(data.total);
					// console.log(data);
					// $('#total').val(data.total);
					$('#total').text(data.total);
				},'json');
			}
		}
		function remove_row(id){
			$('#del-'+id).click(function(){
				$.post(baseUrl+'menu/remove_recipe_item','recipe_id='+id,function(data){
					$('#row-'+id).remove();
					rMsg(data.msg,'success');
				},'json');
				get_recipe_total();
				return false;
			});
		}
		$('#override-price').click(function(event){
			event.preventDefault();
			var mid = $( '#menu-id-hid' ).val();
			if ( mid != "" ) {
				var total = $('#total').val();
				$.post(baseUrl+'menu/override_price_total','menu_id='+mid+'&total='+total,function(data){
					rMsg('<b>Recipe price has been updated</b>','success');
				});
			}
		});

		$('.del-item').click(function(event)
		{
			event.preventDefault();
			var id = $(this).attr('ref');
			$.post(baseUrl+'menu/remove_recipe_item','recipe_id='+id,function(data){
				$('tr#row-'+id).remove();
				get_recipe_total();
				rMsg(data.msg,'success');
			},'json');
		});
	<?php elseif($use_js == 'menuModifierJs'): ?>
		// $('#item-search').typeaheadmap({
		// 	"source": function(search, process) {
		// 		var url = $('#item-search').attr('search-url');
		// 		var formData = 'search='+search;
		// 		$.post(baseUrl+url,formData,function(data){
		// 			process(data);
		// 		},'json');
		// 	},
		//     "key": "key",
		//     "value": "value",
		//     "listener": function(k, v) {
		// 		$('#mod-group-id-hid').val(v);

		// 		$('#menu-modifier-form').rOkay({
		// 			btn_load	         : $('#add-btn'),
		// 			btn_load_remove 	 : true,
		// 			asJson            	 : true,
		// 			onComplete        	 : function(data) {
		// 										if (data.result == 'success') {
		// 											$('#details-tbl').append(data.row);
		// 											rMsg(data.msg,'success');
		// 											remove_row(data.id);
		// 										} else {
		// 											rMsg(data.msg,'error');
		// 										}
		// 									}
		// 		});
		// 	}
		// });
		$('#add-grp-modifier').click(function(){
			var grp_id = $('#item-search').val();
			
			$('#mod-group-id-hid').val(grp_id);
			$('#menu-modifier-form').rOkay({
				btn_load	         : $('#add-grp-modifier'),
				btn_load_remove 	 : true,
				asJson            	 : true,
				onComplete        	 : function(data) {
											console.log(data);
											if (data.result == 'success') {
												$('#details-tbl').append(data.row);
												rMsg(data.msg,'success');
												remove_row(data.id);
												$('#item-search').selectpicker('val','');
											} else {
												rMsg(data.msg,'error');
											}
										}
			});			
			return false;
		});
		// $('#add-btn').click(function(event)
		// {
		// 	event.preventDefault();
		// 	$('#menu-modifier-form').rOkay({
		// 		btn_load	         : $('#add-btn'),
		// 		btn_load_remove 	 : true,
		// 		// asJson            	 : true,
		// 		onComplete        	 : function(data) {
		// 			alert(data);
		// 									// if (data.result == 'success') {
		// 									// 	$('#details-tbl').append(data.row);
		// 									// 	rMsg(data.msg,'success');
		// 									// 	remove_row(data.id);
		// 									// } else {
		// 									// 	rMsg(data.msg,'error');
		// 									// }
		// 									// remove_row(data.id);
		// 								}
		// 	});
		// });
		function remove_row(id){
			$('#del-'+id).click(function(){
				$.post(baseUrl+'menu/remove_menu_modifier','recipe_id='+id,function(data){
					$('#row-'+id).remove();
					rMsg(data.msg,'success');
				},'json');
				return false;
			});
		}
        $(document).on('click','.del-item',function(event){
		// $('.del-item').click(function(event)
		// {
			event.preventDefault();
			var id = $(this).attr('ref');
			$.post(baseUrl+'menu/remove_menu_modifier','id='+id,function(data){
				// console.log(data);
				$('tr#row-'+id).remove();
				rMsg(data.msg,'success');
			},'json');
		});

	<?php elseif($use_js == 'subcategoryListNewJs'): ?>
		$('#subcategory-tbl').rTable({
			loadFrom	: 	 'menu/get_subcategories_new',
			noEdit		: 	 true,
			add			: 	 function(){
								$.rPopForm({
									loadUrl : 'menu/subcategories_form_new',
									passTo	: 'menu/subcategories_form_new_db',
									title	: 'Add New SubCategory',
									rform 	: 'subcategories_form_new',
									onComplete : function(data){
												// alert(data);
													goTo('menu/subcategories_new');
												 }
								});
							 },
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var branch = $(this).attr('branch');
									$('#edit-'+id+'-'+branch).click(function(){
										// console.log(id+ ' : '+branch );
										$.rPopForm({
											loadUrl : 'menu/subcategories_form_new/'+id+'/'+branch,
											passTo	: 'menu/subcategories_form_new_db',
											title	: 'Edit SubCategory',
											rform 	: 'subcategories_form_new',
											onComplete : function(data){
														// alert(data);
															goTo('menu/subcategories_new/');
														 }
										});
										return false;
									});
								});
								var table = $('#subcategory-tbl');
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
	<?php elseif($use_js == 'menuPricesJs'): ?>
		// $('#item-search').typeaheadmap({
		// 	"source": function(search, process) {
		// 		var url = $('#item-search').attr('search-url');
		// 		var formData = 'search='+search;
		// 		$.post(baseUrl+url,formData,function(data){
		// 			process(data);
		// 		},'json');
		// 	},
		//     "key": "key",
		//     "value": "value",
		//     "listener": function(k, v) {
		// 		$('#mod-group-id-hid').val(v);

		// 		$('#menu-modifier-form').rOkay({
		// 			btn_load	         : $('#add-btn'),
		// 			btn_load_remove 	 : true,
		// 			asJson            	 : true,
		// 			onComplete        	 : function(data) {
		// 										if (data.result == 'success') {
		// 											$('#details-tbl').append(data.row);
		// 											rMsg(data.msg,'success');
		// 											remove_row(data.id);
		// 										} else {
		// 											rMsg(data.msg,'error');
		// 										}
		// 									}
		// 		});
		// 	}
		// });
		$('#add-price').click(function(){
			var trans_type = $('#trans_type').val();
			
			// $('#mod-group-id-hid').val(grp_id);
			$('#menu-price-form').rOkay({
				btn_load	         : $('#add-price'),
				btn_load_remove 	 : true,
				asJson            	 : true,
				onComplete        	 : function(data) {
											if (data.result == 'success') {
												$('#details-tbl').append(data.row);
												rMsg(data.msg,'success');
												// remove_row(data.id);
												// $('#item-search').selectpicker('val','');
											} else {
												rMsg(data.msg,'error');
											}
										}
			});			
			return false;
		});
		// $('#add-btn').click(function(event)
		// {
		// 	event.preventDefault();
		// 	$('#menu-modifier-form').rOkay({
		// 		btn_load	         : $('#add-btn'),
		// 		btn_load_remove 	 : true,
		// 		// asJson            	 : true,
		// 		onComplete        	 : function(data) {
		// 			alert(data);
		// 									// if (data.result == 'success') {
		// 									// 	$('#details-tbl').append(data.row);
		// 									// 	rMsg(data.msg,'success');
		// 									// 	remove_row(data.id);
		// 									// } else {
		// 									// 	rMsg(data.msg,'error');
		// 									// }
		// 									// remove_row(data.id);
		// 								}
		// 	});
		// });
		function remove_row(id){
			// alert('wawa');
			$('#del-'+id).click(function(){
				$.post(baseUrl+'menu/remove_menu_price','id='+id,function(data){
					$('#row-'+id).remove();
					rMsg(data.msg,'success');
				},'json');
				return false;
			});
		}
		// $('.del-item').click(function(event)
		$('.del-item').off('click');
		$(document).on('click','.del-item',function(event)
		{	
			// alert('wewe');
			// event.preventDefault();
			var id = $(this).attr('ref');
			$.post(baseUrl+'menu/remove_menu_price','id='+id,function(data){
				$('tr#row-'+id).remove();
				rMsg(data.msg,'success');
			},'json');
		});
	<?php elseif($use_js == 'inventoryJS'): ?>
		var table = $('.data-table');
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
                { extend: 'print', className: 'btn dark btn-outline' },
                { extend: 'copy', className: 'btn red btn-outline' },
                { extend: 'pdf', className: 'btn green btn-outline' },
                { extend: 'excel', className: 'btn yellow btn-outline ' },
                { extend: 'csv', className: 'btn purple btn-outline ' },
                { extend: 'colvis', className: 'btn dark btn-outline', text: 'Columns'}
            ],

            // setup responsive extension: http://datatables.net/extensions/responsive/
            responsive: false,
            dom: 'Blfrtip',

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
	<?php endif; ?>
});
</script>