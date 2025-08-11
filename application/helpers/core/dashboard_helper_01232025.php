<?php
function dashboardMain($lastGT=0,$todaySales=0,$todayTransNo=0,$lsu=array(),$ts=array()){
	$CI =& get_instance();
	// $CI->make->sDiv(array('style'=>'width:100%;background-color:#fff;padding-top:15px;padding-left:10px;padding-right:10px;'));
	// 	$opts = array('Today'=>'today','This Month'=>'monthly','This Year'=>'yearly');
	// 	$CI->make->sDivRow();
	// 	$CI->make->sDivRow();
	// 		$CI->make->sDivCol(3,'right',9);
	// 			$CI->make->select(null,'show-drop',$opts);
	// 		$CI->make->eDivCol();
	// 	$CI->make->eDivRow();
	// $CI->make->eDiv();
	$CI->make->sDiv(array('style'=>'width:100%;background-color:#ffffff;margin-top:-20px'));
		$CI->make->sDiv(array('style'=>'padding:10px;'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(3, "left", 9);							
					$CI->make->branchesDrop('','branch_id',null,'All Branch');
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			################################################
			########## BOXES
			################################################
			$CI->make->sDivRow();
				$CI->make->sDivCol(6);
			    	$CI->make->sDiv(array('class'=>'dashboard-stat blue'));
			        	$CI->make->tdiv(fa('fa-desktop'),array('class'=>'visual'));
			        	$CI->make->sDiv(array('class'=>'details','id'=>'last-gt-box'));

			        		$CI->make->tdiv(num($lastGT),array( 'class'=>'number','id'=>"last-gt","style"=>" font-size:20px"));
			        		$CI->make->tdiv('Last Grand Total',array('class'=>'desc',"style"=>" font-size:20px"));

			        	$CI->make->eDiv();
			        	$CI->make->sDiv(array('class'=>'more','id'=>''));
			        	$CI->make->eDiv();
			        $CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(6);
					$CI->make->sDiv(array('class'=>'dashboard-stat red'));
				    	$CI->make->tdiv(fa('icon-calculator'),array('class'=>'visual'));
				    	$CI->make->sDiv(array('class'=>'details','style'=>'width:80%'));
					     $CI->make->sDivRow();
								// $CI->make->sDivCol();
									$CI->make->sDiv(array('class'=>'table-responsive',"style"=>"height: 100px;"));
									$CI->make->sTable(array('class'=>'table','id'=>'','style'=>'margin-top:5px;'));
										$CI->make->sRow();

											$CI->make->th('Branch',array('style'=>'font-size:15px;border-top:0;padding:0;color:#fff;font-weight:bold;'));
											$CI->make->th('Today Sales',array('style'=>'font-size:15px;border-top:0;padding:0;color:#fff;;font-weight:bold;text-align:center'));
											foreach ($ts as $tot_sale => $total_sales) {
											$CI->make->sRow();
												// switch ($total_sales->branch_code) {
												// 	case 'MAX':
												// 		$total_sales->branch_code = "MAX'S SUMULONG";
												// 		break;
												// 	case 'YELLOWCAB':
												// 		$total_sales->branch_code = "YELLOW CAB SUMULONG";
												// 		break;
												// 	default:
												// 		$total_sales->branch_code = "MAX'S MB MAIN AVE.";
												// 		break;
												// }
												$CI->make->td($total_sales->branch_name,array('style'=>'font-size:14px;border-top:0;padding:0;color:#fff;font-weight:bold;width:200px;'));
												$CI->make->td(num($total_sales->today_sales),array('style'=>'font-size:14px;border-top:0;padding:0;color:#fff;font-weight:300;width: 120px;text-align:center'));
											$CI->make->eRow();
											}
										$CI->make->eRow();
									$CI->make->eTable();
									$CI->make->eDiv();
							$CI->make->eDivRow();
				    		
				    		// $CI->make->tdiv('Today Sales',array('class'=>'desc',"style"=>" font-size:20px"));
				    	$CI->make->eDiv();
				    	$CI->make->sDiv(array('class'=>'more','id'=>''));
			        	$CI->make->eDiv();
				    $CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(6);
					$CI->make->sDiv(array('class'=>'dashboard-stat  green'));
						$CI->make->tdiv(fa('fa-users'),array('class'=>'visual '));
						$CI->make->sDiv(array('class'=>'details',"style"=>"width: 80%;"));
							$CI->make->tdiv(num($todayTransNo),array('class'=>'number',"id"=>"total_transaction","style"=>" font-size:20px"));
							$CI->make->tdiv('Total Transactions',array('class'=>'desc',"style"=>" font-size:20px"));
						$CI->make->eDiv();
						$CI->make->sDiv(array('class'=>'more','id'=>''));
			        	$CI->make->eDiv();
					$CI->make->eDiv();
				$CI->make->eDivCol();
				// $CI->make->sDivCol(3);
			 //    	$CI->make->sDiv(array('class'=>'info-box'));
			 //        	$CI->make->span(fa('fa-cutlery'),array('class'=>'info-box-icon  bg-blue'));
			 //        	$CI->make->sDiv(array('class'=>'info-box-content'));
			 //        		$CI->make->span('Today\'s Top Menu',array('class'=>'info-box-text'));
			 //        		$CI->make->span('Congee',array('class'=>'info-box-number'));
			 //        	$CI->make->eDiv();
			 //        $CI->make->eDiv();
				// $CI->make->eDivCol();
				$CI->make->sDivCol(6);
					$CI->make->sDiv(array('class'=>'dashboard-stat purple','style'=>'line-height:17px;word-spacing:-2px;'));
						$CI->make->tdiv(fa('fa-calendar'),array('class'=>'visual'));
						$CI->make->sDiv(array('class'=>'details',"style"=>"width: 80%;"));
							$CI->make->sDivRow();
								// $CI->make->sDivCol();
									$CI->make->sDiv(array('class'=>'table-responsive',"style"=>"height: 100px;"));
									$CI->make->sTable(array('class'=>'table','id'=>'','style'=>'margin-top:5px;'));
										$CI->make->sRow();

											$CI->make->th('Branch',array('style'=>'font-size:16px;border-top:0;padding:0;color:#fff;font-weight:bold;width:200px;'));
											$CI->make->th('Last Sales Update',array('style'=>'font-size:16px;border-top:0;padding:0;color:#fff;;font-weight:bold;width:200px;'));
											foreach ($lsu as $sales_updated => $last_update) {
												// switch ($last_update->branch_code) {
												// 	case 'MAX':
												// 		$last_update->branch_code = "MAX'S SUMULONG";
												// 		break;
												// 	case 'YELLOWCAB':
												// 		$last_update->branch_code = "YELLOW CAB SUMULONG";
												// 		break;
												// 	default:
												// 		$last_update->branch_code = "MAX'S MB MAIN AVE.";
												// 		break;
												// }
											$CI->make->sRow();
												$CI->make->td($last_update->branch_name,array('style'=>'font-size:10px;border-top:0;padding:0;color:#fff;font-weight:bold'));
												$CI->make->td(date('m-d-Y &\nb\sp;&\nb\sp;&\nb\sp; h:i a', strtotime($last_update->mdate)),array('style'=>'font-size:11px;border-top:0;padding:0;color:#fff;font-weight:300;width: 120px;'));
											$CI->make->eRow();
											}
										$CI->make->eRow();
									$CI->make->eTable();
									$CI->make->eDiv();
							$CI->make->eDivRow();
						$CI->make->eDiv();
						$CI->make->sDiv(array('class'=>'more','id'=>''));
			        	$CI->make->eDiv();
					$CI->make->eDiv();
				$CI->make->eDivCol();

			$CI->make->eDivRow();
			################################################
			########## GRAPHS
			################################################
			$CI->make->sDivRow();
				
				$CI->make->sDivCol(6);
					$CI->make->sBox('default',array('class'=>''));
						$CI->make->sBoxHead(array('class'=>''));
							// $CI->make->h(4,fa('icon-pin').' Today\'s Top Menu',array('class'=>'caption-subject font-green bold '));
							$CI->make->span(fa('icon-pin').' Today\'s Top Menus', array('class'=>'caption-subject bold uppercase font-green-haze'));
						$CI->make->eBoxHead();
							// 									$CI->make->append('<div class="overlay">
							//   <i class="fa fa-refresh fa-spin"></i>
							// </div>');
						// $CI->make->sBoxBody(array('class'=>'chart-responsive no-padding'));
							$CI->make->sDiv(array('id'=>'top-menu-box','class'=>'table-scrollable','style'=>'min-height:238px;'));
							$CI->make->eDiv();
						// $CI->make->eBoxBody();
					$CI->make->eBox();
				$CI->make->eDivCol();

				$CI->make->sDivCol(6);
					$CI->make->sBox('default',array('class'=>''));
						$CI->make->sBoxHead(array('class'=>''));
							$CI->make->span(fa('icon-pin').' Top Ten Items', array('class'=>'caption-subject bold uppercase font-green-haze'));
						$CI->make->eBoxHead();
							$CI->make->sDiv(array('id'=>'top-topping-box','class'=>'table-scrollable','style'=>'min-height:238px;'));
							$CI->make->eDiv();
					$CI->make->eBox();	
				$CI->make->eDivCol();												
			$CI->make->eDivRow();
					
				// $CI->make->sDiv();
				// $CI->make->eDiv();				
			$CI->make->sDivRow();				
				$CI->make->sDivCol(5);
					$CI->make->sBox('solid');
							$CI->make->sBoxHead(array('class'=>''));
								// $CI->make->h(3,fa('icon-calculator').' Today\'s Sales',array('class'=>'caption-subject bold uppercase font-green-haze'));
								$CI->make->span(fa('icon-calculator').' Monthly Sales', array('class'=>'caption-subject bold uppercase font-green-haze'));
							$CI->make->eBoxHead();
							$CI->make->sBoxBody();
								// $CI->make->sDivRow(array('class'=>'chart-responsive no-padding'));
								// $CI->make->yearDrop('Year','year_id',null,null,array('class'=>'rOkay','style'=>'position:initial;'),fa('fa-calendar'));
									// $CI->make->sDivCol(12);
										$CI->make->sDiv(array('id'=>'echarts_bar','style'=>'height:250px;'));
										$CI->make->eDiv();
									// $CI->make->eDivCol();
								// $CI->make->eDivRow();
							$CI->make->eBoxBody();
						$CI->make->eBox();
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->append('
	                                <div class="portlet light bordered">
	                                    <div class="portlet-title">
	                                        <div class="caption">
	                                            <i class="icon-bar-chart font-green-haze"></i>
	                                            <span class="caption-subject bold uppercase font-green-haze"> Store</span>
	                                            <span class="caption-helper">Sales Pie Chart</span>
	                                        </div>
	                                        <div class="tools">
	                                            <a href="javascript:;" class="collapse"> </a>                                                
	                                            <a href="javascript:;" class="fullscreen"> </a>                                                
	                                        </div>
	                                    </div>
	                                    <div class="portlet-body">
	                                        <div id="chart_7" class="chart" style="height: 250px;"> </div>                                            
	                                        
	                                    </div>
	                                </div>');
							
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->append('
                                <div class="portlet light bordered" id="blockui_category_portlet_body">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-green-haze"></i>
                                            <span class="caption-subject bold uppercase font-green-haze"> Category</span>
                                            <span class="caption-helper">Sales Category</span>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse"> </a>
                                            <a href="javascript:;" class="fullscreen"> </a>                                                
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div id="chart_5" class="chart" style="height: 250px;"></div>                                            
                                    </div>
                                </div>');
				$CI->make->eDivCol();
			$CI->make->eDivRow();

			$CI->make->sDivRow();				
				$CI->make->sDivCol(6);
					$CI->make->append("<div class='portlet light bordered'>
	                                        <div class='portlet-title'>
	                                            <div class='caption'>
	                                                <i class='icon-bar-chart font-green-haze'></i>
	                                                <span class='caption-subject bold uppercase font-green-haze'> Year To Date</span>
	                                                <span class='caption-helper'>Sales Analytics</span>
	                                            </div>
	                                            <div class='tools'>
	                                                <a href='javascript:;'' class='collapse'> </a>	                                                
	                                                <a href='javascript:;' class='fullscreen'> </a>
	                                                <a href='javascript:;' class='remove'> </a>
	                                            </div>
	                                        </div>
	                                        <div class='portlet-body'>
	                                            <div id='chart_1' class='chart' style='height: 250px;'> </div>
	                                        </div>
	                                    </div>");
				$CI->make->eDivCol();
				$CI->make->sDivCol(6);
					$CI->make->append("<!-- BEGIN SAMPLE TABLE PORTLET-->
                            <div class='portlet light bordered'>
                                <div class='portlet-title'>
                                    <div class='caption'>
                                        <i class='icon-bar-chart font-green'></i>
                                        <span class='caption-subject font-green bold uppercase'>Month To Month</span>
                                    </div>
                                    <div class='tools'>
	                                    <a href='javascript:;'' class='collapse'> </a>
	                                    <a href='javascript:;'' class='fullscreen'> </a>                                                
	                                </div>                                    
                                </div>
                                <div class='portlet-body' style='height:255px;'>
                                	<input type='date' id='m2m_date' value='".date("Y-m-d")."'/>
                                    <div class='table-scrollable' id='m2m-tbl'>
                                        <table class='table table-hover'>
                                           
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- END SAMPLE TABLE PORTLET-->");
				$CI->make->eDivCol();
			$CI->make->eDivRow();	
							
		$CI->make->eDiv();
	$CI->make->eDiv();

	return $CI->make->code();
}
?>