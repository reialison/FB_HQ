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
		loader('#details_link');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var user = $('#user').val();
			var id = $('#id').val();
			if(id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#details').addClass('active');
				$('#details_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			// $(tabPane).rLoad({url:baseUrl+loadUrl+'/'+user+'/'});
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
		$('#branches-search').typeaheadmap({
			"source": function(search, process) {
				var url = $('#branches-search').attr('search-url');
				// var branch = $('#branch_code').val();
				var formData = 'search='+search;
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
				get_branch_details(v);
			}
		});
		// alert('aw');
		$('#add-branch').click(function(){
			
			App.blockUI(), window.setTimeout(function() {                    
                   // App.unblockUI()
                    //$("body").removeClass("modal-backdrop");   
                }, 2e3)
			$("body").addClass("modal-backdrop fade in");
			var branch_id = $('#branches-search').val();
			// var branch_code = $('select[name^=b_code]').val();
			// console.log(branch_code);
			
			// alert (branches);
			var branch_txt = $('#branches-search').find("option:selected").text();
			add_branch(branch_id,branch_txt);
			return false;
		});
		$('.del').each(function(){
			var id = $(this).attr('ref');
			var branch = $(this).attr('ref2');
			var sysid = $(this).attr('ref3');
			// var mod_id = $(this).attr('ref3');
			// var mod_group_id = $('#mod_group_id').val();
			// alert(mod_id);
			// remove_row(id,branch,mod_id,mod_group_id);
			remove_row(id,branch,sysid);
		});
		// $('#item-search').keyup(function(e){
		// 	if(e.keyCode == '13'){
		// 		$(this).val("");
		// 	}
		// });
		function add_branch(branch_id,branch_txt){
			// var sys_user_id = sys_user_id;
			var sys_user_id = $('#sys_user_id').val();
			// var mod_group_id = $('#mod_group_id').val();
			var branch_id = branch_id;
			// var mod_text = text;
			$.post(baseUrl+'user/user_branches_db','sys_user_id='+sys_user_id+'&branch_id='+branch_id+'&branch_txt='+branch_txt,function(data){
				// console.log(data);
				if(data.act == 'add'){
					// console.log(data);
					$('#branch-list').append(data.li);
					$.unblockUI();
					$("body").removeClass("modal-backdrop");
					rMsg(data.msg,'success');
					$("#branches-search").val('').selectpicker('refresh');


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
					$('#branch-list').append(data.li);
					rMsg(data.msg,'success');
				}
				remove_row(data.id);
			},'json');
			// },);
		}
		// function remove_row(id,branch,mod_id,mod_group_id){
		function remove_row(id,branch,sysid){
			$('#del-'+id).click(function(){
				// alert(mod_group_id);
				App.blockUI(), window.setTimeout(function() {                    
                   // App.unblockUI()
                    //$("body").removeClass("modal-backdrop");   
                }, 2e3)
				$.post(baseUrl+'user/remove_user_branch','sysid='+sysid+'&branch_id='+branch,function(data){
					// console.log(data);
					// return false;
					$('#li-'+id).remove();
					$('#li-'+id).remove();
					$.unblockUI();
					$("body").removeClass("modal-backdrop");
					rMsg(data.msg,'warning');
					// },);
					// goTo('mods/group_form/'+id+'/'+branch);
				},'json');
				// return false;
			});
		}
		
		// $('.tab_link').click(function(){
		// 	var id = $(this).attr('id');
		// 	loader('#'+id);
		// });
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
	
	<?php elseif($use_js == 'detailsLoadJs'): ?>


	// alert('aw');
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

		<?php elseif($use_js == 'userBranchesLoadJs'): ?>
			loader('#branches');
		$('.tab_link').click(function(){
			var id = $(this).attr('id');
			loader('#'+id);
		});
		function loader(btn){
			var loadUrl = $(btn).attr('load');
			var tabPane = $(btn).attr('href');
			var user = $('#user').val();
			var id = $('#id').val();
			if(id == ""){
				disEnbleTabs('.load-tab',false);
				$('.tab-pane').removeClass('active');
				$('.tab_link').parent().removeClass('active');
				$('#branches').addClass('active');
				$('#branches_link').parent().addClass('active');
			}
			else{
				disEnbleTabs('.load-tab',true);
			}
			// $(tabPane).rLoad({url:baseUrl+loadUrl+'/'+user+'/'});
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
		// $('#add-modifier').click(function(){
		// 	App.blockUI(), window.setTimeout(function() {                    
        //            // App.unblockUI()
        //             //$("body").removeClass("modal-backdrop");   
        //         }, 2e3)
		// 	$("body").addClass("modal-backdrop fade in");
		// 	var mod = $('#item-search').val();
		// 	var branch_code = $('select[name^=b_code]').val();
		// 	// console.log(branch_code);
		// 	var mod_txt = $('#item-search').find("option:selected").text();
		// 	add_to_group(mod,mod_txt,branch_code);
		// 	return false;
		// });
		// $('.del').each(function(){
		// 	var id = $(this).attr('ref');
		// 	var branch = $(this).attr('ref2');
		// 	var mod_id = $(this).attr('ref3');
		// 	var mod_group_id = $('#mod_group_id').val();
		// 	// alert(mod_id);
		// 	remove_row(id,branch,mod_id,mod_group_id);
		// });
		// $('.dflt').each(function(){
		// 	var id = $(this).attr('ref');
		// 	make_dflt_row(id);
		// });
		// // $('#item-search').keyup(function(e){
		// // 	if(e.keyCode == '13'){
		// // 		$(this).val("");
		// // 	}
		// // });
		// function add_to_group(id,text,branch_code){
		// 	var mod_id = id;
		// 	var mod_group_id = $('#mod_group_id').val();
		// 	var bcode = branch_code;
		// 	var mod_text = text;
		// 	$.post(baseUrl+'mods/group_modifiers_details_db','mod_group_id='+mod_group_id+'&mod_id='+mod_id+'&mod_text='+text+'&branch_code='+bcode,function(data){
		// 		// console.log(data);
		// 		if(data.act == 'add'){
		// 			// console.log(data);
		// 			$('#modifier-list').append(data.li);
		// 			$.unblockUI();
		// 			$("body").removeClass("modal-backdrop");
		// 			rMsg(data.msg,'success');
		// 			$("#item-search").val('').selectpicker('refresh');


		// 			// $('#modifier-list').append(data.li);
		// 			// $('#item-search').selectpicker('deselectAll');
		// 		}
		// 		else if(data.act == 'error'){
		// 			// console.log(data);
		// 			$.unblockUI();
		// 			$("body").removeClass("modal-backdrop");
		// 			rMsg(data.msg,'error');
		// 		}
		// 		else{
		// 			var i = $('#li-'+data.id);
		// 			$('#li-'+data.id).remove();
		// 			$('#modifier-list').append(data.li);
		// 			rMsg(data.msg,'success');
		// 		}
		// 		remove_row(data.id);
		// 		make_dflt_row(data.id);
		// 	},'json');
		// 	// },);
		// }
		// function remove_row(id,branch,mod_id,mod_group_id){
		// 	$('#del-'+id).click(function(){
		// 		// alert(mod_group_id);
		// 		App.blockUI(), window.setTimeout(function() {                    
        //            // App.unblockUI()
        //             //$("body").removeClass("modal-backdrop");   
        //         }, 2e3)
		// 		$.post(baseUrl+'mods/remove_group_modifier','group_mod_id='+id+'&mod_group_id='+mod_group_id,function(data){

		// 			$('#li-'+id).remove();
		// 			$('#li-'+id).remove();
		// 			$.unblockUI();
		// 			$("body").removeClass("modal-backdrop");
		// 			rMsg(data.msg,'warning');
		// 			// },);
		// 			// goTo('mods/group_form/'+id+'/'+branch);
		// 		},'json');
		// 		return false;
		// 	});
		// }
		// function make_dflt_row(id){
		// 	$('#dflt_'+id).click(function(){
		// 		var checked = $(this).is(":checked");
		// 		if(checked){
		// 			var ch = 1;
		// 		}
		// 		else{
		// 			var ch = 0;
		// 		}
		// 		$.post(baseUrl+'mods/default_group_modifier','group_mod_id='+id+'&dflt='+ch,function(data){
		// 			rMsg(data.msg,'success');
		// 		},'json');
			// });
		// }

	
	<?php endif; ?>
});
</script>