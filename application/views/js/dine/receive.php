<script>
$(document).ready(function(){        
	<?php if($use_js == 'receiveListJs'): ?>
		$('#main-tbl').rTable({
			loadFrom	: 	 'receiving/get_receiving',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('receiving/form');
							 },
			afterLoad 	: 	 function(){
								$('.void').each(function(){
									$(this).click(function(){
										var id = $(this).attr('ref');
										var title = $(this).attr('title');
										$.rPopForm({
											loadUrl			:	"trans/void_form/"+id,
											passTo			:	"receiving/void/"+id,
											title			:	title,
											rform			:	"main-form",
											onComplete		:	function(response){
																	window.location.reload();
																},
										});
										return false;
									});
								});
								var table = $('#main-tbl');
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
								// $('#main-tbl').dataTable();
							 }
		});
	<?php elseif($use_js == 'receiveJs'): ?>
		$('#save-btn').click(function(){
			$("#receive_form").rOkay({
				btn_load		: 	$('#save-btn'),
				btn_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										if(data.error != ""){
											rMsg(data.msg,'error');
										}else{
											window.location.reload();
										}
									}
			});
			return false;
		});
		$('#back-btn').click(function(){
			goTo('receiving');
			return false;
		});	
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
		// 		set_item_details(v);
		// 	}
		// });
		$('#item-search').change(function(){
			var item = $(this).val();
			set_item_details(item);
		});
		function set_item_details(id){
			$.post(baseUrl+'receiving/get_item_details/'+id,function(data){
				$('#item-id').val(data.item_id);
				$('#item-uom').val(data.uom);
				$('#select-uom').find('option').remove();
				$.each(data.opts,function(key,val){
					$('#select-uom').append($("<option/>", {
				        value: val,
				        text: key
				    }));
				});
				$('#item-ppack').val(data.ppack);
				$('#item-ppack-uom').val(data.ppack_uom);
				$('#item-pcase').val(data.pcase);
			},'json');
		}
		$('#add-item-btn').click(function(){
			var noError = $('#add_item_form').rOkay({
				btn_load	: 	$('#add-item-btn'),
				goSubmit	: 	false,
				bnt_load_remove	: 	true
			});
			if(noError){
				var formData = $("#add_item_form").serialize();
				$.post(baseUrl+'wagon/add_to_wagon/rec_cart',formData,function(data){
					var row = data.items;
					var id = data.id;
					var tr = $("<tr/>").attr('id','row-'+id);
					$.each(row,function(key,val){
						if(key == 'item-search' || key == 'qty' || key == 'select-uom' || key == 'cost'){
							if(key == 'item-search'){
								var selectedText = $('#item-search').find("option:selected").text();
							    tr.append($("<td/>", {text: selectedText}));
							}	
							else if(key == 'qty' || key == 'cost'){
							    tr.append($("<td/>", {text: $.number(val,2)}));
							}	
							else if(key == 'select-uom'){
								var txt = splitUom(val);
								tr.append($("<td/>", {text: txt}));
							}
							else
								tr.append($("<td/>", {text: val}));
						}
					});
					var link = $('<a/>')
						.attr('id','del-'+id)
						.attr('class','del')
						.attr('href','#')
						.html('<i class="fa fa-trash-o fa-lg fa-fw"></i>');
					tr.append($("<td/>",{html:link}));
				    $('#details-tbl').append(tr);
				    $('#add_item_form').find("input[type=text], textarea").val("");
				    $("#item-search").val('').selectpicker('refresh');
				    $('#select-uom').find('option').remove();
				    deleteRow(id);
				    total();
				},'json');
			}
			return false;
		});
		function deleteRow(id){
			$('#del-'+id).click(function(){
				$.post(baseUrl+'wagon/delete_to_wagon/rec_cart/'+id,function(data){
					$('#row-'+id).remove();
					total();
				},'json');
				return false;
			});
		}
		function total(){
			$.post(baseUrl+'wagon/get_wagon/rec_cart/',function(data){
				var items = data.items;
				var count = items.length;
				if(count > 0){
					$('.no-items-row').hide();
					var total = 0;
					$.each(items,function(line_id,row){
						total += parseFloat(row.qty) * parseFloat(row.cost);
					});
					$('#grand-total').number(total,2);
				}
				else{
					$('.no-items-row').show();
					$('#grand-total').number(0,2);
				}				
			},'json').fail( function(xhr, textStatus, errorThrown) {
			   alert(xhr.responseText);
			});	
		}
		function splitUom(txt){
			var txt = txt.split('-');
			var line = "";
			if(1 in txt){
				if(txt[1] == 'pack'){					
					line = $("#item-ppack-uom").val()+'(@'+txt[2]+' '+txt[0]+')';
				}
				else{
					line = 'Case(@'+txt[2]+' Packs)';
				}
			}
			else
				line = txt[0];
			return line;
		}
	<?php endif; ?>
});
</script>