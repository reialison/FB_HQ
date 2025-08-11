<script>
$(document).ready(function(){
	<?php if($use_js == 'rolesJs'): ?>
		$(".check").click(function(){
			var id = $(this).attr('id');
			var ch = false
			if($(this).is(':checked'))
				var ch = true;
			$('.'+id).prop('checked',ch);

			var parent = $(this).attr('parent');
			if (typeof parent !== 'undefined' && parent !== false) {
			   parentCheck(ch,parent); 
			}

			// var classList = $(this).attr('class').split(/\s+/);
			// var chk = $(this);
			
			// $.each( classList, function(key, parent){
			// });
		});

		function parentCheck(ch,parent){
			if(parent != "check"){
				var par = $('#'+parent);
				if(!ch){
					var ctr = 0;
					$('.'+parent).each(function(){
						if($(this).is(':checked'))
							ctr ++;
					});
					if(ctr == 0)
						par.prop('checked',ch)
				}
				else
					par.prop('checked',ch);
				
				var parentParent = par.attr('parent');
				if (typeof parentParent !== 'undefined' && parentParent !== false) {
					parentCheck(ch,parentParent);	
				}

			}
		}
	<?php elseif($use_js == 'usersListJs'): ?>
		$('#users-tbl').rTable({
			loadFrom	: 	 'user/get_users',
			add			: 	 function(){goTo('user/users_form')},
			edit		: 	 function(id){goTo('user/users_form/'+id);},
			afterLoad 	    : 	 function(){
								var table = $('#users-tbl');
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
								// $('#users-tbl').dataTable();
				 }						 	
		});
	<?php elseif($use_js == 'userFormJs'): ?>	
		// $('#save-btn').click(function(){
		// 	$('#users_form').rOkay({
		// 		asJson				: 	false,
		// 		bnt_load_remove		: 	false,
		// 		btn_load			: 	$(this),
		// 		onComplete			: 	function(data){
		// 									goTo('user');
		// 								}
		// 	});
		// 	return false;
		// });	
		$('#save-btn').click(function(){
			var btn = $(this);
			var noError = $('#users_form').rOkay({
    			btn_load		: 	btn,
				bnt_load_remove	: 	true,
				goSubmit		: 	false,
    		});
    		
    		if(noError){
    			btn.goLoad();
    			$("#users_form").submit(function(e){
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
			    			// console.log(data);
			    			// alert(data);
			    			if(data.act == "error"){
			    				rMsg(data.msg,'error');
		    					btn.goLoad({load:false});
			    			}
			    			else{
								goTo('user');
			    			}	
				        },
				        error: function(jqXHR, textStatus, errorThrown){
							alert(jqXHR.responseText);
				        }         
				    });
				    e.preventDefault();
				//     e.unbind();
				});
				$("#users_form").submit();
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
	<?php elseif($use_js == 'restartJs'): ?>
		  $('#restart-pos').click(function(){
			  $.callManager({
			  	success : function(){
			  		$('#restart-pos').goLoad2();
			  		$.post(baseUrl+'admin/go_restart',function(data){
				  		$('#restart-pos').goLoad2({load:false});
			  			window.location = baseUrl;
			  		});
			  	}
			  });
			  return false;
		  });
	<?php elseif($use_js == 'uploadFormJs'): ?>	
		// $('#save-btn').click(function(){
		// 	$('#users_form').rOkay({
		// 		asJson				: 	false,
		// 		bnt_load_remove		: 	false,
		// 		btn_load			: 	$(this),
		// 		onComplete			: 	function(data){
		// 									goTo('user');
		// 								}
		// 	});
		// 	return false;
		// });	
		$('#upload-btn').click(function(){
			var btn = $(this);
			var noError = $('#upload_form').rOkay({
    			btn_load		: 	btn,
				bnt_load_remove	: 	true,
				goSubmit		: 	false,
    		});
    		
    		if(noError){
    			btn.goLoad();
    			$("#upload_form").submit(function(e){
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
			    			// console.log(data);
			    			// alert(data);
			    			if(data.act == "error"){
			    				rMsg(data.msg,'error');
		    					btn.goLoad({load:false});
			    			}
			    			else{
								goTo('admin/import_file');
			    			}	
				        },
				        error: function(jqXHR, textStatus, errorThrown){
							alert(jqXHR.responseText);
				        }         
				    });
				    e.preventDefault();
				    e.unbind();
				});
				$("#upload_form").submit();
    		}
    		return false;
		});

	<?php elseif($use_js == 'exportJS'): ?>	
		$(".timepicker").timepicker({
		    showInputs: false
		});

		$('#gen-rep').click(function(){
			$obj = $(this);
			$obj.goLoad2({load:true});
			branch = $('#branch_id').val();
			// alert(branch);
			formData = 'branch='+branch;
			$.post(baseUrl+'admin/download_trans_for_pos',formData,function(data){
		  		$obj.goLoad2({load:false});
	  			// window.location = baseUrl+ "admin/export_data";
	  			window.location = baseUrl+data.file;
	  		},'json');


			// window.location.href = baseUrl + "admin/download_trans_for_pos?branch="+branch;
	    	// return false;

		});



	
	<?php endif; ?>
});
</script>