<script>
$(document).ready(function(){
	<?php if($use_js == 'modFormJs'): ?>
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var mod_id = $('#mod_id').val();
			var bcode = $('#bcode').val();
			if(mod_id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			$(tabPane).rLoad({url:baseUrl+loadUrl+'/'+mod_id+'/'+bcode});
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
	<?php elseif($use_js == 'modsListFormJs'): ?>
		$('#modifiers-tbl').rTable({
			loadFrom	: 	 'mods/get_modifiers',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('mods/form');
							 },				 	
			edit		: 	 function(id){
								goTo('mods/form/'+id);
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
	<?php elseif($use_js == 'modGroupssListFormJs'): ?>
		$('#modifier_groups-tbl').rTable({
			loadFrom	: 	 'mods/get_modifier_groups',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('mods/group_form');
							 },				 	
			edit		: 	 function(id){
								goTo('mods/group_form/'+id);
							 },
			afterLoad 	: 	 function(){
								var table = $('#modifier_groups-tbl');
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
								// $('#modifier_groups-tbl').dataTable();
							 }					 	
		});
	<?php elseif($use_js == 'detailsLoadJs'): ?>
	//alert('zxczxc');
		$('#save-mod').click(function(){
			$("#details_form").rOkay({
				btn_load		: 	$('#save-mod'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(typeof data.msg != 'undefined' ){
											$('#mod_id').val(data.id);
											// $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
											disEnbleTabs('.load-tab',true);
											goTo('mods');
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
	<?php elseif($use_js == 'recipeLoadJs'): ?>
		$('#add-item-btn').click(function(){
			$("#recipe_form").rOkay({
				btn_load		: 	$('#add-item-btn'),
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										if(data.act == 'add'){
											$('#details-tbl').append(data.row);
											rMsg(data.msg,'success');
										}
										else{
											var i = $('#row-'+data.id);
											$('#row-'+data.id).remove();
											$('#details-tbl').append(data.row);
											rMsg(data.msg,'success');
										}
										get_recipe_total();
										remove_row(data.id);
									}
			});
			return false;
		});
		$('#item-search').typeaheadmap({
			"source": function(search, process) {
				var url = $('#item-search').attr('search-url');
				var branch = $('#branch_code').val();
				var formData = 'search='+search+'&branch_code='+branch;
				$.post(baseUrl+url,formData,function(data){
					 // console.log(data);
					// alert(data);
					process(data);
				},'json');
			},
		    "key": "key",
		    "value": "value",
		    "listener": function(k, v) {
				$('#item-id-hid').val(v);
				get_item_details(v);
			}
		});
		$('.dels').each(function(){
			var id = $(this).attr('ref');
			remove_row(id);
		});
		$('#override-price').click(function(){
			var cid = $('#mod_id').val();
			if(cid != ""){
				var total = $('#total').val();
				$.post(baseUrl+'mods/update_modifier_price','mod_id='+cid+'&total='+total,function(data){
					rMsg('Selling Price Updated.','success');
				});
			}
			return false;
		});
		function get_item_details(v){
			// console.log(v);
			// alert(v);
			var branch = $('#branch_code').val();
			$.post(baseUrl+'mods/get_item_details/'+v+'/'+branch,function(data){
				$('#item-cost').val(data.cost);
				$('#item-uom-hid').val(data.uom);
				$('#uom-txt').text(data.uom);
				$('#qty').focus();
			},'json');
		}
		function remove_row(id){
			$('#del-'+id).click(function(){
				$.post(baseUrl+'mods/remove_recipe_item','mod_recipe_id='+id,function(data){
					$('#row-'+id).remove();
					rMsg(data.msg,'warning');
					get_recipe_total();
				},'json');
				return false;
			});
		}
		function get_recipe_total(){
			var cid = $('#mod_id').val();
			var branch = $('#branch_code').val();
			// alert(branch);
			if(cid != ""){
				$.post(baseUrl+'mods/get_recipe_total','mod_id='+cid,function(data){
					// alert(data);
					// console.log(data);
					$('#total').val(data.total);
				// });
				},'json');
			}
		}
	<?php elseif($use_js == 'modGroupFormJs'): ?>
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var mod_group_id = $('#mod_group_id').val();
			var bcode = $('#bcode').val();
			if(mod_group_id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			$(tabPane).rLoad({url:baseUrl+loadUrl+'/'+mod_group_id+'/'+bcode});
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
	<?php elseif($use_js == 'groupDetailsLoadJs'): ?>
		$('#save-grp').click(function(){
			$("#details_form").rOkay({
				btn_load		: 	$('#save-grp'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										if(typeof data.msg != 'undefined' ){
											$('#mod_group_id').val(data.id);
											// $('#details').rLoad({url:baseUrl+'branches/details_load/'+sel+'/'+res_id});
											disEnbleTabs('.load-tab',true);
												rMsg(data.msg,'success');
											setTimeout(function() {
								                
												goTo('mods/groups');
								            }, 1000);
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
	<?php elseif($use_js == 'groupRecipeLoadJs'): ?>
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
		$('#add-modifier').click(function(){
			App.blockUI(), window.setTimeout(function() {                    
                   // App.unblockUI()
                    //$("body").removeClass("modal-backdrop");   
                }, 2e3)
			$("body").addClass("modal-backdrop fade in");
			var mod = $('#item-search').val();
			var branch_code = $('select[name^=b_code]').val();
			// console.log(branch_code);
			var mod_txt = $('#item-search').find("option:selected").text();
			add_to_group(mod,mod_txt,branch_code);
			return false;
		});
		$('.del').each(function(){
			var id = $(this).attr('ref');
			var branch = $(this).attr('ref2');
			var mod_id = $(this).attr('ref3');
			var mod_group_id = $('#mod_group_id').val();
			// alert(mod_id);
			remove_row(id,branch,mod_id,mod_group_id);
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
		function add_to_group(id,text,branch_code){
			var mod_id = id;
			var mod_group_id = $('#mod_group_id').val();
			var bcode = branch_code;
			var mod_text = text;
			$.post(baseUrl+'mods/group_modifiers_details_db','mod_group_id='+mod_group_id+'&mod_id='+mod_id+'&mod_text='+text+'&branch_code='+bcode,function(data){
				// console.log(data);
				if(data.act == 'add'){
					// console.log(data);
					$('#modifier-list').append(data.li);
					$.unblockUI();
					$("body").removeClass("modal-backdrop");
					rMsg(data.msg,'success');
					$("#item-search").val('').selectpicker('refresh');


					// $('#modifier-list').append(data.li);
					// $('#item-search').selectpicker('deselectAll');
				}
				else if(data.act == 'error'){
					// console.log(data);
					$.unblockUI();
					$("body").removeClass("modal-backdrop");
					rMsg(data.msg,'error');
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
			// },);
		}
		function remove_row(id,branch,mod_id,mod_group_id){
			$('#del-'+id).click(function(){
				// alert(mod_group_id);
				App.blockUI(), window.setTimeout(function() {                    
                   // App.unblockUI()
                    //$("body").removeClass("modal-backdrop");   
                }, 2e3)
				$.post(baseUrl+'mods/remove_group_modifier','group_mod_id='+id+'&mod_group_id='+mod_group_id,function(data){

					$('#li-'+id).remove();
					$('#li-'+id).remove();
					$.unblockUI();
					$("body").removeClass("modal-backdrop");
					rMsg(data.msg,'warning');
					// },);
					// goTo('mods/group_form/'+id+'/'+branch);
				},'json');
				return false;
			});
		}
		function make_dflt_row(id){
			$('#dflt_'+id).click(function(){
				var checked = $(this).is(":checked");
				if(checked){
					var ch = 1;
				}
				else{
					var ch = 0;
				}
				$.post(baseUrl+'mods/default_group_modifier','group_mod_id='+id+'&dflt='+ch,function(data){
					rMsg(data.msg,'success');
				},'json');
			});
		}
	<?php elseif($use_js == 'modPricesJs'): ?>
	
		$('#add-price').click(function(){
			var trans_type = $('#trans_type').val();
			
			// $('#mod-group-id-hid').val(grp_id);
			$('#mod-price-form').rOkay({
				btn_load	         : $('#add-price'),
				btn_load_remove 	 : true,
				asJson            	 : true,
				onComplete        	 : function(data) {
											if (data.result == 'success') {
												$('#modprice-details-tbl').append(data.row);
												rMsg(data.msg,'success');
												remove_row(data.id);
												// $('#item-search').selectpicker('val','');
											} else {
												rMsg(data.msg,'error');
											}
										}
			});			
			return false;
		});
		
		function remove_row(id){
			// alert('wawa');
			$('#del-'+id).click(function(){
				$.post(baseUrl+'mods/remove_mod_price','id='+id,function(data){
					$('#row-'+id).remove();
					rMsg(data.msg,'success');
				},'json');
				return false;
			});
		}
		// $('.del-item').click(function(event)
		$(document).on('click','.del-item',function(event)
		{	
			// alert('wewe');
			event.preventDefault();
			var id = $(this).attr('ref');
			$.post(baseUrl+'mods/remove_mod_price','id='+id,function(data){
				$('tr#row-'+id).remove();
				rMsg(data.msg,'success');
			},'json');
		});
	
	<?php elseif($use_js == 'modsubPricesJs'): ?>
		$('#add-modsub-price').click(function(){
				var trans_type = $('#trans_type').val();
				
				// $('#mod-group-id-hid').val(grp_id);
				$('#modsub-price-form').rOkay({
					btn_load	         : $('#add-modsub-price'),
					btn_load_remove 	 : true,
					asJson            	 : true,
					onComplete        	 : function(data) {
												if (data.result == 'success') {
													$('#modsub-price-details-tbl').append(data.row);
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

			function remove_row(id){
				// alert('wawa');
				$('#modsub-price-del-'+id).click(function(){
					$.post(baseUrl+'mods/remove_mod_sub_price','id='+id,function(data){
						$('#modsub-price-del-row-'+id).remove();
						rMsg(data.msg,'success');
					},'json');
					return false;
				});
			}
			// $('.del-modsub-price').click(function(event)
		<?php elseif($use_js == 'modsubLoadJs'): ?>
		// $('#modsub-price').rLoad({url:baseUrl+'mods/mod_sub_price_load/'+1+'/'+'yoyo'}); return false;
		$('#add-modsub-btn').click(function(){
			$("#mod_sub_form").rOkay({
				btn_load		: 	$('#add-modsub-btn'),
				asJson			: 	true,
				onComplete		:	function(data){
										if(data.act == 'add'){
											$('#modsub-details-tbl').append(data.row);
											rMsg(data.msg,'success');
									        $('input#name').val('');
									        $('input#cost').val('');
											var test = ul.getElementsByTagName("li")[4];
											$(test).removeClass("active");
										    $(test).addClass("active");
											remove_modsub_row(data.id);
										}
										else{
											var i = $('#row-'+data.id);
											$('#row-'+data.id).remove();
											$('#modsub-details-tbl').append(data.row);
											rMsg(data.msg,'success');
										}

										document.getElementById("mod_sub_form").reset();
										// get_recipe_total();
										// remove_row(data.id);
									}
			});
			return false;
		});
		
		$('.dels').each(function(){
			var id = $(this).attr('ref');
			remove_modsub_row(id);
		});
		// $('#override-price').click(function(){
		// 	var cid = $('#mod_id').val();
		// 	if(cid != ""){
		// 		var total = $('#total').val();
		// 		$.post(baseUrl+'mods/update_modifier_price','mod_id='+cid+'&total='+total,function(data){
		// 			rMsg('Selling Price Updated.','success');
		// 		});
		// 	}
		// 	return false;
		// });
		// function get_item_details(v){
		// 	$.post(baseUrl+'mods/get_item_details/'+v,function(data){
		// 		$('#item-cost').val(data.cost);
		// 		$('#item-uom-hid').val(data.uom);
		// 		$('#uom-txt').text(data.uom);
		// 		$('#qty').focus();
		// 	},'json');
		// }
		function remove_modsub_row(id){
			$('#del-'+id).click(function(){
				$.post(baseUrl+'mods/remove_mod_sub','mod_sub_id='+id,function(data){
					$('#row-'+id).remove();
					rMsg(data.msg,'warning');
				},'json');
				return false;
			});
		}
		
		
		$(document).on('click','.modsub-row',function() {
			var id = $(this).attr('ref');
			var modsub_name = $(this).attr('modsub-name');

			// $('.modsub-row').removeClass('active');
			$('.modsub-row > td').removeClass('active');
			// $(this).addClass('active').css('background-color','#017ebc').css('color','#fff');
			// $(this).css('background-color','#017ebc').css('color','#fff');

			// $('.modsub-row').addClass('active');
			$(this).children('td').addClass('active');

			// $.ajaxSetup({async: false});
   //      	$.post(baseUrl+'mods/mod_sub_price_load',{'mod_sub_id':id,'mod_sub_name':modsub_name},function(data){
   //      		// alert(data);
			// 		$('#modsub-price').html(data);
			// });
			
			$('#modsub-price').rLoad({url:baseUrl+'mods/mod_sub_price_load/'+id});
			return false;
   		 });


		$(document).on('click','.del-modsub-price',function()
		{	
			// alert('wewe');
			// event.preventDefault();
			var id = $(this).attr('ref');
			$.post(baseUrl+'mods/remove_mod_sub_price','id='+id,function(data){
				$('tr#modsub-price-row-'+id).remove();
				rMsg(data.msg ,'success');
			},'json');
		});	

	<?php endif; ?>
});
</script>