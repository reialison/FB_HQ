<script>
$(document).ready(function(){
	<?php if($use_js == 'printReceiptJs'): ?>
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


		$('#search-btn').click(function(){
			if($('#branch_code').val() == ""){
				rMsg('Please select branch first.','error');
			}
			// console.log(data);
			// console.log($('#branch_code').val());
			var branch_code = $('#branch_code').val();
			var brand = $('#brand').val();
			$("#search-form").rOkay({
				btn_load		: 	$('#search-btn'),
				btn_load_remove	: 	true,
				// addData			: 	'change_db=main',
				asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										$("#results-div").html('');
										$("#results-div").html(data.code);
										$.each(data.ids,function(key,id){
											split_str = id.split('-');
											// view_div(id,branch_code,brand);
											view_div(id,split_str[1],split_str[2]);
										});
									}
			});
			return false;
		});
		$('#print-btn').click(function(){
			var id = $('#print-div').attr('ref-id');
			var btn = $(this);
			btn.goLoad();
			if(id != ""){
				var branch_code = $('#branch_code').val();
				var brand = $('#brand').val();

				$.post(baseUrl+'reprint/view/'+id+'/0/'+branch_code+'/'+brand,function(data){
					// $('#print-div').html(data);
					btn.goLoad({load:false});
				});
			}
			return false;
		});
		function view_div(id,branch_code,brand){
			$('#rec-'+id).click(function(){
				var btn = $(this);
				btn.goLoad();
				$('#print-div').html('');
				$('#print-div').attr('ref-id',id);
				// console.log(baseUrl+'reprint/view/'+id+'/'+branch_code);
				$.post(baseUrl+'reprint/view_branch/'+id+'/'+branch_code+'/1/'+brand,function(data){
					$('#print-div').html(data);
					btn.goLoad({load:false});
				});
				return false;
			});
		}

		<?php elseif($use_js == 'printReceiptAllJs'): ?>
		$('.daterangepicker').each(function(index){
 			if ($(this).hasClass('datetimepicker')) {
 				$(this).daterangepicker({separator: ' to ', timePicker: true, timePickerIncrement:15, format: 'YYYY/MM/DD h:mm A'});
 			} else {
 				$(this).daterangepicker({separator: ' to '});
 			}
 		});

 		$('#gen-rep').click(function(){
			$("#search-form").rOkay({
				btn_load		: 	$('#gen-rep'),
				btn_load_remove	: 	true,
				// addData			: 	'change_db=main',
				// asJson			: 	true,
				onComplete		:	function(data){
										// alert(data);
										$("#print-div").html('');
										$("#print-div").html(data);
										// $.each(data.ids,function(key,id){
										// 	view_div(id);
										// });
									}
			});
			return false;
		});

		$('#print-btn').click(function(){
			// var id = $('#print-div').attr('ref-id');
			var btn = $(this);
			btn.goLoad();
			// if(id != ""){
			formData = 'calendar_range='+$('#calendar_range').val()
						+ '&branch_code='+$('#branch_code').val()
						+ '&brand='+$('#brand').val();
						
			$.post(baseUrl+'reprint/printAll',formData,function(data){
				// $('#print-div').html(data);
				console.log(data);
				// rMsg('Receipts has been regenerated on C:/RECEIPT','success');
				btn.goLoad({load:false});
			});
			// }
			return false;
		});
		// $('#print-btn').click(function(){
		// 	var id = $('#print-div').attr('ref-id');
		// 	var btn = $(this);
		// 	btn.goLoad();
		// 	if(id != ""){
		// 		var branch_code = $('#branch_code').val();
		// 		var brand = $('#brand').val();

		// 		$.post(baseUrl+'reprint/view/'+id+'/0/'+branch_code+'/'+brand,function(data){
		// 			// $('#print-div').html(data);
		// 			btn.goLoad({load:false});
		// 		});
		// 	}
		// 	return false;
		// });

		$("#branch_code").change(function () {
			// alert('asdaqsda');
			var branch_ = $('select[name="branch_code"] :selected').val()
			if(branch_ != ''){

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
			var branch_ = $('select[name="branch_code"] :selected').val();

			if(branch_ == ''){
				alert('Please select branch first.');
			}

			return false
		});  
	<?php endif; ?>
});
</script>