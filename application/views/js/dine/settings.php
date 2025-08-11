<script>
$(document).ready(function(){
	<?php if($use_js == 'categoryListFormJs'): ?>
		$('#categories-tbl').rTable({
			loadFrom	: 	 'settings/get_categories',
			noEdit		: 	 true,
			add			: 	 function(){
								$.rPopForm({
									loadUrl : 'settings/category_form',
									passTo	: 'settings/category_db',
									title	: 'Add New Category',
									rform 	: 'category_form',
									onComplete : function(data){
										// console.log(data);
													goTo('settings/categories');
												 }
								});
							 },
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									$('#edit-'+id+'-'+bcode).click(function(){
										// alert('#edit-'+id+'-'+bcode);
										$.rPopForm({
											loadUrl : 'settings/category_form/'+id+'/'+bcode,
											passTo	: 'settings/category_db',
											title	: 'Edit Category',
											rform 	: 'category_form',
											onComplete : function(data){
												// console.log(data);
															goTo('settings/categories/');
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
		});	
	<?php elseif($use_js == 'subcategoryListFormJs'): ?>
		$('#subcategories-tbl').rTable({
			loadFrom	: 	 'settings/get_subcategories',
			noEdit		: 	 true,
			add			: 	 function(){
								$.rPopForm({
									loadUrl : 'settings/subcategory_form',
									passTo	: 'settings/subcategory_db',
									title	: 'Add New SubCategory',
									rform 	: 'subcategory_form',
									onComplete : function(){
										// console.log(data);
													goTo('settings/subcategories');
												 }
								});
							 },
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									$('#edit-'+id+'-'+bcode).click(function(){
										console.log(bcode);
										$.rPopForm({
											loadUrl : 'settings/subcategory_form/'+id+'/'+bcode,
											passTo	: 'settings/subcategory_db',
											title	: 'Edit SubCategory',
											rform 	: 'subcategory_form',
											onComplete : function(){
															goTo('settings/subcategories/');
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
	<?php elseif($use_js == 'seatingJs'): ?>
		displayImage();
		$('#change-img').rPopFormFile({
			asJson	  : true,
			onComplete: function(data){
				rMsg(data.msg,'success');
				$('#imgSrc').val(baseUrl+data.src);				
				$('#imgCon').html("");
				// alert(data.src);
				$('#change-img').html('<i class="fa fa-picture-o"></i> Change Image');
				bootbox.hideAll();
				displayImage();
				location.reload();
			}
		});
		$('#cancel-move-btn').click(function(){
			$('.marker').show();
			$('.move-shows').hide();
			$('#rtag-div').removeClass('moving');
			$('#rtag-div').removeAttr('moving-id');
			return false;
		});
		function displayImage(){
			var img = $('#imgSrc').val();
			if(img != ""){
				var img_real_width=0,
				    img_real_height=0;
				$("<img/>")
				    .attr("src", img)
				    .attr("id", "imgLayout")
				    .load(function(){
			           img_real_width = this.width;
			           img_real_height = this.height;
			           $(this).appendTo('#imgCon');
			           $("<div/>")
					    .attr("class", "rtag")
					    .attr("id", "rtag-div")
					    .css("height", img_real_height)
					    .css("width", img_real_width)
					    .css("left", '15px')
					    .appendTo('#imgCon')
					    .click(function(e){
			    			var offset_t = $(this).offset().top - $(window).scrollTop();
							var offset_l = $(this).offset().left - $(window).scrollLeft();
							var left = Math.round( (e.clientX - offset_l) - 10 );
							var top = Math.round( (e.clientY - offset_t) - 10 );
					    	if($(this).hasClass('moving')){
					    		var tbid = $(this).attr('moving-id');
					    		var name = $(this).attr('moving-name');
					    		var formData = 'tbl_id='+tbid+'&top='+top+'&left='+left;
					    		$.post(baseUrl+'settings/tables_update_pos',formData,function(data){
	    							rMsg(data.msg,'success');
	    							var tbl_id = data.id;
	    							$('#mark-'+tbl_id).remove();
	    							$('<a/>')
	    				    			.attr('href','#')
	    				    			.attr('class','marker')
	    				    			.attr('id','mark-'+tbl_id)
	    				    			.css('top',top+'px')
	    				    			.css('left',left+'px')
	    				    			.html('<h5 style="text-align:center;padding-top:7px;color:#fff">'+name+'</h5>')
	    				    			.appendTo('#rtag-div')
	    				    			.click(function(e){
	    				    				showDeleteDialog(tbl_id,top,left,name)
	    				    				return false;
	    			    			});
	    				    		$('.marker').show();
	    				    		$('.move-shows').hide();
	    				    		$('#rtag-div').removeClass('moving');
	    				    		$('#rtag-div').removeAttr('moving-id');
					    		},'json');
					    	}
					    	else{
			    				showDialog('',top,left);
					    	}
				    		return false;
			   			});
			   			loadMarks(); 
				    });  
			}
		}
		function loadMarks(){
			$.post(baseUrl+'settings/get_tables',function(data){
				$.each(data,function(key,val){
					$('<a/>')
		    			.attr('href','#')
		    			.attr('class','marker')
		    			.attr('id','mark-'+key)
		    			.css('top',val.top+'px')
		    			.css('left',val.left+'px')
		    			.appendTo('#rtag-div')
		    			.html('<h5 style="text-align:center;padding-top:7px;color:#fff">'+val.name+'</h5>')
		    			.click(function(e){
		    				showDeleteDialog(key,val.top,val.left,val.name);
		    				return false;
	    			});
				});
			},'json');
		}
		function showDialog(tbl_id,top,left){
			bootbox.dialog({
			  message: baseUrl+'settings/tables_form/'+tbl_id,
			  title: 'Table Details',
			  buttons: {
			    cancel: {
			      label: "Cancel",
			      className: "btn-default",
			      callback: function() {
			        // Example.show("uh oh, look out!");
			      }
			    },
			    submit: {
			      label: "<i class='fa fa-plus'></i> Add",
			      className: "btn-primary rFormSubmitBtn",
			      callback: function() {
						$('#table_form').rOkay({
								passTo		: 	$('#table_form').attr('action'),
								addData		: 	'top='+top+'&left='+left,
								asJson		: 	true,
								btn_load	: 	$('.rFormSubmitBtn'),
								onComplete	: 	function(data){
													var tbl_id = data.id;
													var name = data.desc;
													$('<a/>')
										    			.attr('href','#')
										    			.attr('class','marker')
										    			.attr('id','mark-'+tbl_id)
										    			.css('top',top+'px')
										    			.css('left',left+'px')
										    			.html('<h5 style="text-align:center;padding-top:7px;color:#fff">'+name+'</h5>')
										    			.appendTo('#rtag-div')
										    			.click(function(e){
										    				showDeleteDialog(tbl_id,top,left,name)
										    				return false;
									    				});
										    		bootbox.hideAll();	
												}
						});
						// alert('something');
						return false;	
			      }
			    }
			  }
			});
		}
		function showDeleteDialog(tbl_id,top,left,name){
			bootbox.dialog({
			  message: baseUrl+'settings/tables_form/'+tbl_id,
			  title: 'Table Details',
			  buttons: {
			    cancel: {
			      label: "Cancel",
			      className: "btn-default",
			      callback: function() {
			        // Example.show("uh oh, look out!");
			      }
			    },
			    move: {
			      label: "<i class='fa fa-refresh'></i> Move",
			      className: "btn-info",
			      callback: function() {
			        	$('.marker').hide();
			        	$('.move-shows').show();
			        	$('#rtag-div').addClass('moving');
			        	$('#rtag-div').attr('moving-id',tbl_id);
			        	$('#rtag-div').attr('moving-name',name);
			      }
			    },
			    submit: {
			      label: "<i class='fa fa-save'></i> Update",
			      className: "btn-primary rFormSubmitBtn",
			      callback: function() {
						$('#table_form').rOkay({
								passTo		: 	$('#table_form').attr('action'),
								addData		: 	'top='+top+'&left='+left,
								asJson		: 	true,
								btn_load	: 	$('.rFormSubmitBtn'),
								onComplete	: 	function(data){
													var name = data.desc;
													var tbl_id = data.id;
													rMsg(data.msg,'success');
													$('#mark-'+tbl_id).html('<h5 style="text-align:center;padding-top:7px;color:#fff">'+name+'</h5>')
																	  .unbind('click')
														    			.click(function(e){
														    				showDeleteDialog(tbl_id,top,left,name)
														    				return false;
													    				});
										    		bootbox.hideAll();	
												}
						});
						// alert('something');
						return false;	
			      }
			    },
			    "Delete": {
			      label: "<i class='fa fa-trash-o'></i> Delete",
			      className: "btn-danger rFormSubmitBtn",
			      callback: function() {
						$('#table_form').rOkay({
								passTo		: 	$('#table_form').attr('action'),
								addData		: 	'delete='+tbl_id,
								asJson		: 	true,
								btn_load	: 	$('.rFormSubmitBtn'),
								onComplete	: 	function(data){
													rMsg(data.msg,'success');
													$('#mark-'+data.id).remove();
										    		bootbox.hideAll();	
												}
						});
						// alert('something');
						return false;	
			      }
			    }
			  }
			});
		}    	
	<?php elseif($use_js == 'uploadImageSeatJs'): ?>
		function readURL(input) {
        	if (input.files && input.files[0]) {
	            var reader = new FileReader();
	            reader.onload = function (e) {
	                $('#target').attr('src', e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);
	        }
	    }
    	$("#fileUpload").change(function(){
	        readURL(this);
	    });
	    $('#select-img').click(function(e){
	    	$('#fileUpload').trigger('click');

	    }).css('cursor', 'pointer');
	<?php elseif($use_js == 'discountsnewJs'): ?>

		$('#discounts-tbl').rTable({
			loadFrom	: 	 'settings/get_discounts',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('settings/receipt_discount_form');
							 },	
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									$('#edit-'+id+'-'+bcode).click(function(){
										goTo('settings/receipt_discount_form/'+id+'/'+bcode);
									});
								});
								var table = $('#discounts-tbl');
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
	<?php elseif($use_js == 'promosnewJs'): ?>

		$('#promos-tbl').rTable({
			loadFrom	: 	 'settings/get_promos',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('settings/promos_form');
							 },	
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									$('#edit-'+id+'-'+bcode).click(function(){
										goTo('settings/promos_form/'+id+'/'+bcode);
									});
								});
								var table = $('#promos-tbl');
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
	<?php elseif($use_js == 'discformJs'): ?>
		$('#disc_code').keyup(function() {
			var disc_code = $('#disc_code').val();
    		if (this.value.match(/[^a-zA-Z0-9_]/g)) {
        		$('#disc_code').val("");
				rMsg("No Special Characters please.",'error');
		    }else{
		    	$('#save-btn').removeAttr('disabled');
		    }
   		});

		// $('#disc_name').keyup(function() {
		// 	var disc_name = $('#disc_name').val();
  //   		if (this.value.match(/[^a-zA-Z0-9]/g)) {
  //       		$('#disc_name').val("");
		// 		rMsg("No Special Characters please.",'error');
		//     }else{
		//     	$('#save-btn').removeAttr('disabled');
		//     }
  //  		});

		$('select[name^=b_code]').multiSelect();
		$('select[name^=bcode]').multiSelect();
		$('#save-menu').click(function(){
			// var branch = $('select[name^=b_code]').val();
			var branch = $('select[name^=b_code]').val();
			if(branch == null) { 
				rMsg("Error! Branch must not be empty.",'error');
				return false;
			}
			$("#receipt_discount_form").rOkay({
				btn_load		: 	$('#save-menu'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										goTo('settings/discounts_new');
										// console.log(data);
										// alert(data);

										// if(data.act == "update"){
										// 	window.location = baseUrl+'menu/';
										// }
										// else{
										// 	window.location = baseUrl+'menu/form/';
										// }
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
		});	<?php elseif($use_js == 'promosformJs'): ?>
		$('select[name^=b_code]').multiSelect();
		$('select[name^=bcode]').multiSelect();
		$('#save-menu').click(function(){
			// var branch = $('select[name^=b_code]').val();
			var branch = $('select[name^=b_code]').val();
			if(branch == null) { 
				rMsg("Error! Branch must not be empty.",'error');
				return false;
			}
			$("#receipt_discount_form").rOkay({
				btn_load		: 	$('#save-menu'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										goTo('settings/promos_new');
										// console.log(data);
										// alert(data);

										// if(data.act == "update"){
										// 	window.location = baseUrl+'menu/';
										// }
										// else{
										// 	window.location = baseUrl+'menu/form/';
										// }
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

	<?php elseif($use_js == 'paymentsListFormJs'): ?>
		var tbl_ref = $('table#payments-tbl').attr('ref');
		if(tbl_ref == 1){
			tref = true;
		}else{
			tref = false;
		}
		$('#payments-tbl').rTable({
			loadFrom	: 	 'settings/get_payments',
			noEdit		: 	 true,
			noAdd		: 	 tref,
			add			: 	 function(){
								goTo('settings/pay_form');
							 },				 	
			edit		: 	 function(id){
								goTo('settings/pay_form/'+id);
							 },				 	
			afterLoad 	: 	 function(){
								var table = $('#modifiers-tbl');
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
								// $('#modifiers-tbl').dataTable();
							 }	
		});

	<?php elseif($use_js == 'payFormJs'): ?>
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var pay_id = $('#pay_id').val();
			// alert(pay_id);

			if(pay_id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			// alert(loadUrl+pay_id);
			$(tabPane).rLoad({url:baseUrl+loadUrl+pay_id});
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
	// alert('zxczxc');
		$('#save-pay').click(function(){
			$("#details_form").rOkay({
				btn_load		: 	$('#save-pay'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(typeof data.msg != 'undefined' ){
											$('#form_pay_id').val(data.id);
											// $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
											disEnbleTabs('.load-tab',true);
											rMsg(data.msg,'success');
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

	<?php elseif($use_js == 'inputFieldLoadJs'): ?>
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
		// 		$('#item-search').val('');
		// 		add_to_group(v,k);
		// 	}
		// });
		$('#add-field').click(function(){
			var field_name = $('#field_name').val();
			var payment_id = $('#payment_id').val();
			if(field_name == ""){
				rMsg('Field name should not be empty.','error');
			}else{
				add_to_group(payment_id,field_name);
			}
			return false;
		});
		$('.del').each(function(){
			var id = $(this).attr('ref');
			remove_row(id);
		});
		$('.dflt').each(function(){
			var id = $(this).attr('ref');
			make_dflt_row(id);
		});
		// $('#item-search').keyup(function(e){
		// 	if(e.keyCode == '13'){
		// 		$(this).val("");
		// 	}
		// });
		function add_to_group(payment_id,field_name){
			// var mod_id = id;
			// var mod_group_id = $('#mod_group_id').val();
			// var field_name = field_name;
			// var field_name = field_name;
			$.post(baseUrl+'settings/input_fields_db','payment_id='+payment_id+'&field_name='+field_name,function(data){
				if(data.act == 'add'){
					$('#modifier-list').append(data.li);
					rMsg(data.msg,'success');
					// $('#item-search').selectpicker('deselectAll');
					$("#field_name").val('');
				}
				else{
					var i = $('#li-'+data.id);
					$('#li-'+data.id).remove();
					$('#modifier-list').append(data.li);
					rMsg(data.msg,'success');
				}
				remove_row(data.id);
				make_dflt_row(data.id);
			},'json');
		}
		function remove_row(id){
			$('#del-'+id).click(function(){
				$.post(baseUrl+'mods/remove_group_modifier','group_mod_id='+id,function(data){
					$('#li-'+id).remove();
					rMsg(data.msg,'warning');
				},'json');
				return false;
			});
		}
		function make_dflt_row(id){
			$('#dflt_'+id).click(function(){
				var checked = $(this).is(":checked");
				if(checked){
					var ch = 0;
				}
				else{
					var ch = 1;
				}
				$.post(baseUrl+'settings/inactive_input_filed','field_id='+id+'&dflt='+ch,function(data){
					alert(JSON.stringify(data));
					rMsg(data.msg,'success');
				},'json');
			});
		} 

	<?php elseif($use_js == 'paymentsGroupListFormJs'): ?>
		var tbl_ref = $('table#paymentgroup-tbl').attr('ref');
		if(tbl_ref == 1){
			tref = true;
		}else{
			tref = false;
		}
		$('#paymentgroup-tbl').rTable({
			loadFrom	: 	 'settings/get_payment_group',
			noEdit		: 	 true,
			noAdd		: 	 tref,
			add			: 	 function(){
								goTo('settings/payment_group_form');
							 },				 	
			edit		: 	 function(id){
								goTo('settings/payment_group_form/'+id);
							 },				 	
			afterLoad 	: 	 function(){
								var table = $('#modifiers-tbl');
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
								// $('#modifiers-tbl').dataTable();
							 }	
		});
	<?php elseif($use_js == 'paymentGroupLoadJs'): ?>
	// alert('zxczxc');
		$('#save-pay').click(function(){
			$("#details_form").rOkay({
				btn_load		: 	$('#save-pay'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(typeof data.msg != 'undefined' ){
											$('#payment_group_id').val(data.id);
											// $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
											disEnbleTabs('.load-tab',true);
											rMsg(data.msg,'success');
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

	<?php elseif($use_js == 'transTypeJs'): ?>

		$('#transaction_types-tbl').rTable({
			loadFrom	: 	 'settings/get_trans_types',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('settings/trans_form');
							 },	
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									// alert(id+'-'+bcode);
									$('#edit-'+id+'-'+bcode).click(function(){
										goTo('settings/trans_form/'+id+'/'+bcode);
									});
								});
								var table = $('#transaction_types-tbl');
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

	<?php elseif($use_js == 'transtypeFormJs'): ?>
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var trans_id = $('#trans_id').val();
			var branch = $('#branch').val();

			if(trans_id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			$(tabPane).rLoad({url:baseUrl+loadUrl+trans_id+'/'+branch});
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


	<?php elseif($use_js == 'transformJs'): ?>
	//alert('zxczxc');
		$('#save-trans').click(function(){
			$("#transform").rOkay({
				btn_load		: 	$('#save-trans'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
					// alert(data);
										if(typeof data.msg != 'undefined' ){
											// $('#trans_id').val(data.id);
											// // $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
											// disEnbleTabs('.load-tab',true);
											// // goTo('settings/trans_type');
											// rMsg(data.msg,'success');

											window.location = baseUrl+'settings/trans_type';

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

	<?php elseif($use_js == 'transCatJs'): ?>
		$('#add-trans-cat').click(function(){
			var trans_type = $('#trans_type').val();
			
			// $('#mod-group-id-hid').val(grp_id);
			$('#trans-cat-form').rOkay({
				btn_load	         : $('#add-trans-cat'),
				btn_load_remove 	 : true,
				asJson            	 : true,
				onComplete        	 : function(data) {
											if (data.result == 'success') {
												$('#trans-cat-details-tbl').append(data.row);
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

		// function remove_row(id){
		// 	alert('wawa');
		// 	$('#trans-cat-del-'+id).click(function(){
		// 		// $.post(baseUrl+'mods/remove_mod_sub_price','id='+id,function(data){
		// 		// 	$('#modsub-price-del-row-'+id).remove();
		// 		// 	rMsg(data.msg,'success');
		// 		// },'json');
		// 		return false;
		// 	});
		// }
		$(document).on('click','.del-trans-cat',function(event)
		{	
			// alert('wewe');
			// event.preventDefault();
			var id = $(this).attr('ref');
			var branch = $(this).attr('branch_code');
			// alert(branch);
			$.post(baseUrl+'settings/remove_trans_cat','id='+id+'&branch_code='+branch,function(data){
				$('tr#trans-cat-row-'+id+'-'+branch).remove();
				rMsg(data.msg,'success');
			},'json');
		});
	<?php elseif($use_js == 'transMenuJs'): ?>
		$('#add-trans-menu').click(function(){
			var trans_type = $('#trans_type').val();
			
			// $('#mod-group-id-hid').val(grp_id);
			$('#trans-menu-form').rOkay({
				btn_load	         : $('#add-trans-menu'),
				btn_load_remove 	 : true,
				asJson            	 : true,
				onComplete        	 : function(data) {
											if (data.result == 'success') {
												$('#trans-menu-details-tbl').append(data.row);
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
		$(document).on('click','.del-trans-menu',function(event)
		{	
			// alert('wewe');
			// event.preventDefault();
			var id = $(this).attr('ref');
			var branch = $(this).attr('branch_code');
			$.post(baseUrl+'settings/remove_trans_menu','id='+id+'&branch_code='+branch,function(data){
				// alert(data);
				$('tr#trans-menu-row-'+id).remove();
				rMsg(data.msg,'success');
			},'json');
			// });
		});
	
	<?php endif; ?>

});
</script>