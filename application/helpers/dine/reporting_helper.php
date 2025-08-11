<?php
function menusRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(3);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker datetimepicker','style'=>'position:initial;'),fa('fa-calendar'));
						$CI->make->eDivCol();
	                	// $CI->make->sDivRow(array('id'=>'branch-div'));
							$CI->make->sDivCol(3);							
								$CI->make->branchesDrop('Branch','branch_code',null,'Select Branch');
							$CI->make->eDivCol();
	                	// $CI->make->eDivRow();
							$CI->make->sDivCol(3);							
								$CI->make->brandDrop('Brand','brand',null,'Select Brand');
							$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->button(fa('fa-refresh').' Generate Report',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							$CI->make->button(fa('fa-file-excel-o').' Generate as Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid','style'=>'overflow-x: auto;'));
				$CI->make->sBoxHead();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
					$CI->make->eDiv();
					
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
						$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function hourlyRep(){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					//$CI->make->sForm("reprint/results",array('id'=>'search-form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								// $CI->make->date('Date','date',date('m/d/Y'),null,array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
								$CI->make->input('Date Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);							
								$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
							$CI->make->eDivCol();

							$CI->make->sDivCol(3);							
								$CI->make->brandDrop('Brand','brand',null,'Select Brand');
							$CI->make->eDivCol();
							// $CI->make->sDivCol(3);
							// 	$CI->make->userDrop('User','user',null,null);
							// $CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->button(fa('fa-search').' Submit',array('style'=>'margin-top:24px;','id'=>'search-btn','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol(12);
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();
					$CI->make->button(fa('fa-print').' Print',array('id'=>'print-btn','class'=>'pull-right btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'),'success');
				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					$CI->make->sDiv(array('id'=>'print-div','style'=>'margin:0 auto;position:relative;width:700px;'));
						
					//$CI->make->append('asdfafasdfasdfsadf');
					$CI->make->eDiv();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}

function salesRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker datetimepicker','style'=>'position:initial;'),fa('fa-calendar'));
							// $CI->make->date("Date","date",null,"Select Date",array("class"=>"rOkay"));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							// $CI->make->button(fa('fa-file-excel-o').' Export to Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;'),'success');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
					// $CI->make->sDivRow();
					// 	$CI->make->sDivCol(2);							
					// 		 $CI->make->time('Start Time','start_time',null,'Start Time');
					// 	$CI->make->eDivCol();
					// 	$CI->make->sDivCol(2);
					// 		$CI->make->time('End Time','end_time',null,'End Time');
					// 	$CI->make->eDivCol();
     //            	$CI->make->eDivRow();
                	$CI->make->sDivRow();
						$CI->make->sDivCol(4);							
							$CI->make->reportTypeDrop('Report Type','report_type',null,'Select Type',array("id"=>"report_type", 'class'=>'rOkay'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
					$CI->make->sDivRow(array('id'=>'category-div'));
						$CI->make->sDivCol(4);							
							$CI->make->salesRep_menuCategoriesDrop('Category','menu_cat_id',null,'Select Category',array("id"=>"menu_cat_id", 'class'=>'select2-allow-clear'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'brand-div'));
						$CI->make->sDivCol(4);							
							$CI->make->terminalDrop('Terminal','terminal_id',"",null,array('class'=>'','style'=>'position:initial'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

      //           	$CI->make->sDivRow(array('id'=>'brand-div'));
						// $CI->make->sDivCol(4);							
						// 	$CI->make->brandDrop('Brand','brand',null,'Select Brand');
						// $CI->make->eDivCol();
      //           	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					// $CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
					// 	$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
					// 	$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					// $CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function dtrRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
							// $CI->make->date("Date","date",null,"Select Date",array("class"=>"rOkay"));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							// $CI->make->button(fa('fa-file-excel-o').' Export to Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;'),'success');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
					$CI->make->sDivRow();
						$CI->make->sDivCol(2);							
							 $CI->make->time('Start Time','start_time',null,'Start Time');
						$CI->make->eDivCol();
						$CI->make->sDivCol(2);
							$CI->make->time('End Time','end_time',null,'End Time');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();					
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
						$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function MonthToMonthReportUi(){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					//$CI->make->sForm("reprint/results",array('id'=>'search-form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->date('Date','date',date('m/d/Y'),null,array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);							
								$CI->make->branchesDrop('Branch','branch_id',null,'All Branch');
							$CI->make->eDivCol();

							$CI->make->sDivCol(3);							
								$CI->make->brandDrop('Brand','brand',null,'Select Brand');
							$CI->make->eDivCol();
							
							$CI->make->sDivCol(2);							
								$CI->make->button(fa('fa-print').' Print',array('id'=>'generate','class'=>'pull-left','style'=>'margin-top:24px;'),'success');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					// $CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));						
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();		
	return $CI->make->code();
}
function YearToDateReportUi(){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					//$CI->make->sForm("reprint/results",array('id'=>'search-form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(2);								
								$CI->make->yearDrop($label="From",$nameID="from",$value=date("Y"),$placeholder=null,$params=array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(2);								
								$CI->make->yearDrop($label="To",$nameID="to",$value=date("Y"),$placeholder=null,$params=array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);							
								$CI->make->branchesDrop('Branch','branch_id',null,'All Branch');
							$CI->make->eDivCol();

							$CI->make->sDivCol(3);							
								$CI->make->brandDrop('Brand','brand',null,'Select Brand');
							$CI->make->eDivCol();
							
							$CI->make->sDivCol(1);							
								$CI->make->button(fa('fa-print').' Print',array('id'=>'generate','class'=>'pull-left','style'=>'margin-top:24px;'),'success');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					// $CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));						
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();		
	return $CI->make->code();
}
function itemRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
							// $CI->make->date("Date","date",null,"Select Date",array("class"=>"rOkay"));
						$CI->make->eDivCol();
						$CI->make->sDivCol(3);							
								$CI->make->branchesDrop('Branch','branch_id',null,'All Branch');
							$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							// $CI->make->button(fa('fa-file-excel-o').' Export to Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;'),'success');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();					
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					// $CI->make->sDivCol(4);
					// 	$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					// $CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
						$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function voidedSalesRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                
					$CI->make->sDivRow(array('id'=>'category-div'));
						$CI->make->sDivCol(4);							
							$CI->make->salesRep_menuCategoriesDrop('Category','menu_cat_id',null,'Select Category',array("id"=>"menu_cat_id", 'class'=>'select2-allow-clear'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'brand-div'));
						$CI->make->sDivCol(4);							
							$CI->make->brandDrop('Brand','brand',null,'Select Brand');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					// $CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
					// 	$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
					// 	$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					// $CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function issuesStampRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->yearDrop($label="Year",$nameID="year",$value=date("Y"),$placeholder=null,$params=array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
						$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function promoRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->discountDrop('Promo/Discount','discount_id',null,'Select Promo/Discount');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
						$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function brandRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
							// $CI->make->date("Date","date",null,"Select Date",array("class"=>"rOkay"));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							// $CI->make->button(fa('fa-file-excel-o').' Export to Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;'),'success');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
					// $CI->make->sDivRow();
					// 	$CI->make->sDivCol(2);							
					// 		 $CI->make->time('Start Time','start_time',null,'Start Time');
					// 	$CI->make->eDivCol();
					// 	$CI->make->sDivCol(2);
					// 		$CI->make->time('End Time','end_time',null,'End Time');
					// 	$CI->make->eDivCol();
     //            	$CI->make->eDivRow();
      //           	$CI->make->sDivRow();
						// $CI->make->sDivCol(4);							
						// 	$CI->make->reportTypeDrop('Report Type','report_type',null,'Select Type',array("id"=>"report_type", 'class'=>'rOkay'));
						// $CI->make->eDivCol();
      //           	$CI->make->eDivRow();
					$CI->make->sDivRow(array('id'=>'category-div'));
						$CI->make->sDivCol(4);							
							$CI->make->salesRep_menuCategoriesDrop('Category','menu_cat_id',null,'Select Category',array("id"=>"menu_cat_id", 'class'=>'select2-allow-clear'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                	$CI->make->sDivRow(array('id'=>'brand-div'));
						$CI->make->sDivCol(4);							
							$CI->make->brandDrop('Brand','brand',null,'Select Brand');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					// $CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
					// 	$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
					// 	$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					// $CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function gcRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
							// $CI->make->date("Date","date",null,"Select Date",array("class"=>"rOkay"));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							// $CI->make->button(fa('fa-file-excel-o').' Export to Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;'),'success');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
					

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'brand-div'));
						$CI->make->sDivCol(4);							
							$CI->make->brandDrop('Brand','brand',null,'Select Brand');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					// $CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
					// 	$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
					// 	$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					// $CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function salesUploadRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
							// $CI->make->date("Date","date",null,"Select Date",array("class"=>"rOkay"));
						$CI->make->eDivCol();
						$CI->make->sDivCol(4);
							$CI->make->button(fa('fa-refresh').' Generate',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							// $CI->make->button(fa('fa-file-excel-o').' Export to Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;'),'success');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
					

                	$CI->make->sDivRow(array('id'=>'branch-div'));
						$CI->make->sDivCol(4);							
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();

                	$CI->make->sDivRow(array('id'=>'brand-div'));
						$CI->make->sDivCol(4);							
							$CI->make->brandDrop('Brand','brand',null,'Select Brand');
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();					
					$CI->make->sDivCol(4);
						$CI->make->input("","search",null,"Search...",array("id"=>"search"),fa("fa-search"));
					$CI->make->eDivCol();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					// $CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
					// 	$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
					// 	$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					// $CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div','style'=>'overflow-x:auto'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function collectionRep(){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					//$CI->make->sForm("reprint/results",array('id'=>'search-form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								// $CI->make->date('Date','date',date('m/d/Y'),null,array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
								$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker datetimepicker','style'=>'position:initial;'),fa('fa-calendar'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);							
								$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
							$CI->make->eDivCol();

							$CI->make->sDivCol(3);							
								$CI->make->brandDrop('Brand','brand',null,'Select Brand');
							$CI->make->eDivCol();
							// $CI->make->sDivCol(3);
							// 	$CI->make->userDrop('User','user',null,null);
							// $CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->button(fa('fa-search').' Submit',array('style'=>'margin-top:24px;','id'=>'search-btn','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol(12);
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						// $CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'tcpdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
						$CI->make->button(fa('fa-file-excel-o').' Excel',array('id'=>'excel-btn','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					$CI->make->sDiv(array('id'=>'print-div','style'=>'margin:0 auto;position:relative;width:700px;'));
						
					//$CI->make->append('asdfafasdfasdfsadf');
					$CI->make->eDiv();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function menusRepHourly($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range',null,null,array('class'=>'rOkay daterangepicker datetimepicker','style'=>'position:initial;'),fa('fa-calendar'));
						$CI->make->eDivCol();
						$CI->make->sDivCol(3);
							$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
						$CI->make->eDivCol();
						$CI->make->sDivCol(5);
							$CI->make->button(fa('fa-refresh').' Generate Report',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							$CI->make->button(fa('fa-file-excel-o').' Generate as Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				// $CI->make->sBoxHead();
				// 	$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
				// 		$CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn'),'warning');
				// 	$CI->make->eDiv();
					
				// 	$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
				// 		$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
				// 		$CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
				// 	$CI->make->eDiv();

				// $CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					// $CI->make->sBox('solid',array('id'=>'print-box'));
					//     $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					//     $CI->make->eBoxBody();
					// $CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function esalesRep($list = null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4);
							$CI->make->input('Date & Time Range','calendar_range_2',null,null,array('class'=>'rOkay daterangepicker','style'=>'position:initial;'),fa('fa-calendar'));
						$CI->make->eDivCol();
						// if(CONSOLIDATOR){
		                	// $CI->make->sDivRow();
								$CI->make->sDivCol(3);		
								$CI->make->branchesDrop('Branch','branch_code',null,'Select Branch');					
						// 			$CI->make->posTerminalsDrop('Terminal','terminal_id',"",null,array('class'=>'','style'=>'position:initial'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(2);							
								$CI->make->terminalDrop('Terminal','terminal_id',"",null,array('class'=>'','style'=>'position:initial'));
							$CI->make->eDivCol();
		                	// $CI->make->eDivRow();
	     //            	}else{
	     //            		// $CI->make->sDivRow();
						// 		$CI->make->hidden('terminal_id',TERMINAL_ID);
		    //             	// $CI->make->eDivRow();
	     //            	}
						$CI->make->sDivCol(5);
							$CI->make->button(fa('fa-refresh').' Generate Report',array('id'=>'gen-rep','style'=>'margin-top:24px;margin-right:10px;','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							$CI->make->button(fa('fa-file-excel-o').' Generate as Excel',array('id'=>'excel-btn','style'=>'margin-top:24px;','class'=>'btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-print').' PDF',array('id'=>'pdf-btn','class'=>'btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11'));
					$CI->make->eDiv();
					
					$CI->make->sDiv(array('class'=>'btn-group pull-right','role'=>'group','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
						$CI->make->button(fa('fa-table fa-lg'),array('id'=>'view-list','class'=>'listyle-btns'));
						// $CI->make->button(fa('fa-bar-chart fa-lg'),array('id'=>'view-grid','class'=>'listyle-btns'));
					$CI->make->eDiv();

				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					
					$CI->make->sBox('solid',array('id'=>'print-box'));
					    $CI->make->sBoxBody(array('class'=>'no-padding'));
					        $CI->make->sDivRow();
					            $CI->make->sDivCol(12);
									$CI->make->sDiv(array('id'=>'print-div','class'=>'ejournal-table'));
									$CI->make->eDiv();
					            $CI->make->eDivCol();
					        $CI->make->eDivRow();
					    $CI->make->eBoxBody();
					$CI->make->eBox();
									
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function transiteRepPage(){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					//$CI->make->sForm("reprint/results",array('id'=>'search-form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->date('Date','date',null,null,array('class'=>'rOkay','style'=>'position:initial;'),null,fa('fa-calendar'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);
								$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
								// $CI->make->userDrop('User','user',null,null);
							$CI->make->eDivCol();
							// if(CONSOLIDATOR){
			    //             	// $CI->make->sDivRow();
							// 		$CI->make->sDivCol(2);							
							// 			$CI->make->terminalDrop('Terminal','terminal_id',"",null,array('class'=>'','style'=>'position:initial'));
							// 		$CI->make->eDivCol();
			    //             	// $CI->make->eDivRow();
		     //            	}else{
		     //            		// $CI->make->sDivRow();
							// 		$CI->make->hidden('terminal_id',TERMINAL_ID);
			    //             	// $CI->make->eDivRow();
		     //            	}
							$CI->make->sDivCol(2);
								$CI->make->button(fa('fa-search').' Submit',array('style'=>'margin-top:24px;','id'=>'search-btn','class'=>'btn rounded-pill btn-primary-100 text-primary-600 radius-8 px-20 py-11'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol(12);
			$CI->make->sBox('default',array('class'=>'box-solid'));
				$CI->make->sBoxHead();
					$CI->make->button(fa('fa-print').' Print',array('id'=>'print-btn','class'=>'pull-right btn rounded-pill btn-warning-100 text-warning-600 radius-8 px-20 py-11','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'));
					$CI->make->button(fa('fa-print').' Excel',array('id'=>'excel-btn','class'=>'pull-right btn rounded-pill btn-lilac-100 text-lilac-600 radius-8 px-20 py-11','style'=>'margin-top:10px;margin-right:10px;margin-bottom:10px;'),'success');
				$CI->make->eBoxHead();
				$CI->make->sBoxBody(array('class'=>'bg-gray','style'=>'min-height:50px;'));
					$CI->make->sDiv(array('id'=>'print-div','style'=>'margin:0 auto;position:relative;width:310px;'));
						
					//$CI->make->append('asdfafasdfasdfsadf');
					$CI->make->eDiv();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}