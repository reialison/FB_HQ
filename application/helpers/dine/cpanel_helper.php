<?php
function cpanel($user=array(),$set=array(),$today=null){
	$CI =& get_instance();
	$CI->make->sDiv(array('id'=>'cpanel'));
		$CI->make->sDivRow();
			$CI->make->sDivCol(12);
				#TOP-BTNS	
					$CI->make->sDivRow();
						$CI->make->sDivCol(12);
							$CI->make->sDiv(array('id'=>'top-btns-con'));
								$CI->make->sDivRow();
									$CI->make->sDivCol(2,0,'left',array('style'=>'padding:3px;'));
										$CI->make->button(fa('fa-user fa-lg').' Manager',array('class'=>'btn-block btn-red-gray'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(2,0,'left',array('style'=>'padding:3px;'));
										$CI->make->button(fa('fa-cogs fa-lg').' Back Office',array('class'=>'btn-block btn-red-gray'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(4,0,'left',array('style'=>'padding:3px;'));
										$CI->make->sDiv(array('id'=>'top-datetime'));
											$CI->make->sDivRow();
												$CI->make->sDivCol(9);
													$CI->make->img($user['img'],array('style'=>'height:40px;padding-left:5px;padding-right:10px;;float:left;','class'=>'img-circle'));
													$CI->make->H(5,$user['full_name'],array('style'=>'font-size:14px;'));
												$CI->make->eDivCol();
												$CI->make->sDivCol(3,0,'left',array('style'=>'padding-top:3px;'));
													$CI->make->sDiv(array('id'=>'time','class'=>'text-center'));
													$CI->make->eDiv();
													$CI->make->H(5,$today,array("style"=>"font-size:14px;margin:0px;","class"=>'text-center'));
												$CI->make->eDivCol();
											$CI->make->eDivRow();
										$CI->make->eDiv();
									$CI->make->eDivCol();
									$CI->make->sDivCol(1,0,'left',array('style'=>'padding:3px;'));
										$CI->make->button(fa('fa-inbox fa-2x'),array('class'=>'btn-block btn-orange'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(2,0,'left',array('style'=>'padding:3px;'));
										$CI->make->button(fa('fa-clock-o fa-lg').' Time Clock',array('class'=>'btn-block btn-red-gray'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(1,0,'left',array('style'=>'padding:3px;'));
										$CI->make->button(fa('fa-power-off fa-2x'),array('class'=>'btn-block btn-red'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				#BODY
					$CI->make->sDiv(array('id'=>'cpanel-body'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(2);
								$CI->make->sDiv(array('class'=>'cpanel-side-box'));
									$CI->make->H(5,'NEW ORDER',array('class'=>'headline text-center','style'=>'margin-bottom:10px;'));
									$ids = explode(',',$set->controls);
									foreach($ids as $value){
										$text = explode('=>',$value);
										if($text[0] == 1){
											$texts='dine-in';
										}else{
											$texts=$text[1];
										}
										$CI->make->button(strtoupper($text[1]),array('id'=>$texts.'-btn','class'=>'btn-block btn-blue'));
									}								
								$CI->make->eDiv();
							$CI->make->eDivCol();
							$CI->make->sDivCol(8);
								$CI->make->sDiv(array('class'=>'cpanel-center-box','style'=>'background-color:#333;'));
								$CI->make->eDiv();
							$CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->sDiv(array('class'=>'cpanel-side-box'));
									$CI->make->sDivRow();
										$CI->make->sDivCol(12,0,'left',array('style'=>'padding:3px;'));
											$CI->make->sDiv(array('style'=>'padding:8px;background-color:#fff'));
												$CI->make->button(fa('fa-search fa-lg').' Search',array('class'=>'btn-block btn-blue'));
											$CI->make->eDiv();
										$CI->make->eDivCol();
									$CI->make->eDivRow();
									$CI->make->sDivRow();
										$CI->make->sDivCol(12,0,'left',array('style'=>'padding:3px;'));
											$CI->make->button('Customers',array('class'=>'btn-block btn-green'));
										$CI->make->eDivCol();
										$CI->make->sDivCol(12,0,'left',array('style'=>'padding:3px;'));
											$CI->make->button('Customer Deposits',array('class'=>'btn-block btn-green'));
										$CI->make->eDivCol();
										$CI->make->sDivCol(12,0,'left',array('style'=>'padding:3px;'));
											$CI->make->button('Gift Cards',array('class'=>'btn-block btn-green'));
										$CI->make->eDivCol();
									$CI->make->eDivRow();
								$CI->make->eDiv();
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eDiv();
				
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eDiv();
	return $CI->make->code();
}	
?>