<?php
function custsBankPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'manager'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(12,'left',0,array('class'=>'manager-btns'));
					$buttons = array(
									 "deposit-lists"	=> fa('fa-bank fa-lg fa-fw')."<br> Customers Money",
									 "deposit-money"	=> fa('fa-inbox fa-lg fa-fw')."<br> Deposit",
									 );
					$CI->make->sDivRow();
						foreach ($buttons as $id => $text) {
								$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:0;'));
									$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block manager-btn-red double'));
								$CI->make->eDivCol();
						}
						$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:0'));
							$CI->make->button(fa('fa-sign-out fa-lg fa-fw')."<br> EXIT",array('id'=>'exit-btn','class'=>'btn-block manager-btn-red-gray double'));
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->sDiv(array('class'=>'div-content','style'=>'background-color:#015D56'));

			$CI->make->eDiv();
		$CI->make->eDiv();
	return $CI->make->code();
}
function depositPage($det=array()){
	$CI =& get_instance();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',0,array('class'=>'no-padding no-margin','style'=>'padding-left:14px !important;'));
				$CI->make->sDiv(array('style'=>'margin:5px'));
				$CI->make->sBox('default',array('class'=>'box-solid'));
					$CI->make->sBoxBody();
						$CI->make->sDiv(array('id'=>'search-div','style'=>'min-height:200px;'));
							$CI->make->input(null,'search-customer',null,'Search number or Customer Name',array(),fa('fa-search'));
							$CI->make->sDiv(array('class'=>'listings'));
								$CI->make->sUl(array('id'=>'cust-search-list','style'=>'height:250px;overflow:auto;'));
								$CI->make->eUl();
							$CI->make->eDiv();
						$CI->make->eDiv();
					$CI->make->eBoxBody();
				$CI->make->eBox();
				$CI->make->eDiv();
			$CI->make->eDivCol();
			$CI->make->sDivCol(8,'left',0,array());
				$CI->make->sDiv(array('style'=>'margin:5px'));
				$CI->make->sBox('default',array('class'=>'box-solid'));
					$CI->make->sBoxBody();
						$CI->make->sDiv(array('id'=>'details-div','style'=>'min-height:200px;'));
							$CI->make->sForm('custs_bank/deposit_db',array('id'=>'deposit-form'));
								$CI->make->hidden('trans_type','deposit');
								$CI->make->hidden('cust_id',iSetObj($det,'cust_id'));
		
								$CI->make->sDivRow();
									$CI->make->sDivCol(4,'left',0,array('style'=>'border-right:1px solid #ddd;'));
										// $CI->make->input(null,'fname',iSetObj($det,'fname'),'First Name',array('class'=>'rOkay key-ins','readonly'=>'readonly'));
										$CI->make->input('Customer','full_name',iSetObj($det,'full_name'),'Customer Name',array('class'=>'rOkay key-ins','readonly'=>'readonly'),fa('fa-user'));
										$CI->make->input('Contact No.','contact_no',iSetObj($det,'contact_no'),'Type Phone No.',array('class'=>'rOkay key-ins'),fa('fa-phone'));
										$CI->make->input('Email','email',iSetObj($det,'email'),'Type Email Address',array('class'=>'rOkay key-ins'),fa('fa-envelope'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(8);
										$CI->make->sDivRow();
											$CI->make->sDivCol(6);
												$opt = array('Cash'=>'cash','Credit Card'=>'credit');
												$CI->make->select('Type','amount_type',$opt,null,array());
												$opt2 = array('Master Card'=>'Master Card','VISA'=>'VISA','AmEx'=>'AmEx','Discover'=>'Discover');
												$CI->make->select('Card Type','card_type',$opt2,null,array('class'=>'key-ins for-cards'));
												$CI->make->input('Card No.','card_no',iSetObj($det,'card_no'),null,array('class'=>'key-ins for-cards'));
											$CI->make->eDivCol();
											$CI->make->sDivCol(6);
												$CI->make->input('Approval Code','approval_code',iSetObj($det,'approval_code'),null,array('class'=>'key-ins for-cards'));
												$CI->make->input('Amount','amount',iSetObj($det,'amount'),null,array('class'=>'rOkay key-ins','ro-msg'=>'Enter deposit Amount'),fa('fa-money'));
												$CI->make->textarea('Remarks','remarks',iSetObj($det,'remarks'),null,array('class'=>'key-ins','style'=>'height:80px;'));
											$CI->make->eDivCol();
										$CI->make->eDivRow();

									$CI->make->eDivCol();
								$CI->make->eDivRow();

							$CI->make->eForm();
						$CI->make->eDiv();
					$CI->make->eBoxBody();
					$CI->make->sBoxFoot();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'right');
								$CI->make->button(fa('fa-plus').' New Customer',array('id'=>'add-cust-btn','class'=>'manager-btn-teal half','style'=>'margin-right:10px;'));
								$CI->make->button(fa('fa-check').' Submit Deposit',array('id'=>'submit-btn','class'=>'manager-btn-green half'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eBoxFoot();
				$CI->make->eBox();
				$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	return $CI->make->code();
}
function customerDepositSearchForm(){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Customer Name','cust_name',null,null);
			$CI->make->eDivCol();
			// $CI->make->sDivCol(6);
			// 	$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
			// $CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
?>