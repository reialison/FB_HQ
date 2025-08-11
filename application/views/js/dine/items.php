<script>
$(document).ready(function(){
	<?php if($use_js == 'listFormJs'): ?>
		$('#items-tbl').rTable({
			loadFrom	: 	 'items/get_items',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('items/setup');
							 },				 	
			edit		: 	 function(id){
				// console.log(id);
								goTo('items/setup/'+id);
							 },
			dataExport 		:   true,
			afterLoad 	: 	 function(){
								var table = $('#items-tbl');
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
								//$('#items-tbl').dataTable();
							 }				 					 	
		});
	<?php elseif($use_js == 'itemFormContainerJs'): ?>
		$('.tab_link').click(function(event){
			event.preventDefault();
			var id = $(this).attr('id');
			loader('#'+id);
		});
		loader('#details_link');
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			console.log(loadUrl);
			var tabPane = $(btn).attr('href');
			var selected = $('#item_idx').val();
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
			var item_id = $('#item_idx').val();
			var bcode = $('#bcode').val();
			$(tabPane).rLoad({url:baseUrl+loadUrl+'/'+item_id+'/'+bcode});
		}
		function disableTabs(id,enable){
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
	<?php elseif($use_js == 'itemDetailsJs'): ?>
		$('#save-btn').click(function(){
			$("#item_details_form").rOkay({
				btn_load		: 	$('#save-btn'),
				btn_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.error != "")
											rMsg(data.error,'error');
										else
											location.reload();
									}
			});
			return false;
		});
		$('#cat_id').rAddOpt({
			text 	: 'Add New Category',
			loadUrl : 'settings/category_form',
			passTo 	: 'settings/category_db',
			form 	: 'category_form'
		});
		$('#subcat_id').rAddOpt({
			text 	: 'Add New Subcategory',
			loadUrl : 'settings/subcategory_form',
			passTo 	: 'settings/subcategory_db',
			form 	: 'subcategory_form'
		});
		$('#supplier_id').rAddOpt({
			text 	: 'Add New Supplier',
			loadUrl : 'settings/supplier_form',
			passTo 	: 'settings/supplier_db',
			form 	: 'supplier_form'
		});
		// $('#cat_id').on('change',function()
		// {
		// 	var cat_id = $(this).val();
		// 	var passUrl = baseUrl + 'items/get_subcategories/' + cat_id;

		// 	$('#subcat_id').empty();
		// 	$.post(passUrl,'',function(data)
		// 	{
		// 		str = '';
		// 		$.each(data,function(key,value)
		// 		{
		// 			str = str + "<option value='" + key + "'>" + value + "</option>";
		// 		});
		// 		$('#subcat_id').append(str);
		// 	},'json');
		// });
		// $('#cat_id').change();
		changeUOM();
		$('#uom').change(function(){
			changeUOM();
			$('#no_per_pack').val('');
			$('#no_per_pack_uom').val('');
		});
		function changeUOM(){
			var uom = $('#uom').val();
			if(uom != ""){
				$('#uom-txt').text(uom);
			}	
			else{
				$('#uom-txt').text('');
			}
		}
	<?php elseif($use_js == 'inventoryJS'): ?>
		$('#branch_id').change(function(){
			var bcode =$("#branch_id").val();
			var qty_on_hand =$("#qty_on_hand").val();
		    $.ajax({
		        url: "inventories",
		        data: { "branch_id": bcode  },
		        dataType:"html",
		        type: "post",
		        success: function(data){
			// alert(data);
				if(qty_on_hand == 'item'){
		        	window.location = baseUrl+'items/inventories/'+bcode;
		        }else{
		        	window.location = baseUrl+'items/menu_inventories/'+bcode;

		        }
		        	// window.reload();
		           // $('#artist').append(data);
		        }
		    });
		});

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
	<?php elseif($use_js == 'invMoveJS'): ?>
		// load_table();
		$('#search-btn').click(function(){

			load_table();
			return false;
		});
		var doc = new jsPDF();
		var specialElementHandlers = {
		    '#editor': function (element, renderer) {
		        return true;
		    }
		};
		$('#print-btn').click(function(){
			// doc.fromHTML($('#general-div').html(), 15, 15, {
		 //        'width': 170,
	  //           'elementHandlers': specialElementHandlers
		 //    });
		 //    doc.save('inv_move.pdf');

		 	var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_id='+$("#branch_id").val()
							+'&item-search='+$("#item-search").val();


			// alert(formData);


		 	// goTo('items/menu_moves_pdf?'+formData);
		 	window.open(baseUrl+"items/inv_moves_pdf?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			return false;


			return false;
		});

		function load_table(){
			
			var st = $('#item-search').find("option:selected").text();
			if(st == ""){
				rMsg('Please select a component first.','error');
			}else{

				var noError = $('#general-form').rOkay({
					goSubmit	: 	false,
				});
				if(noError){ 
					var getUrl = $('#general-form').attr('action');
					var formData = $('#general-form').serialize();
					$('body').goLoad2();
					$('#general-tbl tbody').html('');
					$.post(baseUrl+getUrl,formData,function(data){
						$('#general-tbl tbody').html(data.html);
						$('body').goLoad2({load:false});
					},'json').fail( function(xhr, textStatus, errorThrown) {
					 	console.log(xhr.responseText);
						$('body').goLoad2({load:false});
					});
				}

			}
		}
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
	<?php elseif($use_js == 'menuMoveJS'): ?>
		// load_table();
		$('#search-btn').click(function(){
			load_table();
			return false;
		});
		var doc = new jsPDF();
		var specialElementHandlers = {
		    '#editor': function (element, renderer) {
		        return true;
		    }
		};
		$('#print-btn').click(function(){
			// doc.fromHTML($('#general-div').html(), 15, 15, {
		 //        'width': 170,
	  //           'elementHandlers': specialElementHandlers
		 //    });
		 //    doc.save('inv_move.pdf');
		 	var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_id='+$("#branch_id").val()
							+'&menu_id='+$("#menu-search").val();


		 	// goTo('items/menu_moves_pdf?'+formData);
		 	window.open(baseUrl+"items/menu_moves_pdf?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			return false;
		});

		function load_table(){

			// var st = $('#menu-search').find("option:selected").text();
			// if(st == ""){
			// 	rMsg('Please select a menu first.','error');
			// }else{
				var noError = $('#general-form').rOkay({
					goSubmit	: 	false,
				});
				if(noError){
					var getUrl = $('#general-form').attr('action');
					var formData = $('#general-form').serialize();
					$('body').goLoad2();
					$('#general-tbl tbody').html('');
					$.post(baseUrl+getUrl,formData,function(data){
						// alert(data);
						// console.log(data);
						$('#general-tbl tbody').html(data.html);
						$('body').goLoad2({load:false});
					},'json').fail( function(xhr, textStatus, errorThrown) {
					 	// alert(xhr.responseText);
					 	// console.log(xhr.responseText);
						$('body').goLoad2({load:false});
					});
				}
			// }
		}
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});

	<?php endif; ?>
});
</script>