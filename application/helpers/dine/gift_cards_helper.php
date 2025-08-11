<?php
function gift_cards_display($list = array())
{
	$CI =& get_instance();

	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('success');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
							$CI->make->sDivCol(6,'left');
									$CI->make->append('<button id="rtable-btn1-btn" class="btn btn-circle green btn-outline" style="margin-right:6px;"><i class="fa fa-upload "></i> Upload</button>');
									$CI->make->append('<button id="rtable-btn2-btn" class="btn btn-circle green btn-outline" style="margin-right:6px;"><i class="fa fa-filter "></i> Filter</button>');
							$CI->make->eDivCol();
							// $CI->make->sDivCol(6,'right');
							// $CI->make->eDivCol();
						// $CI->make->sDivCol(12,'right');
						// 	$CI->make->A(fa('fa-plus').' Add New Gift Card',base_url().'gift_cards/gift_cards_setup',array('class'=>'btn btn-primary'));
						// $CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array(
								// 'Code'=>'',
								'Card Number'=>'',
								'Amount'=>'',
								'Is Used?'=>'',
								''=>array('width'=>'10%','align'=>'right')
							);
							$rows = array();
							foreach ($list as $val) {
								$link = "";
								// $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'items/setup/'.$res->item_id,array('class'=>'btn blue btn-outline','return'=>'true'));
								$link .= $CI->make->A(fa('fa-edit fa-lg').' Edit',base_url().'gift_cards/gift_cards_setup/'.$val->gc_id,array('class'=>'btn blue btn-outline','return'=>'true','title'=>'Edit "'.$val->card_no.'", "'.$val->amount.'"'));
								$rows[] = array(
									$val->card_no,
									number_format($val->amount, 2, '.', ','),
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
function gift_cards_form_container($gc_id)
{
	$CI =& get_instance();

	$CI->make->sDivRow();
		$CI->make->hidden('gc_idx',$gc_id);
		$CI->make->sDivCol(12);
			$CI->make->sTab();
				$tabs = array(
					"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'gift_cards',array('return'=>true)),
					fa('fa-info-circle')." General Details" => array('href'=>'#details','class'=>'tab_link','load'=>'gift_cards/gift_cards_load','id'=>'details_link'),
				);
				$CI->make->tabHead($tabs,null,array());
				$CI->make->sTabBody();
					$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
					$CI->make->eTabPane();
				$CI->make->eTabBody();
			$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function gift_cards_details_form($det, $gc_id)
{
	$CI =& get_instance();

	$CI->make->sForm("gift_cards/gift_cards_details_db",array('id'=>'gift_cards_details_form'));
		if (!empty($gc_id)) {
			$CI->make->hidden('gc_id',$gc_id);
		}

		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(12);
				// $CI->make->hidden('tax_id',iSetObj($det,'tax_id'));
				$CI->make->input('Description Code','description_id',iSetObj($det,'description_id'),'Type Description Code',array('class'=>'rOkay'));
				$CI->make->input('Brand','brand_id',iSetObj($det,'brand_id'),'Type Brand',array('class'=>'rOkay'));
				$CI->make->input('Card Number','card_no',iSetObj($det,'card_no'),'Type Gift Card Code',array('class'=>'rOkay'));
				// $CI->make->input('Amount','amount',iSetObj($det,'amount'),'Type Amount',array('disabled'=>'disabled'));
				$CI->make->input('Amount','amount',iSetObj($det,'amount'),'Type Amount',array('readonly'=>'readonly'));
				$CI->make->inactiveDrop('Is Used?','inactive',iSetObj($det,'inactive'),array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
		$CI->make->eDivRow();
		
		// $CI->make->append('<br/>');
		
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(4);
			$CI->make->eDivCol();
			$CI->make->sDivCol(4, 'right');
				$CI->make->button(fa('fa-save').' Save Gift Card Details',array('id'=>'save-btn','class'=>'btn-block'),'primary');
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
			$CI->make->eDivCol();
	    $CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
function giftCardsPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('id'=>'gc-cashier'));
		
			$CI->make->sDivRow();
	
				/*GIFT CARDS CASHIER*/
				$CI->make->sDivCol(12,'left',0,array('class'=>'gc-cashier-btns'));
					
					$buttons = array("new-gift-card"	=> fa('fa-gift fa-lg fa-fw')."<br> NEW GIFT CARD",
									 "look-up"	=> fa('fa-search fa-lg fa-fw')."<br> LOOK-UP",
									 // "disable-card"	=> fa('fa-ban fa-lg fa-fw')."<br> DISABLE CARD",
									 );
					$CI->make->sDivRow();
					foreach ($buttons as $id => $text) {
							$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:10px;'));
								$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block gc-cashier-btn-red double'));
							$CI->make->eDivCol();
					}
						$CI->make->sDivCol(4,'left',0,array("style"=>'margin-bottom:10px;'));
							$CI->make->button(fa('fa-sign-out fa-lg fa-fw')."<br> EXIT",array('id'=>'exit-btn','class'=>'btn-block gc-cashier-btn-red-gray double'));
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDivCol();
			$CI->make->eDivRow();		
			
			//-----NUMPAD AND GIFT CARD DETAILS-----start
			$CI->make->sDivRow();
				$CI->make->sDivCol(3,'left',0,array('id'=>'loginPin','class'=>'gc-cashier-left'));
					$CI->make->append(onScrNumPad('cardno','cardno-login'));
				$CI->make->eDivCol();
				
				$CI->make->sDivCol(8);
					$CI->make->sDiv(array('class'=>'gc-cashier-center'));
						//----- GIFT CARD DETAILS-----START
							$CI->make->sDiv(array('class'=>'gc_content_div'));
							
							$CI->make->eDiv();	
						//----- GIFT CARD DETAILS-----END
					$CI->make->eDiv();	
				$CI->make->eDivCol();
			$CI->make->eDivRow();		
			//-----NUMPAD AND  GIFT CARD DETAILS-----end

			//-----KEYBOARD-----start
			$CI->make->sDivRow();
				$CI->make->sDivCol(12,'left',0,array('class'=>'gc-cashier-btns'));
					$CI->make->sDiv(array('id'=>'wrap'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			//-----KEYBOARD-----end
			
		$CI->make->eDiv();	
	return $CI->make->code();	
}
function giftCardsList($list = array())
{
	$CI =& get_instance();
	
	$CI->make->sForm("gift_cards/gift_cards_details_db",array('id'=>'gift_cards_details_form'));
		if (!empty($gc_id)) {
			$CI->make->hidden('gc_id',$gc_id);
		}
		
		// $CI->make->sDivRow(array('style'=>'margin:10px; padding-top:10px;'));
		$CI->make->sDivRow();
			$CI->make->sDivCol(12);
				$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
					$CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
						$CI->make->sRow();
	    					$CI->make->th('Card Number');
	    					$CI->make->th('Amount');
	    					$CI->make->th('Is Inactive');
	    					$CI->make->th();
	    				$CI->make->eRow();
						foreach ($list as $val) {
    						$CI->make->sRow(array('id'=>'row-'.$val->gc_id));
    							$CI->make->td($val->card_no);
    							$CI->make->td(number_format($val->amount, 2, '.', ','));
    							$CI->make->td(($val->inactive ? 'Yes' : 'No'));
    							$a = $CI->make->A(fa('fa-pencil fa-fw fa-lg'),'#',array('class'=>'edit-line', 'ref'=>$val->gc_id, 'cardnoref'=>$val->card_no, 'return'=>true));
            					$CI->make->td($a);
    						$CI->make->eRow();
    					}
					$CI->make->eTable();
				$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	
	return $CI->make->code();
}
function giftCheckUploadForm(){
	$CI =& get_instance();
	// $CI->make->H(3,'Warning! THIS WILL REPLACE ALL THE MENUS.',array('class'=>'label label-warning','style'=>'margin-bottom:50px;font-size:24px;'));
	// $CI->make->sForm('menu/upload_excel_db',array('id'=>"upload-form",'enctype'=>'multipart/form-data'));
	$CI->make->sForm('gift_cards/upload_excel_db',array('id'=>"upload-form",'enctype'=>'multipart/form-data'));
		$CI->make->sDivRow(array('style'=>''));
			$CI->make->sDivCol(12,'left');
				$CI->make->append('<button id="gift_card_template" class="btn-sm btn blue" type="button" style="margin-right:6px;"><i class="fa fa-download "></i> Download Template</button>');
			$CI->make->eDivCol();
		$CI->make->eDivRow();
		$CI->make->sDivRow(array('style'=>'margin-top:20px;'));

			$CI->make->sDivCol(6);
				$CI->make->file('menu_excel',array());
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function giftFilterForm(){
	$CI =& get_instance();
	$CI->make->sForm('gift_cards/index',array('id'=>"filter-form",'enctype'=>'multipart/form-data'));
		$CI->make->sDivRow(array('style'=>''));
		$CI->make->eDivRow();
		$CI->make->sDivRow(array('style'=>'margin-top:20px;'));
			$CI->make->sDivCol(6);
				$CI->make->input('Card Number','card_no',null,null);
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->inactiveDrop('Is Used','inactive',null,null,array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}