<?php
function dashboardMain($lastGT=0,$todaySales=0,$todayTransNo=0,$lsu=array(),$ts=array(),$branch_latest_upload=array(),$menus=array(),$count_co=0,$count_fr=0){
	$CI =& get_instance();
	
	$CI->make->sDiv(array('style'=>'width:100%;background-color:#ffffff;margin-top:-20px'));
		$CI->make->sDiv(array('class'=>'row-body','style'=>'padding:10px;'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(3, "right", 9, array('style'=>'margin-top:-50px;color:#434d5c;'));							
					$CI->make->branchesDrop('','branch_id',null,'All Branch',array('style'=>'display:none'));
					// $CI->make->span('LLAOLLAO-PH');
					$img_path = 'uploads/logo/1.png';
					$CI->make->img($img_path, ['style' => 'width: 100px;']);
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			################################################
			########## BOXES
			################################################
			$CI->make->sDivRow(array("class"=>"card-main"));
				$CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-1','id'=>''));
						$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
							$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
								$CI->make->sDiv(array('class'=>'mt-10','id'=>''));
									$CI->make->tdiv('Total Gross Sales',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
									// $CI->make->tdiv(num($lastGT),array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"last-gt"));
									$CI->make->tdiv("0.00",array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"last-gt"));
									// $CI->make->P(' <span class="text-success-main" id="gt-percent">%</span> from last month', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
								$CI->make->eDiv();
								$CI->make->sDiv(array('class' => 'w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center','id' => ''));
									$CI->make->tdiv(fa('fa-desktop'),array('class'=>'visual mt-10'));
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
					$CI->make->eDiv();
				$CI->make->eDivCol();

                $CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-2 ','id'=>''));
						$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
							$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
								$CI->make->sDiv(array('class'=>'mt-10','id'=>''));
									$CI->make->tdiv('Total Net Sales',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
									$CI->make->tdiv("0.00",array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"net-sales"));
									// $CI->make->P(' <span class="text-success-main" id="net-percent">%</span> from last month', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
								$CI->make->eDiv();
								$CI->make->sDiv(array('class' => 'w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center','id' => ''));
                                    $CI->make->tdiv(fa('fa-users'),array('class'=>'visual mt-10'));
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
					$CI->make->eDiv();
				$CI->make->eDivCol();

                $CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-3','id'=>''));
						$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
							$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
								$CI->make->sDiv(array('class'=>'mt-10','id'=>''));
									$CI->make->tdiv('Total Net Sales of Vat',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
									$CI->make->tdiv("0.00",array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"vat-sales"));
									// $CI->make->P(' <span class="text-success-main" id="vat-percent">0.00</span> Last 30 days users', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
								$CI->make->eDiv();
								$CI->make->sDiv(array('class' => 'w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center','id' => ''));
									$CI->make->tdiv(fa('fa-money'),array('class'=>'visual mt-10'));
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
					$CI->make->eDiv();
				$CI->make->eDivCol();
                $CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-5','id'=>''));
						$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
							$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
								$CI->make->sDiv(array('class'=>'mt-10','id'=>''));
									$CI->make->tdiv('Total Discounts',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
									$CI->make->tdiv("0.00",array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"discount-sales"));
									// $CI->make->P(' <span class="text-success-main" id ="discount-percent">0.00</span> Last 30 days expense', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
								$CI->make->eDiv();
								$CI->make->sDiv(array('class' => 'w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center','id' => ''));
									$CI->make->tdiv(fa('fa-exchange'),array('class'=>'visual mt-10'));
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
					$CI->make->eDiv();
				$CI->make->eDivCol();

                $CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-4','id'=>''));
						$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
							$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
								$CI->make->sDiv(array('class'=>'mt-10','id'=>''));
									$CI->make->tdiv('Transaction Count',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
									$CI->make->tdiv("0.00",array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"tcount-sales"));
									// $CI->make->P(' <span class="text-success-main" id="tcount-percent">0.00</span> Last 30 days expense', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
								$CI->make->eDiv();
								$CI->make->sDiv(array('class' => 'w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center','id' => ''));
									$CI->make->tdiv(fa('fa-credit-card'),array('class'=>'visual mt-10'));
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
					$CI->make->eDiv();
				$CI->make->eDivCol();

                

                $CI->make->sDivCol(4);
					$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-1','id'=>''));
						$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
							$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
								$CI->make->sDiv(array('class'=>'mt-10','id'=>'',"style"=>""));
									$CI->make->tdiv('Average Check',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
									$CI->make->tdiv("0",array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"avg-sales"));
									// $CI->make->P(' <span class="text-success-main" id="avg-percent">0.00</span> Last 30 days expense', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
								$CI->make->eDiv();
								$CI->make->sDiv(array('class' => 'w-50-px h-50-px rounded-circle d-flex justify-content-center align-items-center','style' => 'background:#FACC15!important'));
                                    $CI->make->tdiv(fa('fa-bar-chart'),array('class'=>'visual mt-10'));
								$CI->make->eDiv();
							$CI->make->eDiv();
						$CI->make->eDiv();
						
					$CI->make->eDiv();
				$CI->make->eDivCol();

				
				// $CI->make->sDivCol(6);
				// 	$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-3'));
				// 		$CI->make->sDiv(array('class'=>'card-body p-20','id'=>''));
				// 			$CI->make->sDiv(array('class'=>'d-flex flex-wrap align-items-center justify-content-between gap-3','id'=>''));
				// 				$CI->make->sDiv(array('class'=>'mt-10','id'=>''));
				// 					$CI->make->tdiv('Today Transactions',array('class'=>'fw-medium text-primary-light mb-1 font-family'));
				// 					$CI->make->tdiv(num($todayTransNo),array( 'class'=>'h6 mb-0','style'=>'color: var(--text-primary-light) !important; font-weight: 600 !important;','id'=>"total_transaction"));
				// 					$CI->make->P(' <span class="text-success-main"><iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> +5000</span> Last 30 days transaction', array('class' => 'fw-medium  text-primary-light mt-12 mb- mt-20'));
				// 				$CI->make->eDiv();
				// 				$CI->make->sDiv(array('class' =>' w-50-px h-50-px bg-purple  rounded-circle d-flex justify-content-center align-items-center','id' => ''));
				// 					$CI->make->tdiv(fa('fa-users'),array('class'=>'visual '));
				// 				$CI->make->eDiv();
				// 			$CI->make->eDiv();
				// 		$CI->make->eDiv();
				// 	$CI->make->eDiv();
				// $CI->make->eDivCol();
				// $CI->make->sDivCol(6);
				// 	$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-2','style'=>'line-height:17px;word-spacing:-2px;'));
				// 		$CI->make->sDiv(array('class'=>'card-body p-20'));
				// 			$CI->make->sDiv(array('class'=>'details','style'=>'','id'=>'ts'));
				// 			$CI->make->eDiv();
				// 			$CI->make->sDiv(array('class'=>'more','id'=>''));
				// 			$CI->make->eDiv();
				// 		$CI->make->eDiv();
				// 	$CI->make->eDiv();
				// $CI->make->eDivCol();
			
				// $CI->make->sDivCol(6);
				// 	$CI->make->sDiv(array('class'=>'card mt-10 shadow-none border bg-gradient-start-4','style'=>'line-height:17px;word-spacing:-2px;'));
				// 		$CI->make->sDiv(array('class'=>'card-body p-20',"style"=>"",'id'=>'lsu'));
				// 			$CI->make->sDiv(array('class'=>'details',"style"=>"",'id'=>'lsu'));
				// 			$CI->make->eDiv();
				// 			$CI->make->sDiv(array('class'=>'more','id'=>''));
				// 			$CI->make->eDiv();
				// 		$CI->make->eDiv();
						
						
				// 	$CI->make->eDiv();
				// $CI->make->eDivCol();
			// foreach ($branch_latest_upload as $sales_updated => $last_update) {
				// echo $last_update->branch_code;
			// }
			// die();
			$CI->make->eDivRow();
			################################################
			########## GRAPHS
			################################################
                                // <select class="form-select bg-base form-select-sm w-auto">
                                //     <option>Yearly</option>
                                //     <option>Monthly</option>
                                //     <option>Weekly</option>
                                //     <option>Today</option>
                                // </select>
						
                            // <div class="d-flex flex-wrap align-items-center gap-2 mt-8">
                            //     <h6 class="fw-semibold mt-0 mb-0">$27,200</h6>
                            //     <span class="text-sm mt-0 fw-semibold rounded-pill bg-success-focus text-success-main border br-success px-8 py-4 line-height-1 d-flex align-items-center gap-1">
                            //         10% <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                            //     </span>
                            //     <span class="text-xs mt-0 fw-medium">+ $1500 Per Day</span>
                            // </div>
			$CI->make->append(		'
			
            <div class="row">
                <div class="col-xxl-6 col-xl-10 col-lg-8 col-sm-6" >
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h6 class="text-lg ">Sales Performance</h6>
                            </div>
                            <div id="chart_sales" class="pt-28 apexcharts-tooltip-style-1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-2 col-lg-4 col-sm-6">
                    <div class="card h-100 radius-8 border">
                        <div class="card-body p-24">
                            <h6 class="mb-12 fw-semibold text-lg mb-16">Company-Owned Weekly Performance</h6>
                            <div class="d-flex align-items-center gap-2 mb-20">
                                <h6 class="fw-semibold mb-0 mt-0"></h6>
                                
                            </div>

                            <div id="barChart1" class="barChart" style="margin-top:40px"></div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row"  style="margin-top:20px;">
                <div class="col-xxl-3 col-xl-2 col-lg-8 col-sm-6">
                    <div class="card h-100 radius-8 border-0 overflow-hidden">
                        <div class="card-body p-24">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                                <h6 class="mb-2 fw-bold text-lg">Total Sales Performance</h6>
                                
                            </div>

                            <div id="chart_7" class="chart" style="height: 300px;"> </div>

                            

                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-2 col-lg-4 col-sm-6">
                    <div class="card h-100 radius-8 border">
                        <div class="card-body p-24">
                            <h6 class="mb-12 fw-semibold text-lg mb-16">Franchisee Weekly Performance</h6>
                            <div class="d-flex align-items-center gap-2 mb-20">
                                <h6 class="fw-semibold mb-0 mt-0"></h6>
                                
                            </div>

                            <div id="barChart22" class="barChart"></div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:20px;">
                <div class="col-xxl-9 col-xl-12 col-lg-8 col-sm-12">
                    <div class="card h-100" id="table_hide_scroll">');
                        // $CI->make->H(4,'Company Owned :'.$count_co.   '&nbsp&nbsp&nbsp&nbspFranchisee :'.$count_fr,array('style'=>'text-align:right'));
                        $CI->make->H(4, '<strong>Company Owned:</strong> '.$count_co.'&nbsp;&nbsp;&nbsp;&nbsp;<strong>Franchisee:</strong> '.$count_fr, array('style'=>'text-align:right;'));
                        $CI->make->append('<div class="card-body p-24">
                            <div class="d-flex flex-wrap align-items-center gap-1 justify-content-between mb-16">
                                <ul class="nav border-gradient-tab nav-pills mb-0" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link d-flex align-items-center active" id="pills-to-do-list-tab" data-bs-toggle="pill" data-bs-target="#pills-to-do-list" type="button" role="tab" aria-controls="pills-to-do-list" aria-selected="true">
                                            Branch Sales Update
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="fade active show" id="pills-to-do-list" role="tabpanel" aria-labelledby="pills-to-do-list-tab" tabindex="0">
                                    <div class="table-responsive scroll-sm">
                                        <table class="table bordered-table sm-table mb-0 datatable" id="branch_sales_table" style="border-color:white;">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Branch</th>
                                                    <th scope="col">Company</th>
                                                    <th scope="col">Update Date</th>
                                                    <th scope="col" class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>');

                                            	foreach ($branch_latest_upload as $sales_updated => $t) {
                                            		// echo '<pre>', print_r($last_update->branch_code), '</pre>';die();
                                            		if($t->company_owned == 0){
                                            		    $cown = 'Company Owned';
                                            		}else{
                                            		    $cown = 'Franchisee';
                                            		}
													$CI->make->append('<tr>');
                                            			$CI->make->append('<td><span>'.$t->branch_code.'</span></td>');
                                            			$CI->make->append('<td><span>'.$cown.'</span></td>');
                                            			$CI->make->append('<td><span>'.date('m-d-Y', strtotime($t->mdate)).'</span></td>');
                                            			$CI->make->append('<td class="text-center"><span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>');
                                            			$CI->make->append('</td>');
                                            		$CI->make->append('</tr>');
                                            	}
                                            	$CI->make->append('
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="fade" id="pills-recent-leads" role="tabpanel" aria-labelledby="pills-recent-leads-tab" tabindex="0">
                                    <div class="table-responsive scroll-sm">
                                        <table class="table bordered-table sm-table mb-0" style="border-color:white;">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Users 2</th>
                                                    <th scope="col">Registered On</th>
                                                    <th scope="col">Plan</th>
                                                    <th scope="col" class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/users/user1.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                            <div class="flex-grow-1">
                                                                <h6 class="text-md mb-0 fw-medium">Dianne Russell</h6>
                                                                <span class="text-sm text-secondary-light fw-medium">redaniel@gmail.com</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>27 Mar 2024</td>
                                                    <td>Free</td>
                                                    <td class="text-center">
                                                        <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/users/user2.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                            <div class="flex-grow-1">
                                                                <h6 class="text-md mb-0 fw-medium">Wade Warren</h6>
                                                                <span class="text-sm text-secondary-light fw-medium">xterris@gmail.com</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>27 Mar 2024</td>
                                                    <td>Basic</td>
                                                    <td class="text-center">
                                                        <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/users/user3.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                            <div class="flex-grow-1">
                                                                <h6 class="text-md mb-0 fw-medium">Albert Flores</h6>
                                                                <span class="text-sm text-secondary-light fw-medium">seannand@mail.ru</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>27 Mar 2024</td>
                                                    <td>Standard</td>
                                                    <td class="text-center">
                                                        <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/users/user4.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                            <div class="flex-grow-1">
                                                                <h6 class="text-md mb-0 fw-medium">Bessie Cooper </h6>
                                                                <span class="text-sm text-secondary-light fw-medium">igerrin@gmail.com</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>27 Mar 2024</td>
                                                    <td>Business</td>
                                                    <td class="text-center">
                                                        <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/users/user5.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                            <div class="flex-grow-1">
                                                                <h6 class="text-md mb-0 fw-medium">Arlene McCoy</h6>
                                                                <span class="text-sm text-secondary-light fw-medium">fellora@mail.ru</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>27 Mar 2024</td>
                                                    <td>Enterprise </td>
                                                    <td class="text-center">
                                                        <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-12 col-lg-4 col-sm-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between" style="flex-direction:column">
                                <h6 class="mb-2 fw-bold text-lg mb-0">Top Selling Items</h6>
                            </div>

                            <div class="mt-32">

                            	');

                            	foreach ($menus as $menu => $m) {
                            		// echo '<pre>', print_r($last_update->branch_code), '</pre>';die();
									$CI->make->append('<div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">');
                            			$CI->make->append('<h6 class="text-md mb-0 fw-medium">'.$m->menu_name.'</h6>
                        				</div>
                                    </div>');
                            			$CI->make->append('<span class="text-primary-light text-md fw-medium">'.num($m->total_amount,2).'</span>');
                            		$CI->make->append('</div>');
                            	}
                                // <div class="d-flex align-items-center justify-content-between gap-3 mb-24">
                                //     <div class="d-flex align-items-center">
                                //         <div class="flex-grow-1">
                                //             <h6 class="text-md mb-0 fw-medium">Dianne Russell</h6>
                                //         </div>
                                //     </div>
                                //     <span class="text-primary-light text-md fw-medium">$20</span>
                                // </div>
                            	$CI->make->append('


                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!--<div class="row" style="margin-top:20px;">
                <div class="col-xxl-6 col-xl-12 col-lg-6 col-sm-12 ">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                                <h6 class="mb-2 fw-bold text-lg mb-0">Top Countries</h6>
                                
                            </div>

                            <div class="row gy-4 mt-22">
                                <div class="col-lg-6">
                                    <div id="world-map" class="h-100 border radius-8"></div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="h-100 border p-16 pe-0 radius-8" style="padding:10px;">
                                        <div class="max-h-266-px overflow-y-auto scroll-sm pe-16">
                                            <div class="d-flex align-items-center justify-content-between gap-3 mb-12 pb-2">
                                                <div class="d-flex align-items-center w-100">
                                                    <img src="assets/images/flags/flag1.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-sm mb-0">USA</h6>
                                                        <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <div class="w-100 max-w-66 ms-auto">
                                                        <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-primary-600 rounded-pill" style="width: 80%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-secondary-light font-xs fw-semibold">80%</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between gap-3 mb-12 pb-2">
                                                <div class="d-flex align-items-center w-100">
                                                    <img src="assets/images/flags/flag2.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-sm mb-0">Japan</h6>
                                                        <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <div class="w-100 max-w-66 ms-auto">
                                                        <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-orange rounded-pill" style="width: 60%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-secondary-light font-xs fw-semibold">60%</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between gap-3 mb-12 pb-2">
                                                <div class="d-flex align-items-center w-100">
                                                    <img src="assets/images/flags/flag3.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-sm mb-0">France</h6>
                                                        <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <div class="w-100 max-w-66 ms-auto">
                                                        <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-yellow rounded-pill" style="width: 49%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-secondary-light font-xs fw-semibold">49%</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between gap-3 mb-12 pb-2">
                                                <div class="d-flex align-items-center w-100">
                                                    <img src="assets/images/flags/flag4.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-sm mb-0">Germany</h6>
                                                        <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <div class="w-100 max-w-66 ms-auto">
                                                        <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-success-main rounded-pill" style="width: 100%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-secondary-light font-xs fw-semibold">100%</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between gap-3 mb-12 pb-2">
                                                <div class="d-flex align-items-center w-100">
                                                    <img src="assets/images/flags/flag5.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-sm mb-0">South Korea</h6>
                                                        <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <div class="w-100 max-w-66 ms-auto">
                                                        <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-info-main rounded-pill" style="width: 30%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-secondary-light font-xs fw-semibold">30%</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="d-flex align-items-center w-100">
                                                    <img src="assets/images/flags/flag1.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-sm mb-0">USA</h6>
                                                        <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <div class="w-100 max-w-66 ms-auto">
                                                        <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-primary-600 rounded-pill" style="width: 80%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-secondary-light font-xs fw-semibold">80%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-6 col-lg-6 col-sm-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                                <h6 class="mb-2 fw-bold text-lg mb-0">Generated Content</h6>
                                
                            </div>

                            <ul class="d-flex flex-wrap align-items-center mt-3 gap-3">
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-12-px rounded-circle bg-primary-600"></span>
                                    <span class="text-secondary-light text-lg fw-normal">Word:
                                        <span class="text-primary-light fw-semibold">500</span>
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-12-px rounded-circle bg-yellow"></span>
                                    <span class="text-secondary-light text-lg fw-normal">Image:
                                        <span class="text-primary-light fw-semibold">300</span>
                                    </span>
                                </li>
                            </ul>

                            <div class="mt-40">
                                <div id="paymentStatusChart" class="margin-16-minus"></div>
                            </div>

                        </div>
                    </div>
                    </div>
                </div>
            </div> -->
			'
		 );	
							
		$CI->make->eDiv();
	$CI->make->eDiv();

	return $CI->make->code();
}

function last_update_date($lsu){
	$CI =& get_instance();
	
	$CI->make->sDivRow(array("class"=>'card-table'));
		$CI->make->sDiv(array('class'=>'table-responsive',"style"=>"height: 100px;"));
			$CI->make->sTable(array('class'=>'table','id'=>'','style'=>'margin-top:5px;'));
				$CI->make->sRow();
					$CI->make->th('Branch',array('style'=>'font-size:16px;border-top:0;padding:0;color:black;font-weight:bold;width:200px;'));
					$CI->make->th('Last Sales Update',array('style'=>'font-size:16px;border-top:0;padding:0;color:black;;font-weight:bold;width:200px;'));
					foreach ($lsu as $sales_updated => $last_update) {
						$CI->make->sRow();
							$CI->make->td($last_update->branch_name,array('style'=>'font-size:10px;border-top:0;padding:0;color:black;font-weight:bold'));
							$CI->make->td(date('m-d-Y &\nb\sp;&\nb\sp;&\nb\sp; h:i a', strtotime($last_update->mdate)),array('style'=>'font-size:11px;border-top:0;padding:0;color:black;font-weight:300;width: 120px;'));
						$CI->make->eRow();
					}
				$CI->make->eRow();
			$CI->make->eTable();
			
		$CI->make->eDiv();
		$CI->make->sDiv(array('class' =>' w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center','id' => ''));
			$CI->make->tdiv(fa('fa-calendar'),array('class'=>'visual mt-10'));
		$CI->make->eDiv();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function today_sales($ts){
	$CI =& get_instance();

	$CI->make->sDivRow(array('class'=>'card-table'));
		$CI->make->sDiv(array('class'=>'table-responsive',"style"=>"height: 100px;"));
			$CI->make->sTable(array('class'=>'table','id'=>'','style'=>'margin-top:5px;'));
				$CI->make->sRow();
					$CI->make->th('Branch',array('style'=>'font-size:16px;border-top:0;padding:0;color:black;font-weight:bold;width:200px;'));
					$CI->make->th('Today Sales',array('style'=>'font-size:16px;border-top:0;padding:0;color:black;;font-weight:bold;width:200px;'));
					foreach ($ts as $tot_sale => $total_sales) {
						$CI->make->sRow();
							$CI->make->td($total_sales->branch_name,array('style'=>'font-size:10px;border-top:0;padding:0;color:black;font-weight:bold'));
							$CI->make->td(num($total_sales->today_sales),array('style'=>'font-size:11px;border-top:0;padding:0;color:black;font-weight:300;width: 120px;'));
						$CI->make->eRow();
					}
				$CI->make->eRow();
			$CI->make->eTable();
		$CI->make->eDiv();
		$CI->make->sDiv(array('class' =>' w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center','id' => ''));
			$CI->make->tdiv(fa('icon-calculator'),array('class'=>'visual mt-10'));
		$CI->make->eDiv();
	$CI->make->eDivRow();

	return $CI->make->code();
}
?>