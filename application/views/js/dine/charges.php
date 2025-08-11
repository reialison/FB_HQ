<script>
$(document).ready(function(){
	<?php if($use_js == 'chargesJs'): ?>

	<?php elseif($use_js == 'chargesnewJs'): ?>

		$('#charges-tbl').rTable({
			loadFrom	: 	 'charges/get_charges',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('charges/charges_form');
							 },	
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									// alert(id+'-'+bcode);
									$('#edit-'+id+'-'+bcode).click(function(){
										goTo('charges/charges_form/'+id+'/'+bcode);
									});
								});
								var table = $('#charges-tbl');
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

	<?php elseif($use_js == 'chargeformJs'): ?>
		$('#charge_code').keyup(function() {
			var charge_code = $('#charge_code').val();
    		if (this.value.match(/[^a-zA-Z0-9_]/g)) {
        		$('#charge_code').val("");
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
			$("#charges_form").rOkay({
				btn_load		: 	$('#save-menu'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										goTo('charges/charges_new');
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
		<?php elseif($use_js == 'promosformJs'): ?>
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

	<?php endif; ?>
});
</script>