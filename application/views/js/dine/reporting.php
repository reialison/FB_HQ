<script>
$(document).ready(function(){
	<?php if($use_js == 'menusRepJS'): ?>
		$('#print-box').hide();

		$("#branch_code").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_code"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_code"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});

		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_code='+$("#branch_code").val()
							+'&brand='+$("#brand").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/menus_rep_excel?'+formData);
			}

			return false;
		});
		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_code='+$("#branch_code").val()
							+'&brand='+$("#brand").val();
			// console.log(formData);
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				$.post(baseUrl+'reporting/menus_rep_gen',formData,function(data){
					console.log(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
							$('#view-list').click(function(){
								view_list(res);
								return false;
							});
							$('#view-grid').click(function(){
								view_grid(res);
								return false;
							});
							// $('#pdf-btn').click(function(){
							// 	$('#print-div').print();
							// 	return false;
							// });
						 }
					});
				},'json');		
				// });		
			}
			return false;
		});
		$('#pdf-btn').click(function(){		
			var pdf = "menus_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_code='+$("#branch_code").val()
							+'&brand='+$("#brand").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'hourlyRepJS'): ?>
		//$('#daterange').daterangepicker({separator: ' to '});
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});

 		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		}); 

		$('#search-btn').click(function(){

			date = $('#calendar_range').val();
			branch_id = $('#branch_id').val();
			brand = $('#brand').val();
			terminal_id = $('#terminal_id').val();
			// user = $('#user').val();
			json = 'false';
			// alert(date+''+user);

			if(date != ""){
				$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
				var this_url = baseUrl+'reporting/check_hourly_sales';
				//dr = $('#daterange').val();
				formData = 'calendar_range='+date+'&branch_id='+branch_id+'&brand='+brand+'&terminal_id='+terminal_id+'&json='+json;
				$.post(this_url, formData, function(data){
					// alert(data);
					$('#print-div').html(data.code);
				// });
				},'json');
			}else{
				rMsg('Enter Date Range','error');
			}

			
		});

		$('#print-btn').click(function(){
			// alert('aw');
			//$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
			// var this_url = baseUrl+'reporting/check_hourly_sales_excel';

			// date = $('#calendar_range').val();
			// // user = $('#user').val();
			// json = 'true';

			// // alert(date+''+user);

			// //dr = $('#daterange').val();
			// formData = 'date='+date+'&json='+json;
			// $.post(this_url, formData, function(data){
				// alert(data);
				//$('#print-div').html(data.code);
			// });
			date = $('#calendar_range').val();
			branch_id = $('#branch_id').val();
			brand = $('#brand').val();

			var formData = formData = 'calendar_range='+date+'&branch_id='+branch_id+'&brand='+brand+'&terminal_id='+terminal_id;
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/check_hourly_sales_excel?'+formData);
			}

			return false;

			// },'json');
		});
		<?php elseif($use_js == 'salesRepJS'): ?>
		$("#menu_cat_id").change(function () {
			var menu_cat = $('select[name="menu_cat_id"] :selected').attr('branch_name')
			$("#branch_id").val(menu_cat);
			// alert(testing);
			$('#branch_id').attr("disabled", true); 
			if(menu_cat == undefined){
				// alert('as/dfljsao;dapj');
				$('#branch_id').attr("disabled", false); 
			}
		});

		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#menu_cat_id').select2();
				$('#menu_cat_id').select2('destroy');
				$('#menu_cat_id').children('option:not(:first)').hide();
				$('#menu_cat_id').children('option[branch_name='+branch_+']').show();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#menu_cat_id').select2();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});

		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var pdf = "sales_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if(report_type == 1)
			{
				excel = "sales_rep_excel";
			}
			else if(report_type == 2)
			{
				excel = "menu_sales_rep_excel";
			}
			else if(report_type == 3)
			{
				excel = "hourly_sales_rep_excel";
				var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id=0'+'&branch_id='+$("#branch_id").val()
			}


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}else if($('#branch_id').val() == ""){
				rMsg('Please Select Branch.','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+excel+'?'+formData);
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
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val()
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}else if($('#branch_id').val() == ""){
				rMsg('Please Select Branch.','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "sales_rep_gen";
				if(report_type == 1)
				{
					page = "sales_rep_gen";
				}
				else if(report_type == 2)
				{
					page = "menu_sales_rep_gen";
				}
				else if(report_type == 3)
				{
					page = "hourly_sales_rep_gen";
					var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id=0'+'&branch_id='+$("#branch_id").val();
				}
				console.log(page);

				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var report_type = $("#report_type").val();
			var pdf = "sales_rep_gen";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val()
			if(report_type == 1)
			{
				pdf = "sales_rep_pdf";
			}
			else if(report_type == 2)
			{
				pdf = "menu_sales_rep_pdf";
			}
			else if(report_type == 3)
			{
				pdf = "hourly_sales_rep_pdf";
				var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id=0'
							+'&branch_id='+$("#branch_id").val();
			}
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}else if($('#branch_id').val() == ""){
				rMsg('Please Select Branch.','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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
	<?php elseif($use_js == 'dtrRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;
			var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/menus_rep_excel?'+formData);
			}

			return false;
		});
		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&start_time='+$('#start_time').val()
							+'&end_time='+$('#end_time').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				$.post(baseUrl+'reporting/dtr_rep_gen',formData,function(data){
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
			var formData = 'calendar_range='+$('#calendar_range').val()+
							"&start_time="+$("#start_time").val()+
							"&end_time="+$("#end_time").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();								
				window.open(baseUrl+"reporting/dtr_rep_pdf?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'momJs'): ?>
		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});   

		$('#generate').click(function(){
			var formData = 'date='+$('#date').val()							
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();				
				var	page = "month_to_month_gen";								

				$.post(baseUrl+'reporting/'+page,formData,function(data){				
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);							
							$('#view-list').click(function(){
								view_list(res);
								return false;
							});
							$('#view-grid').click(function(){
								view_grid(res);
								return false;
							});
							$('#tcpdf-btn').click(function(){
								// $('#print-div').print();
								return false;
							});
						 }
					});
				// });
				},'json');				
			}
			return false;
		});

		$("#tcpdf-btn").click(function(e){
			var formData = 'date='+$('#date').val()							
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();

			var	page = "mom_rep_pdf";				
			$.rProgressBar();				
			window.open(baseUrl+"reporting/"+page+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			
			return false;
		});
		$('#excel-btn').click(function(){			
			var formData = 'date='+$('#date').val()							
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
							
			var	page = "mom_rep_pdf";				
			if($('#date').val() == ""){
				rMsg('Enter Date','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/mom_rep_excel?'+formData);
			}

			return false;
		});

		function view_list(data)
		{
			$('#print-div').html("");
			$('#print-div').html(data.code);
			$('#print-div').parent().parent().parent().parent().show();
			$('.listyle-btns').removeClass('active');
			$('#view-list').addClass('active');
			$('#print-box').show();
		}
		function view_grid(data)
		{
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
	<?php elseif($use_js == 'ytdJs'): ?>
		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});

		$('#generate').click(function(){
			var formData = 'from='+$('#from').val()
						    +'&to='+$('#to').val()							
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();				
				var	page = "year_to_date_gen";								

				$.post(baseUrl+'reporting/'+page,formData,function(data){				
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);							
							$('#view-list').click(function(){
								view_list(res);
								return false;
							});
							$('#view-grid').click(function(){
								view_grid(res);
								return false;
							});
							$('#tcpdf-btn').click(function(){
								// $('#print-div').print();
								return false;
							});
						 }
					});
				// });
				},'json');				
			}
			return false;
		});

		$("#tcpdf-btn").click(function(e){
			var formData = 'from='+$('#from').val()
						    +'&to='+$('#to').val()							
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			var	page = "ytd_rep_pdf";				
			$.rProgressBar();				
			window.open(baseUrl+"reporting/"+page+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			
			return false;
		});
		$('#excel-btn').click(function(){			
			var formData = 'from='+$('#from').val()
						    +'&to='+$('#to').val()							
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			var	page = "ytd_rep_excel";				
			if($('#date').val() == ""){
				rMsg('Enter Date','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+page+'?'+formData);
			}

			return false;
		});

		function view_list(data)
		{
			$('#print-div').html("");
			$('#print-div').html(data.code);
			$('#print-div').parent().parent().parent().parent().show();
			$('.listyle-btns').removeClass('active');
			$('#view-list').addClass('active');
			$('#print-box').show();
		}
		function view_grid(data)
		{
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
	<?php elseif($use_js == 'itemRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var page = "item_sales_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();						

			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				goTo('reporting/'+page+'?'+formData);
			}

			return false;
		});

		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{				
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();				
				var page = "item_sales_rep_gen";
				
				// console.log(page);
				// alert(formData);
				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var report_type = $("#report_type").val();
			var pdf = "item_sales_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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
	<?php elseif($use_js == 'issuesRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var page = "issues_stamp_rep_excel";

			var formData = 'year='+$('#year').val()+'&branch_id='+$("#branch_id").val();						

			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#year').val() == ""){
				rMsg('Enter Year','error');
			}
			else{
				$.rProgressBar();				
				goTo('reporting/'+page+'?'+formData);
			}

			return false;
		});

		$('#gen-rep').click(function(){
			var formData = 'year='+$('#year').val()+'&branch_id='+$("#branch_id").val();
			if($('#year').val() == ""){
				rMsg('Enter year Range','error');
			}
			else{				
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();				
				var page = "issues_stamp_rep_gen";
				
				// console.log(page);
				// alert(formData);
				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var report_type = $("#report_type").val();
			var pdf = "issues_stamp_rep_pdf";

			var formData = 'year='+$('#year').val()+'&branch_id='+$("#branch_id").val();
			
			
			if($('#year').val() == ""){
				rMsg('Enter Year','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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
	<?php elseif($use_js == 'promoRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var page = "promo_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&discount_id='+$("#discount_id").val();						

			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#year').val() == ""){
				rMsg('Enter Year','error');
			}
			else{
				$.rProgressBar();				
				goTo('reporting/'+page+'?'+formData);
			}

			return false;
		});

		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&discount_id='+$("#discount_id").val();						
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{				
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();				
				var page = "promo_rep_gen";
				
				// console.log(page);
				// alert(formData);
				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var report_type = $("#report_type").val();
			var pdf = "promo_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&discount_id='+$("#discount_id").val();
			
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'voidSalesRepJS'): ?>
		$("#menu_cat_id").change(function () {
			var menu_cat = $('select[name="menu_cat_id"] :selected').attr('branch_name')
			$("#branch_id").val(menu_cat);
			// alert(testing);
			$('#branch_id').attr("disabled", true); 
			if(menu_cat == undefined){
				// alert('as/dfljsao;dapj');
				$('#branch_id').attr("disabled", false); 
			}
		});

		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#menu_cat_id').select2();
				$('#menu_cat_id').select2('destroy');
				$('#menu_cat_id').children('option:not(:first)').hide();
				$('#menu_cat_id').children('option[branch_name='+branch_+']').show();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#menu_cat_id').select2();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val();

			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});  
		
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var page = "voided_sales_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();						

			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				goTo('reporting/'+page+'?'+formData);
			}

			return false;
		});

		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{				
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();				
				var page = "voided_sales_rep_gen";
				
				// console.log(page);
				// alert(formData);
				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var report_type = $("#report_type").val();
			var pdf = "voided_sales_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'cashierJS'): ?>
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: false, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});

 		$('select#cashier ,  select#branch_id ').select2();

 		$('#gen-rep').click(function(){ 
			var formData = 'date='+$('#date').val();
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}else if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}else if($('#cashier').val() == ""){
				rMsg('Select Cashier','error');
			}
			else{
				// var this_url = baseUrl+'reporting/get_cashier_reports';
				date = $('#date').val();
				cashier = $('#cashier').val();
				branch_id = $('#branch_id').val();
				$.rProgressBar(); 

				formData = 'date='+date+'&cashier='+cashier+"&branch_id="+branch_id;
				// console.log(baseUrl+'reporting/cashier_report_gen');
				// console.log('asd');
				var btn = $(this);
				btn.goLoad();
				$.post(baseUrl+'reporting/cashier_report_gen',formData,function(data){
						console.log(data);

						// alert(data.branch_name);

					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							// console.log(res);
							view_list(res);

							var table = $('#main-tbl');
							// console.log(table);
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

									dom: 'Bfrtip',
						            buttons: [
						                {
						                	title: '',
						                	filename: 'Cashier\'s Report',
						                	text: 'Print',
								            extend: 'print', 
								            className: 'btn dark btn-outline',
								            messageTop: '<b>'+data.branch_name+'</b><br><b>'+data.address+'<br><b>Cashier\'s Report</b><br><b>Cashier: '+data.cashier_name+'<br>Date: '+data.dates},
						                { 
						                	title: '',
						                	filename: 'Cashier\'s Report',
											extend: 'pdfHtml5', 
											orientation: 'landscape',
											pageSize: 'legal',
											className: 'btn green btn-outline',
											messageTop: data.branch_name+'\r\n'+data.address+'\r\nCashier\'s Report\r\nCashier: '+data.cashier_name+'\r\nDate: '+data.dates
										},
										{
						                    extend: 'excel', 
						                    className: 'btn yellow btn-outline ',
						                    filename: 'Cashier\'s Report',
						                    title: ' ',
											customize: function (xlsx) {
											        console.log(xlsx);
											        var sheet = xlsx.xl.worksheets['sheet1.xml'];
											        var downrows = 6;
											        var clRow = $('row', sheet);
											        //update Row
											        clRow.each(function () {
											            var attr = $(this).attr('r');
											            var ind = parseInt(attr);
											            ind = ind + downrows;
											            $(this).attr("r",ind);
											        });
											 
											        // Update  row > c
											        $('row c ', sheet).each(function () {
											            var attr = $(this).attr('r');
											            var pre = attr.substring(0, 1);
											            var ind = parseInt(attr.substring(1, attr.length));
											            ind = ind + downrows;
											            $(this).attr("r", pre + ind);
											        });
											 
											        function Addrow(index,data) {
											            msg='<row r="'+index+'">'
											            for(i=0;i<data.length;i++){
											                var key=data[i].k;
											                var value=data[i].v;
											                msg += '<c t="inlineStr" r="' + key + index + '" >';
											                msg += '<is>';
											                msg +=  '<t>'+value+'</t>';
											                msg+=  '</is>';
											                msg+='</c>';
											            }
											            msg += '</row>';
											            return msg;
											        }
											 
											        //insert
											        var r1 = Addrow(1, [{ k: '', v: data.branch_name }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
											        var r2 = Addrow(2, [{ k: '', v: data.address}]);
											        var r3 = Addrow(3, [{ k: '', v: 'Cashier\'s Report'}]);
											        var r4 = Addrow(4, [{ k: '', v: 'Cashier: '+data.cashier_name}]);
											        var r5 = Addrow(5, [{ k: '', v: 'Date: '+data.dates}]);
											        
											        sheet.childNodes[0].childNodes[1].innerHTML = r1+r2+r3+r4+r5+ sheet.childNodes[0].childNodes[1].innerHTML;
											    }
						                    }
						            ],

						            "ordering": false,// disable column ordering 
						            "paging": false, //disable pagination

						            // "order": [
						            //     [0, 'asc']
						            // ],
						            
						            "lengthMenu": [
						                [5, 10, 15, 5, -1],
						                [5, 10, 15, 5, "All"] // change per page values here
						            ],
						            // set the initial value
						            // "pageLength": 10,

						            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
						        });
						 }
					});
				// });
				},'json');				
			}
			return false;
		});

 		$('#excel-btn').click(function(){
 			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}else if($('#cashier').val() == ""){
				rMsg('Select Cashier','error');
			}
			else{

				var this_url = baseUrl+'reporting/get_cashier_reports_excel';
				date = $('#date').val();
				cashier = $('#cashier').val();
				
				$.rProgressBar(); 
				
				formData = 'date='+date+'&cashier='+cashier;
				$.post(this_url, formData, function(data){
					// alert(data);
					if(data == ''){
						$.rProgressBarEnd({
							onComplete : function(){
								window.location = baseUrl+'reporting/cashier_report_excel';
							 }
						});
					}else{
						var res = JSON.parse(data);
						view_list(res);	
					}
					
					
				});
			
			}

			

			return false;
		});

		$('#tcpdf-btn').click(function(){			
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var this_url = baseUrl+'reporting/get_cashier_reports_tcpdf';
				date = $('#date').val();
				cashier = $('#cashier').val();
				
				$.rProgressBar(); 
				
				formData = 'date='+date+'&cashier='+cashier;
				$.post(this_url, formData, function(data){

					$.rProgressBarEnd({
						onComplete : function(){
							if(data == ''){							
								window.open(baseUrl+"reporting/cashier_report_pdf", "popupWindow", "width=600,height=600,scrollbars=yes");
							}else{
								var res = JSON.parse(data);
								view_list(res);		
							}	
							
						 }
					});
				});
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
<?php elseif($use_js == 'cashierChargeJS'): ?>
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: false, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});

 		$('select#cashier ,  select#branch_id ').select2();


 		$('#gen-rep').click(function(){ 
			var formData = 'date='+$('#date').val();
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			// else if($('#cashier').val() == ""){
			// 	rMsg('Select Cashier','error');
			// }
			else{
				// var this_url = baseUrl+'reporting/get_cashier_reports';
				date = $('#date').val();
				cashier = $('#cashier').val();
			    branch_id = $('#branch_id').val();

				$.rProgressBar(); 

				formData = 'date='+date+'&cashier='+cashier+'&branch_id='+branch_id;
				// console.log(baseUrl+'reporting/cashier_report_gen');
				// console.log('asd');
				var btn = $(this);
				btn.goLoad();
				$.post(baseUrl+'reporting/cashier_charge_report_gen',formData,function(data){
						console.log(data);

					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							console.log(res);
							view_list(res);
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

									dom: 'Bfrtip',
						            buttons: [
						                {
						                	title: '',
						                	filename: 'Charge Sales Summary Report',
						                	text: 'Print',
								            extend: 'print', 
								            className: 'btn dark btn-outline',
								            messageTop: '<b>'+data.branch_name+'</b><br><b>'+data.address+'<br><b>Cashier\'s Report</b><br><b>Cashier: '+data.cashier_name+'<br>Date: '+data.dates},
						                { 
						                	title: '',
						                	filename: 'Charge Sales Summary Report',
											extend: 'pdfHtml5', 
											orientation: 'landscape',
											pageSize: 'legal',
											className: 'btn green btn-outline',
											messageTop: data.branch_name+'\r\n'+data.address+'\r\nCashier\'s Report\r\nCashier: '+data.cashier_name+'\r\nDate: '+data.dates
										},
										{
						                    extend: 'excel', 
						                    className: 'btn yellow btn-outline ',
						                    filename: 'Charge Sales Summary Report',
						                    title: ' ',
											customize: function (xlsx) {
											        console.log(xlsx);
											        var sheet = xlsx.xl.worksheets['sheet1.xml'];
											        var downrows = 6;
											        var clRow = $('row', sheet);
											        //update Row
											        clRow.each(function () {
											            var attr = $(this).attr('r');
											            var ind = parseInt(attr);
											            ind = ind + downrows;
											            $(this).attr("r",ind);
											        });
											 
											        // Update  row > c
											        $('row c ', sheet).each(function () {
											            var attr = $(this).attr('r');
											            var pre = attr.substring(0, 1);
											            var ind = parseInt(attr.substring(1, attr.length));
											            ind = ind + downrows;
											            $(this).attr("r", pre + ind);
											        });
											 
											        function Addrow(index,data) {
											            msg='<row r="'+index+'">'
											            for(i=0;i<data.length;i++){
											                var key=data[i].k;
											                var value=data[i].v;
											                msg += '<c t="inlineStr" r="' + key + index + '" >';
											                msg += '<is>';
											                msg +=  '<t>'+value+'</t>';
											                msg+=  '</is>';
											                msg+='</c>';
											            }
											            msg += '</row>';
											            return msg;
											        }
											 
											        //insert
											        var r1 = Addrow(1, [{ k: '', v: data.branch_name }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
											        var r2 = Addrow(2, [{ k: '', v: data.address}]);
											        var r3 = Addrow(3, [{ k: '', v: 'Cashier\'s Report'}]);
											        var r4 = Addrow(4, [{ k: '', v: 'Cashier: '+data.cashier_name}]);
											        var r5 = Addrow(5, [{ k: '', v: 'Date: '+data.dates}]);
											        
											        sheet.childNodes[0].childNodes[1].innerHTML = r1+r2+r3+r4+r5+ sheet.childNodes[0].childNodes[1].innerHTML;
											    }
						                    }
						            ],

						            // setup responsive extension: http://datatables.net/extensions/responsive/
						            responsive: true,

						            "ordering": false,// disable column ordering 
						            "paging": false, //disable pagination

						            // "order": [
						            //     [0, 'asc']
						            // ],
						            
						            "lengthMenu": [
						                [5, 10, 15, 5, -1],
						                [5, 10, 15, 5, "All"] // change per page values here
						            ],
						            // set the initial value
						            // "pageLength": 10,

						            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

						            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
						            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
						            // So when dropdowns used the scrollable div should be removed. 
						            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
						        });
							// console.log(data);
							// $('#view-list').click(function(){
							// 	view_list(res);
							// 	return false;
							// });
							// $('#view-grid').click(function(){
							// 	view_grid(res);
							// 	return false;
							// });
							// $('#pdf-btn').click(function(){
							// 	$('#print-div').print();
							// 	return false;
							// });
						 }
					});
				// });
				},'json');				
			}
			return false;
		});

 		$('#excel-btn').click(function(){
 			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			// else if($('#cashier').val() == ""){
			// 	rMsg('Select Cashier','error');
			// }
			else{

				var this_url = baseUrl+'reporting/get_cashier_reports_excel';
				date = $('#date').val();
				cashier = $('#cashier').val();
				
				$.rProgressBar(); 
				
				formData = 'date='+date+'&cashier='+cashier;
				$.post(this_url, formData, function(data){
					// alert(data);
					if(data == ''){
						$.rProgressBarEnd({
							onComplete : function(){
								window.location = baseUrl+'reporting/cashier_report_excel';
							 }
						});
					}else{
						var res = JSON.parse(data);
						view_list(res);	
					}
					
					
				});
			
			}

			

			return false;
		});

		$('#tcpdf-btn').click(function(){			
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var this_url = baseUrl+'reporting/get_cashier_reports_tcpdf';
				date = $('#date').val();
				cashier = $('#cashier').val();
				
				$.rProgressBar(); 
				
				formData = 'date='+date+'&cashier='+cashier;
				$.post(this_url, formData, function(data){

					$.rProgressBarEnd({
						onComplete : function(){
							if(data == ''){							
								window.open(baseUrl+"reporting/cashier_report_pdf", "popupWindow", "width=600,height=600,scrollbars=yes");
							}else{
								var res = JSON.parse(data);
								view_list(res);		
							}	
							
						 }
					});
				});
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

	<?php elseif($use_js == 'brandRepJS'): ?>
		$("#menu_cat_id").change(function () {
			var menu_cat = $('select[name="menu_cat_id"] :selected').attr('branch_name')
			$("#branch_id").val(menu_cat);
			// alert(testing);
			$('#branch_id').attr("disabled", true); 
			if(menu_cat == undefined){
				// alert('as/dfljsao;dapj');
				$('#branch_id').attr("disabled", false); 
			}
		});

		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#menu_cat_id').select2();
				$('#menu_cat_id').select2('destroy');
				$('#menu_cat_id').children('option:not(:first)').hide();
				$('#menu_cat_id').children('option[branch_name='+branch_+']').show();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#menu_cat_id').select2();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});  

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});  

		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var pdf = "sales_rep_excel";
			excel = "brand_sales_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			// if(report_type == 1)
			// {
			// 	excel = "sales_rep_excel";
			// }
			// else if(report_type == 2)
			// {
			// }
			// else if(report_type == 3)
			// {
			// 	excel = "hourly_sales_rep_excel";
			// 	var formData = 'calendar_range='+$('#calendar_range').val()
			// 				+'&menu_cat_id=0'+'&branch_id='+$("#branch_id").val();
			// }


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+excel+'?'+formData);
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
			// alert($("#branch_id").val());
			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{

				if($("#branch_id").val() != ""){

					var btn = $(this);
					btn.goLoad();
					$.rProgressBar();
					// var report_type = $("#report_type").val();
					var page = "brand_sales_rep_gen";
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
					// 			+'&menu_cat_id=0'+'&branch_id='+$("#branch_id").val();
					// }
					// console.log(page);

					$.post(baseUrl+'reporting/'+page,formData,function(data){
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

				}else{
					rMsg('Select a Branch','error');
				}			
			}
			return false;
		});
		$('#tcpdf-btn').click(function(){			
			var report_type = $("#report_type").val();
			// var pdf = "sales_rep_gen";
			pdf = "brand_sales_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			// if(report_type == 1)
			// {
			// 	pdf = "sales_rep_pdf";
			// }
			// else if(report_type == 2)
			// {
			// }
			// else if(report_type == 3)
			// {
			// 	pdf = "hourly_sales_rep_pdf";
			// 	var formData = 'calendar_range='+$('#calendar_range').val()
			// 				+'&menu_cat_id=0'
			// 				+'&branch_id='+$("#branch_id").val();
			// }
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'gcRepJS'): ?>
		$("#menu_cat_id").change(function () {
			var menu_cat = $('select[name="menu_cat_id"] :selected').attr('branch_name')
			$("#branch_id").val(menu_cat);
			// alert(testing);
			$('#branch_id').attr("disabled", true); 
			if(menu_cat == undefined){
				// alert('as/dfljsao;dapj');
				$('#branch_id').attr("disabled", false); 
			}
		});

		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});  

		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var excel = "gc_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val()
			


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+excel+'?'+formData);
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
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "gc_rep_gen";
				

				$.post(baseUrl+'reporting/'+page,formData,function(data){
				// alert(data);
				// console.log(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
							console.log(data);
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
			var pdf = "gc_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'branchSalesStatusRepJS'): ?>
		$("#menu_cat_id").change(function () {
			var menu_cat = $('select[name="menu_cat_id"] :selected').attr('branch_name')
			$("#branch_id").val(menu_cat);
			// alert(testing);
			$('#branch_id').attr("disabled", true); 
			if(menu_cat == undefined){
				// alert('as/dfljsao;dapj');
				$('#branch_id').attr("disabled", false); 
			}
		});

		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);
				$('#menu_cat_id').select2();
				$('#menu_cat_id').select2('destroy');
				$('#menu_cat_id').children('option:not(:first)').hide();
				$('#menu_cat_id').children('option[branch_name='+branch_+']').show();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#menu_cat_id').select2();

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		}); 

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});

		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var excel = "branch_sales_upload_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&menu_cat_id='+$("#menu_cat_id").val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+excel+'?'+formData);
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
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "branch_sales_upload_gen";
				

				$.post(baseUrl+'reporting/'+page,formData,function(data){
				// alert(data);
				// console.log(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
							console.log(data);
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
			var pdf = "branch_sales_upload_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&branch_id='+$("#branch_id").val()
							+'&brand='+$("#brand").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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

	<?php elseif($use_js == 'collectionRepJS'): ?>
		//$('#daterange').daterangepicker({separator: ' to '});
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});

 		$("#branch_id").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ != ''){
				// alert(branch_);

				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').children('option:not(:first)').hide();
				$('#brand').children('option[branch_name='+branch_+']').show();
			}
			else{
				$('#brand').select2();
				$('#brand').select2('destroy');
				$('#brand').val('').change();
			}
		});

		$("#brand").click(function () {
			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		}); 

		$('#search-btn').click(function(){

			date = $('#calendar_range').val();
			branch_id = $('#branch_id').val();
			brand = $('#brand').val();
			// user = $('#user').val();
			json = 'false';
			// alert(date+''+user);

			var branch_ = $('select[name="branch_id"] :selected').val()
			if(branch_ == ''){
				// alert('Please select branch first.');
				rMsg('Please select branch first.','error');
				return false;
			}
			if(date != ""){

				$('#print-div').html('<center><div style="padding-top:20px"><i class="fa fa-spinner fa-2x fa-fw fa-spin aw"></i></div></center>');
				var this_url = baseUrl+'reporting/collection_rep_gen';
				//dr = $('#daterange').val();
				formData = 'calendar_range='+date+'&branch_id='+branch_id+'&brand='+brand+'&json='+json;
				$.post(this_url, formData, function(data){
					// alert(data);
					$('#print-div').html(data.code);
				// });
				},'json');
			}else{
				rMsg('Enter Date Range','error');
			}

			
		});

		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var excel = "collection_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&brand='+$('#brand').val()
							+'&branch_id='+$("#branch_id").val();
			

			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+excel+'?'+formData);
			}

			return false;
		});
		$('#tcpdf-btn').click(function(){			
			var report_type = $("#report_type").val();
			var pdf = "collection_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()
							+'&brand='+$('#brand').val()
							+'&branch_id='+$("#branch_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			}

			return false;
		});

	<?php elseif($use_js == 'menusRepHrJS'): ?>
		$('#print-box').hide();
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_code='+$('#branch_id').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/menus_rep_hrly_excel?'+formData);
			}

			return false;
		});
		$('#gen-rep').click(function(){
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_code='+$('#branch_id').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Please select branch first.','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				$.post(baseUrl+'reporting/menus_hourly_rep_gen',formData,function(data){
					// console.log(data);
					// alert(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
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
				},'json');	
				// });			
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
	<?php elseif($use_js == 'eSalesRepJS'): ?>
		$('#print-box').hide();
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;
			var formData = 'calendar_range_2='+$('#calendar_range_2').val()+'&branch_code='+$("#branch_code").val()+'&terminal_id='+$("#terminal_id").val();
			if($('#calendar_range_2').val() == ""){
				rMsg('Enter Date Range.','error');
			}else if($('#branch_code').val() == ""){
				rMsg('Please Select Branch.','error');
			}else{
				// $.rProgressBar();
				goTo('reporting/e_sales_excel?'+formData);
			}

			return false;
		});
		$('#gen-rep').click(function(){
			var formData = 'calendar_range_2='+$('#calendar_range_2').val()+'&branch_code='+$("#branch_code").val()+'&terminal_id='+$("#terminal_id").val();
			// alert(formData);
			if($('#calendar_range_2').val() == ""){
				rMsg('Enter Date Range','error');
			}else if($('#branch_code').val() == ""){
				rMsg('Please Select Branch.','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				$.post(baseUrl+'reporting/get_esales_reports',formData,function(data){
					// alert(data);
					console.log(data);
					$.rProgressBarEnd({
						onComplete : function(){
							btn.goLoad({load:false});
							var res = data;
							view_list(res);
							$('#view-list').click(function(){
								view_list(res);
								return false;
							});
							// $('#view-grid').click(function(){
							// 	view_grid(res);
							// 	return false;
							// });
							$('#pdf-btn').click(function(){
								// $('#print-div').print();

								var this_url = baseUrl+'reporting/esales_pdf';
								var formData = 'calendar_range_2='+$('#calendar_range_2').val()+'&branch_code='+$("#branch_code").val()+'&terminal_id='+$("#terminal_id").val();

								// item_id = $('#item_id').val();
								// dr = $('#daterange').val();
								// formData = 'enddate='+end_date+'&debtor_id='+debtor_id+'&summary='+summary;

								// window.location = this_url+'?'+formData;
								window.open(this_url+'?'+formData, '_blank');

								return false;
							});
						 }
					});
				},'json');	
				// });			
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

	<?php elseif($use_js == 'transiteRepJs'): ?>
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
			var this_url = baseUrl+'reporting/transite_rep_gen';
				date = $('#date').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				formData = 'calendar='+date+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&terminal_id='+terminal_id+'&json='+json;
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
			// var this_url = baseUrl+'prints/check_zreads';
				date = $('#date').val();
				user = $('#user').val();
				terminal_id = $('#terminal_id').val();
				branch_id = $('#branch_id').val();
				json = 'false';
				// alert(date+''+user);

				//dr = $('#daterange').val();
				// formData = 'calendar='+date+'&branch_id='+branch_id+'&terminal_id='+terminal_id+'&json='+json;
				// $.post(this_url, formData, function(data){
					console.log(data);
					$('#print-div').print();
					
				return false;
				// });
				// },'json');
		});

		$('#excel-btn').click(function(){
			var formData = 'calendar='+$('#date').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val(); 
			if($('#date').val() == ""){
				rMsg('Enter Date Range','error');
			}
			if($('#branch_id').val() == ""){
				rMsg('Select Branch','error');
			}
			else{
				// $.rProgressBar();
				goTo('reporting/excel_transite?'+formData);
			}

			return false;
		});
		<?php elseif($use_js == 'BadOrderRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			// var pdf = "bad_order_report_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val();
			// if(report_type == 1)
			// {
				excel = "bad_order_report_excel";
			


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				// alert($("#terminal_id").val());
				goTo('reporting/'+excel+'?'+formData);
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
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "bad_order_report_gen";

				// alert(page);
				// console.log(page);

				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var pdf = "bad_order_report_gen_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			}

			return false;
		});

		$('#receipt-btn').click(function(){			
			var report_type = $("#report_type").val();
			var pdf = "bad_order_report_receipt_pdf";

			var formData = 'calendar='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val()+'&terminal_id='+$("#terminal_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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
	<?php elseif($use_js == 'HalfPriceRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			// var pdf = "bad_order_report_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			// if(report_type == 1)
			// {
				excel = "half_price_report_excel";
			


			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				// alert($("#terminal_id").val());
				goTo('reporting/'+excel+'?'+formData);
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
			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "half_price_report_gen";

				// alert(page);
				// console.log(page);

				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var pdf = "half_price_report_gen_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
			}

			return false;
		});
		$('#receipt-btn').click(function(){			
			var report_type = $("#report_type").val();
			var pdf = "half_price_report_receipt_pdf";

			var formData = 'calendar='+$('#calendar_range').val()+'&branch_id='+$("#branch_id").val();
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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
 	
 	<?php elseif($use_js == 'newgcSalesRepJS'): ?>
		$('#print-box').hide();
		$(".timepicker").timepicker({
		    showInputs: false
		});
		$('#excel-btn').click(function(){
			// var values = res.tbl_vals;
			// var range = res.dates;

			var report_type = $("#report_type").val();
			var excel = "new_gc_rep_excel";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&gc_type='+$('#gc-type').val()+'&terminal_id='+$('#terminal_id').val();
			
			// var formData = 'calendar_range='+$('#calendar_range').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();
				goTo('reporting/'+excel+'?'+formData);
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
			var formData = 'calendar_range='+$('#calendar_range').val()+'&gc_type='+$('#gc-type').val()+'&terminal_id='+$('#terminal_id').val();
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				var btn = $(this);
				btn.goLoad();
				$.rProgressBar();
				var report_type = $("#report_type").val();
				var page = "new_gc_rep_gen";

				$.post(baseUrl+'reporting/'+page,formData,function(data){
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
			var report_type = $("#report_type").val();
			var pdf = "new_gc_rep_pdf";

			var formData = 'calendar_range='+$('#calendar_range').val()+'&gc_type='+$('#gc-type').val()+'&terminal_id='+$('#terminal_id').val();
			
			
			if($('#calendar_range').val() == ""){
				rMsg('Enter Date Range','error');
			}
			else{
				$.rProgressBar();				
				window.open(baseUrl+"reporting/"+pdf+"?"+formData, "popupWindow", "width=600,height=600,scrollbars=yes");
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