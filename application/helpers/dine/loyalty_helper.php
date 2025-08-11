<?php
function loyaltyPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'manager'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(12,'left',0,array('class'=>'manager-btns'));
					$buttons = array(
									 "card-lists"		=> fa('fa-list fa-lg fa-fw')."<br> Card Lists",
									 "cards"			=> fa('fa-credit-card fa-lg fa-fw')."<br> Add Card",
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
function cardsPage($det=array()){
	$CI =& get_instance();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',0,array('class'=>'no-padding no-margin','style'=>'padding-left:14px !important;'));
				$CI->make->sDiv(array('style'=>'margin:5px'));
				$CI->make->sBox('default',array('class'=>'box-solid'));
					$CI->make->sBoxBody();
						$CI->make->sDiv(array('id'=>'search-div','style'=>'min-height:200px;'));
							$CI->make->input(null,'search-customer',null,'Search Customer Name',array(),fa('fa-search'));
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
							$CI->make->sForm('loyalty/add_card_db',array('id'=>'card-form'));
								// $CI->make->hidden('trans_type','deposit');
		
								$CI->make->hidden('cust_id',iSetObj($det,'cust_id'),array('class'=>'rOkay','ro-msg'=>'No Customer.'));
								$CI->make->sDivRow();
									$CI->make->sDivCol(6);
										$CI->make->input('Customer','full_name',iSetObj($det,'full_name'),'Customer Name',array('class'=>'key-ins','readOnly'=>'readOnly'),fa('fa-user'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->input('Contact No.','contact_no',iSetObj($det,'contact_no'),'Type Phone No.',array('class'=>' key-ins','readOnly'=>'readOnly'),fa('fa-phone'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDivRow();
									$CI->make->sDivCol(6);
										$CI->make->textarea('Address','address',iSetObj($det,'address'),'Type Email Address',array('class'=>' key-ins','readOnly'=>'readOnly'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->input('Email','email',iSetObj($det,'email'),'Type Email Address',array('class'=>' key-ins','readOnly'=>'readOnly'),fa('fa-envelope'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();

							$CI->make->eForm();
						$CI->make->eDiv();
					$CI->make->eBoxBody();
					$CI->make->sBoxFoot();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'right');
								$CI->make->button(fa('fa-plus').' New Customer',array('id'=>'add-cust-btn','class'=>'manager-btn-teal half','style'=>'margin-right:10px;'));
								$CI->make->button(fa('fa-plus').' Generate New Card',array('id'=>'submit-btn','class'=>'manager-btn-green half'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eBoxFoot();
				$CI->make->eBox();
				$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	return $CI->make->code();
}
function cardsViewPage($det=array()){
	$CI =& get_instance();
	$CI->make->sDiv(array('id'=>'manager'));
		$CI->make->sDiv(array('class'=>'div-content','style'=>'background-color:#015D56'));
			
			$CI->make->sDivRow();
			$CI->make->sDivCol(6,'left',3);
				$CI->make->sBox('default',array('class'=>'box-solid','style'=>'margin-top:60px;'));
					$CI->make->sBoxBody();
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->H(4,"Card No.: ");
							$CI->make->eDivCol();
							$CI->make->sDivCol(9);
								$CI->make->H(4,iSetObj($det,'code'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->H(4,"Name: ");
							$CI->make->eDivCol();
							$CI->make->sDivCol(9);
								$CI->make->H(4,iSetObj($det,'fname')." ".iSetObj($det,'mname')." ".iSetObj($det,'lname')." ".iSetObj($det,'suffix'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->H(4,"Contact No.: ");
							$CI->make->eDivCol();
							$CI->make->sDivCol(9);
								$CI->make->H(4,iSetObj($det,'phone'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->H(4,"Email No.: ");
							$CI->make->eDivCol();
							$CI->make->sDivCol(9);
								$CI->make->H(4,iSetObj($det,'email'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eBoxBody();
					$CI->make->sBoxFoot();
						$CI->make->sDivRow();
							$CI->make->sDivCol(12,'right');
								$CI->make->button(fa('fa-reply').' Go back',array('id'=>'back-btn','class'=>'manager-btn-teal half'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eBoxFoot();
				$CI->make->eBox();
			$CI->make->eDivCol();
			$CI->make->eDivRow();

		$CI->make->eDiv();
	$CI->make->eDiv();	
	return $CI->make->code();
}
function cardsSearchForm(){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Card Code','code',null,null);
			$CI->make->eDivCol();
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