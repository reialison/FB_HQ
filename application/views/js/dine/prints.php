<script>
$(document).ready(function(){
	<?php if($use_js == 'mainPageJS'): ?>
		$('#prnt-main').height($(document).height() - 63);
		$('#prnt-loads').height($('#prnt-main').height() - 68);
		$('#prnt-loads').rLoad({url:baseUrl+'prints/date_and_time'});
		$('#date-time-sales-btn').addClass('selected');

		// $('.datepicker').datepicker({format:'yyyy-mm-dd'});

		$('#date-time-sales-btn').click(function(){
			$('#prnt-loads').rLoad({url:baseUrl+'prints/date_and_time'});
			$('.load-types-btns').removeClass('selected');
			$(this).addClass('selected');
			return false;
		});
		$('#shift-sales-btn').click(function(){
			$('#prnt-loads').rLoad({url:baseUrl+'prints/shift_sales'});
			$('.load-types-btns').removeClass('selected');
			$(this).addClass('selected');
			return false;
		});
		$('#end-day-sales-btn').click(function(){
			$('#prnt-loads').rLoad({url:baseUrl+'prints/end_day_sales'});
			$('.load-types-btns').removeClass('selected');
			$(this).addClass('selected');
			return false;
		});
	<?php elseif($use_js == 'datetimeJS'): ?>
		$('.print-containers').css({'height':$('#prnt-loads').height() - 20});
		$('#report-view-div').css({'height':$('#print-load').height() - 25});
		$('.rep-btns').click(function(){
			load_rep($(this).attr('ref'),$(this));
			return false;
		});
		$('#pdf-paper-btn').click(function(){
			$('#report-view-div').print();
			return false;
		});	
		$('#print-paper-btn').click(function(){
			var ref = $(this).attr('target');
			$("#sform").rOkay({
				btn_load		: 	$('#print-paper-btn'),
				passTo			: 	'prints/'+ref+'/1',
				bnt_load_remove	: 	true,
				asJson			: 	false,
				onComplete		:	function(data){
										// alert(data);
									}
			});
			return false;
		});
		function selector(cl,btn){
			$(cl).removeClass('selected');
			btn.addClass('selected');
		}
		function load_rep(ref,btn){
			$('#report-view-div').html('');
			$("#sform").rOkay({
				btn_load		: 	btn,
				passTo			: 	'prints/'+ref,
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										$('#print-paper-btn').attr('target',ref);
										selector('.rep-btns',btn);
										$('#report-view-div').html(data.code);
										scroller();
									}
			});
		}
		function scroller(){
			scrolled = 0;
			// $('#report-view-div').perfectScrollbar({suppressScrollX: true});
			$("#down-paper-btn").on("click" ,function(){
			    var inHeight = $("#report-view-div")[0].scrollHeight;
			    var divHeight = $("#report-view-div").height();
			    var trueHeight = inHeight - divHeight;
		        if((scrolled + 150) > trueHeight){
		        	scrolled = trueHeight;
		        }
		        else{
		    	    scrolled=scrolled+150;				    	
		        }
			    // scrolled=scrolled+100;
				$("#report-view-div").animate({
				        scrollTop:  scrolled
				},1);
			});
			$("#up-paper-btn").on("click" ,function(){
				if(scrolled > 0){
					scrolled=scrolled-150;
					$("#report-view-div").animate({
					        scrollTop:  scrolled
					},1);
				}
			});
		}
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 	<?php elseif($use_js == 'sfhitJS'): ?>
		$('.print-containers').css({'height':$('#prnt-loads').height() - 20});
		$('#shifts-load').css({'height':$('#prnt-loads').height() - 85});
		$('#report-view-div').css({'height':$('#print-load').height() - 25});
		load_shifts();
		$('#calendar').change(function(){
			load_shifts();
		});
		function load_shifts(){
			var calendar = $('#calendar').val();
			$.post(baseUrl+'prints/get_shifts','calendar='+calendar,function(data){
				$('#shifts-load').html(data.code);
				$('#report-view-div').html('');
				$('.rep-btns').removeClass('selected');
				// alert(data.code);
				var shifts = data.shifts;
				$.each(shifts,function(key,opt){
					$('#shift-box-'+key).click(function(){
						var box = $(this);
						
						
						$('.shift-box').removeClass('selected');
						box.addClass('selected');
						var selected = $('.rep-btns.selected');
						if(typeof selected !== 'undefined' || selected !== null){
							load_rep(selected.attr('ref'),selected);
						}	
					});
				});
			},'json');
			// alert(data)
			// });
		}
		$('.rep-btns').click(function(){
			load_rep($(this).attr('ref'),$(this));
			return false;
		});
		$('#pdf-paper-btn').click(function(){
			$('#report-view-div').print();
			return false;
		});	
		$('#print-paper-btn').click(function(){
			var ref = $(this).attr('target');
			var title = $(this).attr('title');
			var shift_id = $('.shift-box.selected').attr('ref');

			if(typeof shift_id === 'undefined' || shift_id === null){
				rMsg('Select A shift','error');   
			}
			else{
				$("#sform").rOkay({
					btn_load		: 	$('#print-paper-btn'),
					passTo			: 	'prints/'+ref+'/1',
					addData			: 	'shift_id='+shift_id+'&title='+title,
					bnt_load_remove	: 	true,
					asJson			: 	false,
					onComplete		:	function(data){
											// alert(data);
										}
				});
			}	
				return false;
		});
		function selector(cl,btn){
			$(cl).removeClass('selected');
			btn.addClass('selected');
		}
		function load_rep(ref,btn){
			$('#report-view-div').html('');
			var shift_id = $('.shift-box.selected').attr('ref');
			var title = btn.attr('title');
			if(typeof shift_id === 'undefined' || shift_id === null){
				rMsg('Select A shift','error');   
			}
			else{
				if(typeof title != 'undefined'){
					$("#sform").rOkay({
						btn_load		: 	btn,
						passTo			: 	'prints/'+ref,
						addData			: 	'shift_id='+shift_id+'&title='+title,
						bnt_load_remove	: 	true,
						asJson			: 	true,
						onComplete		:	function(data){
												// alert(data);
												// console.log(data);
												$('#print-paper-btn').attr('target',ref);
												$('#print-paper-btn').attr('title',title);
												// $('#print-paper-btn').attr('code',data.code);
												selector('.rep-btns',btn);
												$('#report-view-div').html(data.code);
												scroller();
											}
					});
				}
			}
		}
		function scroller(){
			scrolled = 0;
			$("#report-view-div").scrollTop = 0;
			// $('#report-view-div').perfectScrollbar({suppressScrollX: true});
			$("#down-paper-btn").on("click" ,function(){
			    var inHeight = $("#report-view-div")[0].scrollHeight;
			    var divHeight = $("#report-view-div").height();
			    var trueHeight = inHeight - divHeight;
		        if((scrolled + 150) > trueHeight){
		        	scrolled = trueHeight;
		        }
		        else{
		    	    scrolled=scrolled+150;				    	
		        }
			    // scrolled=scrolled+100;
				$("#report-view-div").animate({
				        scrollTop:  scrolled
				},1);
			});
			$("#up-paper-btn").on("click" ,function(){
				if(scrolled > 0){
					scrolled=scrolled-150;
					$("#report-view-div").animate({
					        scrollTop:  scrolled
					},1);
				}
			});
		}
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});	
	<?php elseif($use_js == 'dayReadsJS'): ?>
		$('.print-containers').css({'height':$('#prnt-loads').height() - 20});
		$('#report-view-div').css({'height':$('#print-load').height() - 25});
		$('.rep-btns').click(function(){
			load_rep($(this).attr('ref'),$(this));
			return false;
		});
		$('#pdf-paper-btn').click(function(){
			$('#report-view-div').print();
			return false;
		});	
		$('#print-paper-btn').click(function(){
			var ref = $(this).attr('target');
			var title = $(this).attr('title');
			$("#sform").rOkay({
				btn_load		: 	$('#print-paper-btn'),
				passTo			: 	'prints/'+ref+'/1',
				addData			: 	'title='+title,
				bnt_load_remove	: 	true,
				asJson			: 	false,
				onComplete		:	function(data){
										// alert(data);
									}
			});
			return false;
		});
		function selector(cl,btn){
			$(cl).removeClass('selected');
			btn.addClass('selected');
		}
		function load_rep(ref,btn){
			var title = btn.attr('title');

			$('#report-view-div').html('');
			$("#sform").rOkay({
				btn_load		: 	btn,
				passTo			: 	'prints/'+ref,
				addData			: 	'title='+title,
				bnt_load_remove	: 	true,
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										console.log(data);
										$('#print-paper-btn').attr('target',ref);
										$('#print-paper-btn').attr('title',title);
										selector('.rep-btns',btn);
										$('#report-view-div').html(data.code);
										scroller();
									}
			});
		}
		function scroller(){
			scrolled = 0;
			$("#report-view-div").scrollTop = 0;
			$("#down-paper-btn").on("click" ,function(){
			    var inHeight = $("#report-view-div")[0].scrollHeight;
			    var divHeight = $("#report-view-div").height();
			    var trueHeight = inHeight - divHeight;
		        if((scrolled + 150) > trueHeight){
		        	scrolled = trueHeight;
		        }
		        else{
		    	    scrolled=scrolled+150;				    	
		        }
			    // scrolled=scrolled+100;
				$("#report-view-div").animate({
				        scrollTop:  scrolled
				},1);
			});
			$("#up-paper-btn").on("click" ,function(){
				if(scrolled > 0){
					scrolled=scrolled-150;
					$("#report-view-div").animate({
					        scrollTop:  scrolled
					},1);
				}
			});
		}
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 	<?php elseif($use_js == 'xreadBackJs'): ?>
		//$('#daterange').daterangepicker({separator: ' to '});

		$('#search-btn').click(function(){
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
				return false;
			}
			if($('#user').val() == ""){
				rMsg('Select User','error');
				return false;
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
			$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_xreads';
				date = $('#date').val();
				user = $('#user').val();
				branch_id = $('#branch_id').val();
				terminal_id = $('#terminal_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'date='+date+'&user='+user+'&branch_id='+branch_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').html(data.code);
				// });
				},'json');
			}
			return false;
		});

		$('#print-btn').click(function(){
			//$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_xreads';
				date = $('#date').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'date='+date+'&user='+user+'&branch_id='+branch_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').print();
					
				return false;
				});
				// },'json');
		});

		$('#excel-btn').click(function(){
			var formData = 'date='+$('#date').val()+'&user='+$("#user").val()+'&branch_id='+$("#branch_id").val(); 
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#user').val() == ""){
				rMsg('Select User','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
				// $.rProgressBar();
				goTo('prints/excel_xread?'+formData);
			}

			return false;
		});
	<?php elseif($use_js == 'zreadBackJs'): ?>
		//$('#daterange').daterangepicker({separator: ' to '});

		$('#search-btn').click(function(){
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
				return false;
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch ','error');
				return false;
			}
			else{
			$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_zreads';
				date = $('#date').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'title=ZREAD&calendar='+date+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&terminal_id='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').html(data.code);
				// });
				},'json');
			}
			return false;
		});

		$('#print-btn').click(function(){
			//$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_zreads';
				date = $('#date').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'calendar='+date+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').print();
					
				return false;
				// });
				},'json');
		});

		$('#excel-btn').click(function(){
			var formData = 'title=ZREAD&calendar='+$('#date').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val(); 
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
				// $.rProgressBar();
				goTo('prints/excel_zread?'+formData);
			}

			return false;
		});
	<?php elseif($use_js == 'DiscRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var excel = "disc_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&disc_id='+$("#disc_id").val()+'&terminal_id='+$("#terminal_id").val();

			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('prints/'+excel+'?'+formData);
			}

			return false;
		});
		$("#report_type").change(function(){
			if($(this).val() == 3){
				$("#category-div").hide();
			}else{
				$("#category-div").show();
			}
		});
		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&disc_id='+$("#disc_id").val()+'&terminal_id='+$("#terminal_id").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "disc_rep_gen";				
				
				console.log(page);

				$.post(baseUrl+'prints/'+page,formData,function(data){
					// alert(data);
					console.log(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
							// console.log(data);
							$('#view-list').click(function(){
								view_list(res);
								return false;
							});
							$('#view-grid').click(function(){
								view_grid(res);
								return false;
							});
							$('#pdf-btn').click(function(){
								$('#print-div').print();
								return false;
							});
						 }
					});
				// });
				},'json');				
			}
			return false;
		});
		$('#tcpdf-btn').click(function(){			
			var report_type = $("#report_type").val();
			var pdf = "disc_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&disc_id='+$("#disc_id").val()+'&terminal_id='+$("#terminal_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"prints/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			}

			return false;
		});
		function view_list(data){
			$('#print-div').html("");
			$('#print-div').html(data.code);
			$('#print-div').parent().parent().parent().parent().show();
			$('.listyle-btns').removeClass('active');
			$('#view-list').addClass('active');
			$('#print-box').show();
		}
		function view_grid(data){
			$('#print-div').html("");
			var bar = new Morris.Bar({
                element: 'print-div',
                resize: true,
                data: data.tbl_vals,
                xkey: 'name',
                ykeys: ['amount', 'qty'],
                labels: ['Amount', 'Qty'],
                gridTextSize: 8,

                hideHover: 'auto'
            });
            $('#print-div').parent().parent().parent().parent().show();
            $('.listyle-btns').removeClass('active');
			$('#view-grid').addClass('active');
			$('#print-box').show();
		}


		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 		$("#search").keyup(function(e){
 			e.preventDefault();
 			if($("#main-tbl").length)
 			{
 				myFunction(); 			 				
 			}
 		}); 		
 		function myFunction() {
		  // Declare variables 
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("search");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("main-tbl");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		    td = tr[i].getElementsByTagName("td")[0];
		    if (td) {
		      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		        tr[i].style.display = "";
		      } else {
		        tr[i].style.display = "none";
		      }
		    } 
		  }
		}
	<?php elseif($use_js == 'SysSalesBackJs'): ?>
		//$('#daterange').daterangepicker({separator: ' to '});

		$('#search-btn').click(function(){
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
				return false;
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch ','error');
				return false;
			}
			else{
			$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_system_sales';
				calendar_range = $('#calendar_range').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'calendar_range='+calendar_range+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&terminal_id='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').html(data.code);
				// });
				},'json');
			}
			return false;
		});

		$('#print-btn').click(function(){
			//$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_system_sales';
				calendar_range = $('#calendar_range').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'calendar_range='+calendar_range+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').print();
					
				return false;
				// });
				},'json');
		});

		$('#excel-btn').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val(); 
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
				// $.rProgressBar();
				goTo('prints/excel_zread?'+formData);
			}

			return false;
		});
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 	<?php elseif($use_js == 'menuItemSalesRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		// $('#excel-btn').click(function(){

		// 	var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
		// 	excel = "menuitem_sales_rep_gen_excel";

		// 	if($('#calendar_range').val() == ""){
		// 		rMsg('Enter Date Range','error');
		// 	}
		// 	if($('#branch_id').val() == ""){
		// 		rMsg('Please Select Branch','error');
		// 	}
		// 	else{
		// 		$.rProgressBar();
		// 		goTo('prints/'+excel+'?'+formData);
		// 	}

		// 	return false;
		// });
		// $("#report_type").change(function(){
		// 	if($(this).val() == 3){
		// 		$("#category-div").hide();
		// 	}else{
		// 		$("#category-div").show();
		// 	}
		// });
		// $('#gen-rep').click(function(){
		// 	var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
		// 	if($('#calendar_range').val() == ""){
		// 		rMsg('Enter Date Range','error');
		// 	}
		// 	if($('#branch_id').val() == ""){
		// 		rMsg('Please Select Branch','error');
		// 	}
		// 	else{
		// 		var btn = $(this);
		// 		btn.goLoad();
		// 		$.rProgressBar();

		// 		$.post(baseUrl+'prints/menuitem_sales_rep_gen',formData,function(data){
		// 			// alert(data);
		// 			console.log(data);
		// 			$.rProgressBarEnd({
		// 				onComplete : function(){
		// 					btn.goLoad({load:false});
		// 					var res = data;
		// 					view_list(res);
		// 					// console.log(data);
		// 					$('#view-list').click(function(){
		// 						view_list(res); 
		// 						return false;
		// 					});
		// 					$('#view-grid').click(function(){
		// 						view_grid(res);
		// 						return false;
		// 					});
		// 					$('#pdf-btn').click(function(){
		// 						$('#print-div').print();
		// 						return false;
		// 					});
		// 				 }
		// 			});
		// 		// });
		// 		},'json');				
		// 	}
		// 	return false;
		// });
		$('#gen-rep').click(function(){
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
				return false;
			}
			if($('#branch_id').val() == ""){
				rMsg('Please Select Branch','error');
			}
			else{
			$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_menu_item_sales';
				date = $('#date').val();
				user = $('#user').val();
				branch_id = $('#branch_id').val();
				terminal_id = $('#terminal_id').val();
				json = 'false';
				var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&brand='+$("#terminal_id").val()+'&json='+json;
				// formData = 'date='+date+'&user='+user+'&branch_id='+branch_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').html(data.code);
				// });
				},'json');
			}
			return false;
		});
		$('#print-btn').click(function(){
			//$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_menu_item_sales';
				date = $('#date').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				// formData = 'date='+date+'&user='+user+'&branch_id='+branch_id+'&json='+json;
				var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&brand='+$("#terminal_id").val()+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').print();
					
				return false;
				});
				// },'json');
		});

		$('#excel-btn').click(function(){
			// var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&json='+json;
			var formData = 'calendar_range='+$('#calendar_range').val()+'&user='+$("#user").val()+'&branch_id='+$("#branch_id").val()+'&brand='+$("#terminal_id").val(); 
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#user').val() == ""){
				rMsg('Select User','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
				// $.rProgressBar();
				// goTo('prints/excel_menu_item_sales?'+formData);
				goTo('prints/menuitem_sales_rep_gen_excel?'+formData);
			}

			return false;
		});
		$('#tcpdf-btn').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Please Select Branch','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"prints/menuitem_sales_rep_pdf?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			}

			return false;
		});
		function view_list(data){
			$('#print-div').html("");
			$('#print-div').html(data.code);
			$('#print-div').parent().parent().parent().parent().show();
			$('.listyle-btns').removeClass('active');
			$('#view-list').addClass('active');
			$('#print-box').show();
		}
		function view_grid(data){
			$('#print-div').html("");
			var bar = new Morris.Bar({
                element: 'print-div',
                resize: true,
                data: data.tbl_vals,
                xkey: 'name',
                ykeys: ['amount', 'qty'],
                labels: ['Amount', 'Qty'],
                gridTextSize: 8,

                hideHover: 'auto'
            });
            $('#print-div').parent().parent().parent().parent().show();
            $('.listyle-btns').removeClass('active');
			$('#view-grid').addClass('active');
			$('#print-box').show();
		}


		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 		$("#search").keyup(function(e){
 			e.preventDefault();
 			if($("#main-tbl").length)
 			{
 				myFunction(); 			 				
 			}
 		}); 		
 		function myFunction() {
		  // Declare variables 
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("search");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("main-tbl");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		    td = tr[i].getElementsByTagName("td")[0];
		    if (td) {
		      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		        tr[i].style.display = "";
		      } else {
		        tr[i].style.display = "none";
		      }
		    } 
		  }
		}
	<?php elseif($use_js == 'ModSalesBackJs'): ?>
		//$('#daterange').daterangepicker({separator: ' to '});

		$('#search-btn').click(function(){
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
				return false;
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch ','error');
				return false;
			}
			else{
			$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_mod_sales_rep';
				calendar_range = $('#calendar_range').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'calendar_range='+calendar_range+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&brand='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').html(data.code);
				// });
				 },'json');
			}
			return false;
		});

		$('#print-btn').click(function(){
			//$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			var this_url = baseUrl+'prints/check_mod_sales_rep';
				calendar_range = $('#calendar_range').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'calendar_range='+calendar_range+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&brand='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').print();
					
				return false;
				// });
				},'json');
		});

		$('#excel-btn').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val()+'&brand='+$("#terminal_id").val(); 
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
				// $.rProgressBar();
				goTo('prints/excel_mod_sales?'+formData);
			}

			return false;
		});
		
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 	<?php elseif($use_js == 'mgtSalesJs'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			// var report_type = $("#report_type").val();
			// var pdf = "sales_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			excel = "mgt_rep_gen_excel";
			// 				+'&menu_cat_id='+$("#menu_cat_id").val();
			// if(report_type == 1)
			// {
			// }
			// else if(report_type == 2)
			// {
			// 	excel = "menu_sales_rep_excel";
			// }
			// else if(report_type == 3)
			// {
			// 	excel = "hourly_sales_rep_excel";
			// 	var formData = 'calendar_range='+$('#calendar_range').val()
			// 				+'&menu_cat_id=0';
			// }


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('prints/'+excel+'?'+formData);
			}

			return false;
		});
		// $("#report_type").change(function(){
		// 	if($(this).val() == 3){
		// 		$("#category-div").hide();
		// 	}else{
		// 		$("#category-div").show();
		// 	}
		// });
		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				// var report_type = $("#report_type").val();
				// var page = "sales_rep_gen";
				// if(report_type == 1)
				// {
				// 	page = "sales_rep_gen";
				// }
				// else if(report_type == 2)
				// {
				// 	page = "menu_sales_rep_gen";
				// }
				// else if(report_type == 3)
				// {
				// 	page = "hourly_sales_rep_gen";
				// 	var formData = 'calendar_range='+$('#calendar_range').val()
				// 			+'&menu_cat_id=0';
				// }
				// console.log(page);

				$.post(baseUrl+'prints/mgt_rep_gen',formData,function(data){
					// alert(data);
					// console.log(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
							// console.log(data);
							$('#view-list').click(function(){
								view_list(res); 
								return false;
							});
							$('#view-grid').click(function(){
								view_grid(res);
								return false;
							});
							$('#pdf-btn').click(function(){
								$('#print-div').print();
								return false;
							});
						 }
					});
				// });
				},'json');				
			}
			return false;
		});
		$('#tcpdf-btn').click(function(){			
			// var report_type = $("#report_type").val();
			// var pdf = "sales_rep_gen";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			// if(report_type == 1)
			// {
			// 	pdf = "sales_rep_pdf";
			// }
			// else if(report_type == 2)
			// {
			// 	pdf = "menu_sales_rep_pdf";
			// }
			// else if(report_type == 3)
			// {
			// 	pdf = "hourly_sales_rep_pdf";
			// 	var formData = 'calendar_range='+$('#calendar_range').val()
			// 				+'&menu_cat_id=0';
			// }
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"prints/menuitem_sales_rep_pdf?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			}

			return false;
		});
		function view_list(data){
			$('#print-div').html("");
			$('#print-div').html(data.code);
			$('#print-div').parent().parent().parent().parent().show();
			$('.listyle-btns').removeClass('active');
			$('#view-list').addClass('active');
			$('#print-box').show();
		}
		function view_grid(data){
			$('#print-div').html("");
			var bar = new Morris.Bar({
                element: 'print-div',
                resize: true,
                data: data.tbl_vals,
                xkey: 'name',
                ykeys: ['amount', 'qty'],
                labels: ['Amount', 'Qty'],
                gridTextSize: 8,

                hideHover: 'auto'
            });
            $('#print-div').parent().parent().parent().parent().show();
            $('.listyle-btns').removeClass('active');
			$('#view-grid').addClass('active');
			$('#print-box').show();
		}


		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});
 		$("#search").keyup(function(e){
 			e.preventDefault();
 			if($("#main-tbl").length)
 			{
 				myFunction(); 			 				
 			}
 		}); 		
 		function myFunction() {
		  // Declare variables 
		  var input, filter, table, tr, td, i;
		  input = document.getElementById("search");
		  filter = input.value.toUpperCase();
		  table = document.getElementById("main-tbl");
		  tr = table.getElementsByTagName("tr");

		  // Loop through all table rows, and hide those who don't match the search query
		  for (i = 0; i < tr.length; i++) {
		    td = tr[i].getElementsByTagName("td")[0];
		    if (td) {
		      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
		        tr[i].style.display = "";
		      } else {
		        tr[i].style.display = "none";
		      }
		    } 
		  }
		}



	<?php endif; ?>
});
</script>