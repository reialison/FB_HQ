<?php
function couponsPage($list = array())
{
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('success');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12,'right');
							$CI->make->A(fa('fa-plus').' Add New Coupon',base_url().'coupons/form',array('class'=>'btn btn-primary'));
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array(
								// 'Code'=>'',
								'Card Number'=>'',
								'Amount'=>'',
								'Expiration'=>'',
								'Is Inactive?'=>'',
								''=>array('width'=>'10%','align'=>'right')
							);
							$rows = array();
							foreach ($list as $val) {
								$link = "";
								// $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'items/setup/'.$res->item_id,array('class'=>'btn blue btn-outline','return'=>'true'));
								$link .= $CI->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'coupons/form/'.$val->coupon_id,array('class'=>'btn blue btn-sm btn-outline','return'=>'true','title'=>'Edit "'.$val->card_no.'", "'.$val->amount.'"'));
								$rows[] = array(
									$val->card_no,
									number_format($val->amount, 2, '.', ','),
									sql2Date($val->card_no),
									($val->inactive == 0 ? 'No' : 'Yes'),
									$link
								);
							}
							$CI->make->listLayout($th,$rows);
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function couponsForm($det=array(),$coupon_id=null)
{
	$CI =& get_instance();
	$CI->make->sBox('primary');
		$CI->make->sBoxBody();
			$CI->make->sForm("coupons/db",array('id'=>'coupons_form'));
				if (!empty($coupon_id)) {
					$CI->make->hidden('coupon_id',$coupon_id);
				}

				$CI->make->sDivRow(array('style'=>'margin:10px;'));
					$CI->make->sDivCol(12);
						// $CI->make->hidden('tax_id',iSetObj($det,'tax_id'));
						$CI->make->input('Card Number','card_no',iSetObj($det,'card_no'),'Type Gift Card Code',array('class'=>'rOkay'));
						$CI->make->input('Amount','amount',iSetObj($det,'amount'),'Type Amount',array());
						$date = null;
						if(iSetObj($det,'expiration') != "")
							$date = sql2Date(iSetObj($det,'expiration'));
						$CI->make->date('Expiration','expiration',$date,null,array());
						$CI->make->inactiveDrop('Is Inactive?','inactive',iSetObj($det,'inactive'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
				
				// $CI->make->append('<br/>');
				
				$CI->make->sDivRow(array('style'=>'margin:10px;'));
					$CI->make->sDivCol(4,'left');
						$CI->make->button(fa('fa-save').' Save Coupon',array('id'=>'save-btn','class'=>''),'success');
					// $CI->make->sDivCol(4);
						$CI->make->A(fa('fa-reply').' Go Back',base_url().'coupons',array('id'=>'goback-btn','class'=>'btn btn-primary'));
					$CI->make->eDivCol();
					// $CI->make->eDivCol();
			    $CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eBoxBody();
	$CI->make->eBox();

	return $CI->make->code();
}