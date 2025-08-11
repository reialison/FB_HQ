<script>
$(document).ready(function(){
	<?php if($use_js == 'detailsJs'): ?>
		$(".timepicker").timepicker({
            showInputs: false
        });
		$('#save-btn').click(function(event){
			event.preventDefault();
			// $("#details_form").rOkay({
			// 	btn_load		: 	$('#save-btn'),
			// 	bnt_load_remove	: 	true,
			// 	asJson			: 	false,
			// 	onComplete		:	function(data){
			// 							alert(data);
			// 							rMsg(data.msg,'success');
			// 						}
			// });
			var formData = $('#details_form').serialize();
			var dtype = 'json';
			$.post(baseUrl+'setup/details_db',formData,function(data)
			{
				// alert(data);
				rMsg(data.msg,'success');
			},'json');
			// });
			// alert(formData);

		// 	$.ajax({
		//         url: baseUrl+'setup/details_db',
		//         type: 'POST',
		//         data:  formData,
		//         dataType:  dtype,
		//         mimeType:"multipart/form-data",
		//         contentType: false,
		//         cache: false,
		//         processData:false,
		//         success: function(data, textStatus, jqXHR){
		// 			// alert(data);
		// //          	settings.onComplete.call(this,data);
		// 				rMsg(data.msg,'success');
		//         },
		//         error: function(jqXHR, textStatus, errorThrown){
		// 			console.log(jqXHR);
		// 			console.log(textStatus);
		// 			console.log(errorThrown);
		//         }         
		//     });
			return false;
		});

		$('#save-pos-btn').click(function(event){
			$("#settings_form").rOkay({
				btn_load		: 	$('#save-pos-btn'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										rMsg(data.msg,'success');
									}
			});
			return false;
		});
		$('#save-db-btn').click(function(event){
			$("#database_form").rOkay({
				btn_load		: 	$('#save-db-btn'),
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										rMsg(data.msg,'success');
									}
			});
			return false;
		});
		$('#backup-db-btn').click(function(event){
			// $(this).goLoad2();
			window.location = baseUrl+'setup/download_backup_db';
			return false;
		});
		$('#warning-db-button').click(function(event){
			// $(this).goLoad2();
			// window.location = baseUrl+'setup/download_backup_db';
			// return false;
			swal({
			  title: "Are you sure you want to consolidate the data to Master DB?",
			  text: "CHANGES ARE IRREVERSIBLE",
			  type: "warning",
			  showCancelButton: true,
			  cancelButtonText: 'No',
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "YES, PROCEED",
			  closeOnConfirm: false,
			  showLoaderOnConfirm: true
			},
			function(){
				// swal('',)
				$.post(baseUrl+'site/execute_migration',{'ajax':true},function(e){
					console.log(e);
					if(e){
						 swal("Consolidation has been successfully executed!",'','success');
					}
			 // 		 swal("Deleted!", "Your imaginary file has been deleted.", "success");

				});
			});
		});

		$('#target').click(function(e){
	    	$('#complogo').trigger('click');
	    }).css('cursor', 'pointer');

	    $('#upload-splsh-img').rPopFormFile({
	    // $('#upload-splsh-img').rPopForm({
	    	asJson	  : true,
	    	hide	  : true,
	    	onComplete: function(data){
	    		if(data.msg == "Image uploaded"){
		    		// rMsg(data.msg,'success');
		    		location.reload();
	    		}
		    	else{
		    		rMsg(data.msg,'error');
		    	}
	    	}
	    });
	    $('.del-spl-btn').click(function(){
	    	var img_id = $(this).attr('ref');
	    	var img = $(this);
	    	$.post(baseUrl+'setup/delete_splash_img/'+img_id,function(data){
	    		if(data.error == ""){
		    		img.parent().remove();
	    			rMsg('Image removed.','success');
	    		}
	    		else{
	    			rMsg(data.error,'error');
	    		}
	    	},'json');
	    	// alert(data);
	    	// });
	    	return false;
	    });
	<?php elseif($use_js == 'referencesJs'): ?>
		// alert('asd');
		$('.save_btn').click(function(){
			var type_id = $(this).attr('ref');
			var name = $(this).attr('label');
			var next_ref = $('#type-'+type_id).val();
			var formData = 'type_id='+type_id+'&next_ref='+next_ref+'&name='+name;

			// alert(formData);

			$.post(baseUrl+'settings/references_db', formData, function(data){
				rMsg(data.msg,'success');
			}, 'json');

			// $.post(baseUrl+'settings/references_db', formData, function(data){
				// alert(data);
				// // rMsg(data.msg,'success');
			// });

			return false;
		});
	<?php elseif($use_js == 'uploadSplashImagePopJs'): ?>
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

	<?php elseif($use_js == 'brandJs'): ?>

		$('#brands-tbl').rTable({
			loadFrom	: 	 'setup/get_brands',
			noEdit		: 	 true,
			add			: 	 function(){
								goTo('setup/brands_form');
							 },	
			afterLoad	:    function(){
								$('.edit').each(function(){
									var id = $(this).attr('ref');
									var bcode = $(this).attr('ref2');
									// alert(id+'-'+bcode);
									$('#edit-'+id+'-'+bcode).click(function(){
										goTo('setup/brands_form/'+id+'/'+bcode);
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

	<?php elseif($use_js == 'brandformJs'): ?>
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
										goTo('setup/brands');
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