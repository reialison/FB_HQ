<?php
function itemCatSearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input('Item Category Name','name',null,null);
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
			$CI->make->allbranchesDrop('Branch','branch_id',null,'');
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
				$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function itemSubCatSearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input('Item SubCategory Name','name',null,null);
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
			$CI->make->allbranchesDrop('Branch','branch_id',null,'');
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
				$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function makeUOMForm($uom=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/uom_db",array('id'=>'uom_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				$CI->make->hidden('id',iSetObj($uom,'id'));
				if(!empty($uom))
					$CI->make->input('Code','code',iSetObj($uom,'code'),'Type Code',array('class'=>'rOkay', 'readonly'=>'readonly'));
				else
					$CI->make->input('Code','code',iSetObj($uom,'code'),'Type Code',array('class'=>'rOkay'));
			$CI->make->eDivCol();
			$CI->make->sDivCol(5);
				$CI->make->input('Name','name',iSetObj($uom,'name'),'Type Name',array('class'=>'rOkay'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
  //   	$CI->make->boxTitle('Conversion',array('style'=>'text-align:left;margin-left:25px;margin-right:25px;','class'=>'page-header'));
		// $CI->make->sDivRow(array('style'=>'margin:10px;'));
		// 	$CI->make->sDivCol(3);
		// 		$CI->make->input('Number','num',iSetObj($uom,'num'),'',array());
		// 	$CI->make->eDivCol();			
		// 	$CI->make->sDivCol(2);
		// 		$CI->make->uomDrop('','to',iSetObj($uom,'to'),' ',array('style'=>'margin-top:25px;'));
		// 	$CI->make->eDivCol();			
  //   	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Categories-----start-----allyn
function makeCategoryForm($category=array(),$ebcode=null){
	// echo "<pre>",print_r($category),"</pre>";die();
	$CI =& get_instance();
	$CI->make->sForm("settings/category_db",array('id'=>'category_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(12);
				$CI->make->hidden('cat_id',iSetObj($category,'cat_id'));
				$CI->make->hidden('ebcode',$ebcode);
					$CI->make->input('Code','code',iSetObj($category,'code'),'Type Code',array('class'=>'rOkay'));
				$CI->make->input('Name','name',iSetObj($category,'name'),'Type Name',array('class'=>'rOkay'));
				// $CI->make->branchesDrop('Branch','branch_code',null,'Select Branch');
				if(iSetObj($category,'cat_id') != null){
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($category,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selectedCatbranchDrop('Branch Code','bcode[]',null,null,array("multiple"=>"","selected"=>""),iSetObj($category,'code'));
				}else{
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->allbranchesDrop('','b_code[]',null,null,array("multiple"=>""));
				}
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Categories-----end-----allyn
//-----------Sub Categories-----start-----allyn
function makeSubCategoryForm($subcategory=array(),$ebcode=null){
	$CI =& get_instance();
	$CI->make->sForm("settings/subcategory_db",array('id'=>'subcategory_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(12);
				$CI->make->hidden('sub_cat_id',iSetObj($subcategory,'sub_cat_id'));
				$CI->make->hidden('ebcode',$ebcode);
				$CI->make->categoriesDrop('Under Category','cat_id',iSetObj($subcategory,'cat_id'),'',array());
				$CI->make->input('Code','code',iSetObj($subcategory,'code'),'Type Code',array('class'=>'rOkay'));
				$CI->make->input('Name','name',iSetObj($subcategory,'name'),'Type Name',array('class'=>'rOkay'));
				if(iSetObj($subcategory,'sub_cat_id') != null){
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($subcategory,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selectedSubCatbranchDrop('','bcode[]',null,null,array("multiple"=>"","selected"=>""),iSetObj($subcategory,'code'));
				}else{
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->allbranchesDrop('','b_code[]',null,null,array("multiple"=>""));
				}
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Sub Categories-----end-----allyn
//-----------Suppliers-----start-----allyn
function makeSupplierForm($supplier=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/supplier_db",array('id'=>'supplier_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('supplier_id',iSetObj($supplier,'supplier_id'));
				$CI->make->input('Name','name',iSetObj($supplier,'name'),'Type Name',array('class'=>'rOkay'));
				$CI->make->textarea('Address','address',iSetObj($supplier,'address'),'Type Supplier Address',array('class'=>'rOkay'));
				$CI->make->input('Contact No.','contact_no',iSetObj($supplier,'contact_no'),'Type Contact Number',array('class'=>'rOkay'));
				$CI->make->textarea('Memo','memo',iSetObj($supplier,'memo'),'Type Memo',array('class'=>'rOkay'));
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($supplier,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
			// $CI->make->sDivCol(6);
				//For another column
			// $CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Suppliers-----end-----allyn
//-----------Tax Rates-----start-----allyn
function makeTaxRateForm($tax_rate=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/tax_rate_db",array('id'=>'tax_rate_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('tax_id',iSetObj($tax_rate,'tax_id'));
				$CI->make->input('Name','name',iSetObj($tax_rate,'name'),'Type Name',array('class'=>'rOkay'));
				$CI->make->input('Rate','rate',iSetObj($tax_rate,'rate'),'Type Rate',array('class'=>'rOkay'));
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($tax_rate,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
// ------------- Receipt Discounts ------------- //
function makeReceiptDiscountForm($receipt_disc = null,$branch=null)
{
	$CI =& get_instance();
	$CI->make->sForm("settings/receipt_discount_db",array('id'=>'receipt_discount_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('disc_id',iSetObj($receipt_disc,'disc_id'));
				$CI->make->input('Code','disc_code',iSetObj($receipt_disc,'disc_code'),'Discount Code',array('class'=>'rOkay'));
				$CI->make->input('Name','disc_name',iSetObj($receipt_disc,'disc_name'),'Discount Name',array('class'=>'rOkay'));
				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
					$CI->make->input('Rate'
						, 'disc_rate'
						, iSetObj($receipt_disc,'disc_rate')
						, 'Rate'
						, array('class'=>'rOkay','style'=>'width:85px')
					);
					$CI->make->inactiveDrop('Absolute','fix',iSetObj($receipt_disc,'fix'),'',array('style'=>'width: 85px;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
					// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($receipt_disc,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->inactiveDrop('Make No Tax','no_tax',iSetObj($receipt_disc,'no_tax'),'',array('style'=>'width: 85px;'));
				$CI->make->eDivCol();
				if($branch != null){
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($receipt_disc,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("receipt_discounts","disc_id",iSetObj($receipt_disc,'disc_id'))); //table , where id, id needed
				}
				else{
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'','required'=>''));
				}
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	$CI->make->sDivRow();
	
		$CI->make->sDivCol(3,'left',3);
			$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-menu','class'=>'btn-block btn btn-success-600 radius-8 px-16 py-9'));
		$CI->make->eDivCol();
    $CI->make->eDivRow();
	return $CI->make->code();
}

function makeChargesForm($charges = null,$branch=null)
{
	$CI =& get_instance();
	$CI->make->sForm("charges/charges_new_db",array('id'=>'charges_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('charge_id',iSetObj($charges,'charge_id'));
				$CI->make->input('Code','charge_code',iSetObj($charges,'charge_code'),'Charge Code',array('class'=>'rOkay'));
				$CI->make->input('Name','charge_name',iSetObj($charges,'charge_name'),'Charge Name',array('class'=>'rOkay'));
				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
					$CI->make->input('Rate'
						, 'charge_amount'
						, iSetObj($charges,'charge_amount')
						, 'Rate'
						, array('class'=>'rOkay','style'=>'width:85px')
					);
					$CI->make->inactiveDrop('Absolute','absolute',iSetObj($charges,'absolute'),'',array('style'=>'width: 85px;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
					// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($receipt_disc,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->inactiveDrop('Tax Exempt','no_tax',iSetObj($charges,'no_tax'),'',array('style'=>'width: 85px;'));
				$CI->make->eDivCol();
				if($branch != null){
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($charges,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("charges","charge_id",iSetObj($charges,'charge_id'))); //table , where id, id needed
				}
				else{
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'','required'=>''));
				}
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	$CI->make->sDivRow();
	
		$CI->make->sDivCol(3,'left',3);
			$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-menu','class'=>'btn-block btn btn-success-600 radius-8 px-16 py-9'));
		$CI->make->eDivCol();
    $CI->make->eDivRow();
	return $CI->make->code();
}
// ---------- End of Receipt Discounts --------- //
//----------- PROMOS ---------------------------//
function makepromoform($receipt_disc = null,$branch=null)
{
	$CI =& get_instance();
	$CI->make->sForm("settings/promos_db",array('id'=>'receipt_discount_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('disc_id',iSetObj($receipt_disc,'disc_id'));
				$CI->make->input('Code','disc_code',iSetObj($receipt_disc,'disc_code'),'Promo Code',array('class'=>'rOkay'));
				$CI->make->input('Name','disc_name',iSetObj($receipt_disc,'disc_name'),'Promo Name',array('class'=>'rOkay'));
				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
					$CI->make->input('Rate'
						, 'disc_rate'
						, iSetObj($receipt_disc,'disc_rate')
						, 'Rate'
						, array('class'=>'rOkay','style'=>'width:85px')
					);
					$CI->make->inactiveDrop('Absolute','fix',iSetObj($receipt_disc,'fix'),'',array('style'=>'width: 85px;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
					// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($receipt_disc,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->inactiveDrop('Make No Tax','no_tax',iSetObj($receipt_disc,'no_tax'),'',array('style'=>'width: 85px;'));
				$CI->make->eDivCol();
				if($branch != null){
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($receipt_disc,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("receipt_discounts","disc_id",iSetObj($receipt_disc,'disc_id'))); //table , where id, id needed
				}
				else{
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'','required'=>''));
				}
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->eForm();
	$CI->make->sDivRow();
	
		$CI->make->sDivCol(3,'left',3);
			$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-menu','class'=>'btn-block btn btn-success-600 radius-8 px-16 py-9'));
		$CI->make->eDivCol();
    $CI->make->eDivRow();
	return $CI->make->code();
}
//---------------promos end-------------------//
//-----------Tax Rates-----end-----allyn
function makeTablesPage($branch=null){
	$CI =& get_instance();
	$CI->make->sBox('primary');
		$CI->make->sBoxBody();
		    $CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->H(3,fa('fa-warning').' Click where you want to move.',array('class'=>'move-shows','style'=>'margin:0px;display:none;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(1,'right');
					$CI->make->A(fa('fa-ban').' Cancel Move','#',array('id'=>'cancel-move-btn','class'=>'move-shows btn btn-warning','style'=>'display:none;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(7,'right');
				$btnMsg = "Add an Image";
		    	if($branch->image != null)
					$btnMsg = "Change Image";
					$CI->make->A(fa('fa-picture-o').' '.$btnMsg,'settings/upload_image_seat_form/',array(
																'id'=>'change-img',
																'rata-title'=>'Restaurant Seating Image Upload',
																'rata-pass'=>'settings/upload_image_seat_db',
																'rata-form'=>'upload_image_form',
																'class'=>'btn btn-primary'
															));
				$CI->make->eDivCol();
		    $CI->make->eDivRow();
		    $CI->make->sDivRow();
		    	if($branch->image != null)
			    	$CI->make->hidden('imgSrc',base_url().'uploads/'.$branch->image);
		    	else
			    	$CI->make->hidden('imgSrc',null);
				$CI->make->sDivCol(12,'left',0,array('id'=>'imgCon'));
				$CI->make->eDivCol();
		    $CI->make->eDivRow();
	    $CI->make->eBoxBody();
	$CI->make->sBox();

	return $CI->make->code();
}
function makeTableForm($det=null){
	$CI =& get_instance();
		$CI->make->sForm("settings/tables_db",array('id'=>'table_form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(2);
					$CI->make->hidden('tbl_id',iSetObj($det,'tbl_id'));
					$CI->make->input('Capacity','capacity',iSetObj($det,'capacity'),'Type Capacity',array('class'=>'rOkay'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(8);
					$CI->make->input('Name','name',iSetObj($det,'name'),'Type Name',array('class'=>'rOkay'));
				$CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}
function makeTableUploadForm($det=null){
	$CI =& get_instance();
		$CI->make->sForm("settings/upload_image_seat_db",array('id'=>'upload_image_form','enctype'=>'multipart/form-data'));
			$CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
				$CI->make->sDivCol();
					$CI->make->A(fa('fa-picture-o').' Select an Image','#',array(
															'id'=>'select-img',
															'class'=>'btn btn-primary'
														));
					$CI->make->append('<br>');
					$CI->make->H(4,'Warning! Changing image will delete all the set tables.',array('class'=>'label label-warning'));
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->sDivRow();
				$CI->make->sDivCol();
					$thumb = base_url().'img/noimage.png';
					if(iSetObj($det,'image')  != ""){
						$thumb = base_url().'uploads/'.iSetObj($det,'image');
					}
					$CI->make->img($thumb,array('class'=>'media-object thumbnail','id'=>'target','style'=>'height:220px;'));
					$CI->make->file('fileUpload',array('style'=>'display:none;'));
				$CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}
//-----------Terminals-----start-----allyn
function makeTerminalForm($terminal=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/terminal_db",array('id'=>'terminal_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				$CI->make->hidden('terminal_id',iSetObj($terminal,'terminal_id'));
				if(!empty($terminal))
					$CI->make->input('Code','terminal_code',iSetObj($terminal,'terminal_code'),'Type Terminal Code',array('class'=>'rOkay', 'readonly'=>'readonly'));
				else
					$CI->make->input('Code','terminal_code',iSetObj($terminal,'terminal_code'),'Type Code',array('class'=>'rOkay'));
				$CI->make->input('Name','terminal_name',iSetObj($terminal,'terminal_name'),'Type Name',array('class'=>'rOkay'));
				$CI->make->input('I.P. Address','ip',iSetObj($terminal,'ip'),'Type I.P. Address',array('class'=>'rOkay'));
				$CI->make->input('Computer Name','comp_name',iSetObj($terminal,'comp_name'),'Type Computer Name',array('class'=>'rOkay'));
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($terminal,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Terminals-----end-----allyn
//-----------Currencies-----start-----allyn
function makeCurrencyForm($currency=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/currency_db",array('id'=>'currency_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				$CI->make->hidden('id',iSetObj($currency,'id'));
				// if(!empty($currency))
					// $CI->make->input('Code','currency_code',iSetObj($currency,'currency_code'),'Type currency Code',array('class'=>'rOkay', 'readonly'=>'readonly'));
				// else
					$CI->make->input('Currency','currency',iSetObj($currency,'currency'),'Type Code',array('class'=>'rOkay'));
				$CI->make->input('Description','currency_desc',iSetObj($currency,'currency_desc'),'Type Name',array('class'=>'rOkay'));
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($currency,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Currencies-----end-----allyn
//-----------References-----start-----allyn
function makeReferencesForm($det=array()){
	$CI =& get_instance();

	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sForm("settings/references_db",array('id'=>'references_form'));
						foreach($det as $val){
							$CI->make->sDivRow(array('style'=>'margin:10px;'));
								$CI->make->sDivCol(6);
									$x = $CI->make->A(fa('fa-save'),'#', array('return'=>true, 'class'=>'save_btn', 'ref'=>$val->type_id, 'label'=>ucwords($val->name)));
									$CI->make->input(ucwords($val->name),'type-'.$val->type_id,$val->next_ref,'Type Code',array('class'=>'rOkay'), null, $x);
								$CI->make->eDivCol();
							$CI->make->eDivRow();
						}
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
//-----------References-----end-----allyn
//-----------Locations-----start-----allyn
function makeLocationForm($location=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/location_db",array('id'=>'location_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				$CI->make->hidden('loc_id',iSetObj($location,'loc_id'));
					$CI->make->input('Code','loc_code',iSetObj($location,'loc_code'),'Type Location Code',array('class'=>'rOkay'));
				$CI->make->input('Name','loc_name',iSetObj($location,'loc_name'),'Type Location Name',array('class'=>'rOkay'));
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($location,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//-----------Locations-----end-----allyn
function makeChargeForm($item=array()){
	$CI =& get_instance();

	$CI->make->sForm("charges/db",array('id'=>'charge_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				$CI->make->hidden('charge_id',iSetObj($item,'charge_id'));
				if(!empty($item))
					$CI->make->input('Code','charge_code',iSetObj($item,'charge_code'),'Type Code',array('class'=>'rOkay', 'readonly'=>'readonly'));
				else
					$CI->make->input('Code','charge_code',iSetObj($item,'charge_code'),'Type Code',array('class'=>'rOkay'));
				$CI->make->input('Name','charge_name',iSetObj($item,'charge_name'),'Type Name',array('class'=>'rOkay'));
				$CI->make->number('Amount','charge_amount',iSetObj($item,'charge_amount'),'Type Name',array('class'=>'rOkay'));
				$CI->make->inactiveDrop('Absolute','absolute',iSetObj($item,'absolute'),'',array('style'=>'width: 85px;'));
				$CI->make->inactiveDrop('Tax Excempt','no_tax',iSetObj($item,'no_tax'),'',array('style'=>'width: 85px;'));
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($item,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//---------------------jed
function makeDenominationForm($deno=array()){
	$CI =& get_instance();
	$CI->make->sForm("settings/denomination_db",array('id'=>'denomination_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				$CI->make->hidden('id',iSetObj($deno,'id'));
					$CI->make->input('Description','desc',iSetObj($deno,'desc'),'Type Description',array('class'=>'rOkay'));
				$CI->make->decimal('Value','value',iSetObj($deno,'value'),'Type Value',2,array('class'=>'rOkay'));
				//$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($location,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
function makeTransForm($item=array()){
	$CI =& get_instance();
	// echo iSetObj($item,'trans_id');die();
	$CI->make->sForm("settings/transaction_type_db",array('id'=>'transform'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('trans_id',iSetObj($item,'trans_id'));
				$CI->make->input('Transaction Name (no space)','trans_name',iSetObj($item,'trans_name'),'Type Name',array('class'=>'rOkay'));
				if(iSetObj($item,'trans_id') != null){
						// $CI->make->inactiveDrop('Inactive','inactive',iSetObj($item,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedTTbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($item,'trans_name'));
					}else{
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->allbranchesDrop('','b_code[]',null,null,array("multiple"=>"",'class'=>'rOkay'));
					}
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($item,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',4);
				$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-trans','class'=>'btn btn-success-600 radius-8 px-16 py-9'));
			$CI->make->eDivCol();
	    $CI->make->eDivRow();

	return $CI->make->code();
}
function paySearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input('Payment Name','name',null,null);
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
				$CI->make->allbranchesDrop('Branch','branch_id',null,'');
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
				$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function payFormPage($pay_id=null){
	$CI =& get_instance();
	// $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
	// 		$CI->make->sDivCol(12,'right');
	// 			$CI->make->A(fa('fa-reply').' Go Back To list',base_url().'mods',array('class'=>'btn btn-primary'));
	// 		$CI->make->eDivCol();
	// 	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'settings/payment_mode',array('return'=>true,'class'=>'btn btn-success-600 radius-8 px-16 py-9')),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'settings/pay_details_load/','id'=>'details_link'),
						fa('fa-book')." Input Fields"=>array('href'=>'#recipe','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'settings/input_fields_load/','id'=>'recipe_link'),
					);
					$CI->make->hidden('pay_id',$pay_id);
					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabBody();
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'recipe','class'=>'tab-pane'));
						$CI->make->eTabPane();
					$CI->make->eTabBody();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function payDetailsLoad($pay=null,$pay_id=null){
	$CI =& get_instance();
		$CI->make->sForm("settings/pay_details_db",array('id'=>'details_form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->hidden('form_pay_id',$pay_id);
					if($pay_id){
						$CI->make->input('Payment Code (Show on receipt and Zread)','payment_code',iSetObj($pay,'payment_code'),'Type Code',array('class'=>'rOkay','readOnly'=>true));
					}else{
						$CI->make->input('Payment Code (Show on receipt and Zread)','payment_code',iSetObj($pay,'payment_code'),'Type Code',array('class'=>'rOkay'));
					}
					$CI->make->input('Description (Show on payment page)','description',iSetObj($pay,'description'),'Price',array('class'=>'rOkay'));
					$CI->make->paymentGroupDrop('Payment Group','payment_group_id',iSetObj($pay,'payment_group_id'));
					$CI->make->inactiveDrop('Inactive','inactive',iSetObj($pay,'inactive'));
				$CI->make->eDivCol();
				// $CI->make->sDivCol(4);
				// 	$CI->make->inactiveDrop('Has Recipe','has_recipe',iSetObj($mod,'has_recipe'));
				// $CI->make->eDivCol();
	    	$CI->make->eDivRow();
	  //   	$CI->make->sDivRow();
			// 	$CI->make->sDivCol(12);
			// 		if($pay_id != null){
			// 			$CI->make->sDivCol(12);
			// 				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
			// 					$CI->make->span("Non-affected Branch");
			// 				$CI->make->eDivCol();
			// 				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
			// 					$CI->make->span("Affected Branch");
			// 				$CI->make->eDivCol();
			// 			$CI->make->eDivCol();
			// 			$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("menus","menu_id",$menu_id)); //table , where id, id needed
			// 		}
			// 		else{
			// 			$CI->make->sDivCol(12);
			// 				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
			// 					$CI->make->span("Non-affected Branch");
			// 				$CI->make->eDivCol();
			// 				$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
			// 					$CI->make->span("Affected Branch");
			// 				$CI->make->eDivCol();
			// 			$CI->make->eDivCol();
			// 			$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'','class'=>'bootstrap-select'));		
						
			// 		}
			// 	$CI->make->eDivCol();
			// $CI->make->eDivRow();
		$CI->make->eForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',4);
				$CI->make->button(fa('fa-save').' Submit',array('id'=>'save-pay','class'=>'btn btn-success-600 radius-8 px-16 py-9'));
			$CI->make->eDivCol();
	    $CI->make->eDivRow();
	return $CI->make->code();
}
function inputFieldsLoad($payment_id=null,$det=null){
	$CI =& get_instance();
		$CI->make->sForm("settings/fields_db",array('id'=>'fields_form'));
			$CI->make->hidden('payment_id',$payment_id);
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->input('Field Name','field_name',null,'name',array('class'=>'rOkay'));
					// $CI->make->input(null,'item-search',null,'Search Modifiers',array('search-url'=>'mods/search_modifiers'),'',fa('fa-search'));
					// $CI->make->modifiersAjaxDrop('Search Modifier','item-search');
				$CI->make->eDivCol();				
				$CI->make->sDivCol(4);
	                $CI->make->A(fa('fa-lg fa-plus')." Add",'#',array('class'=>'btn btn-primary','id'=>'add-field','style'=>'margin-top:23px;'));
				$CI->make->eDivCol();				
			$CI->make->eDivRow();
			$CI->make->H(5,'Note: Check the input field if active.');
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->sUl(array('class'=>'vertical-list','id'=>'modifier-list'));
						foreach ($det as $res) {
							$chck = true;
							if($res->inactive == 1){
								$chck = false;
							}
							$li = $CI->make->li(
				                $CI->make->checkbox(null,'dflt_'.$res->field_id,0,array('class'=>'dflt','ref'=>$res->field_id,'return'=>true),$chck)." ".
				                $CI->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
				                $CI->make->span($res->field_name,array('class'=>'text','return'=>true)),
				                // $CI->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$res->field_id,'ref'=>$res->field_id)),
				                array('id'=>'li-'.$res->field_id)
				             );
						}
					$CI->make->eUl();
				$CI->make->eDivCol();				
			$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}

function paymentGroupFormPage($payment_group=array()){
	$CI =& get_instance();
	// $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
	// 		$CI->make->sDivCol(12,'right');
	// 			$CI->make->A(fa('fa-reply').' Go Back To list',base_url().'mods',array('class'=>'btn btn-primary'));
	// 		$CI->make->eDivCol();
	// 	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'settings/payment_mode',array('return'=>true,'class'=>'btn btn-success-600 radius-8 px-16 py-9')),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'settings/pay_details_load/','id'=>'details_link'),
					);				
					
		$CI->make->sForm("settings/payment_group_details_db",array('id'=>'details_form'));
			$CI->make->hidden('payment_group_id',$payment_group->payment_group_id);
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					if($payment_group->payment_group_id){
						$CI->make->input('Payment Group Code','code',iSetObj($payment_group,'code'),'Type Code',array('class'=>'rOkay','readOnly'=>true));
					}else{
						$CI->make->input('Payment Group Code','code',iSetObj($payment_group,'code'),'Type Code',array('class'=>'rOkay'));
					}
					$CI->make->input('Description','description',iSetObj($payment_group,'description'),'Description',array('class'=>'rOkay'));
					$CI->make->inactiveDrop('Inactive','inactive',iSetObj($payment_group,'inactive'));
				$CI->make->eDivCol();
				// $CI->make->sDivCol(4);
				// 	$CI->make->inactiveDrop('Has Recipe','has_recipe',iSetObj($mod,'has_recipe'));
				// $CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',4);
				$CI->make->button(fa('fa-save').' Submit',array('id'=>'save-pay','class'=>'btn btn-success-600 radius-8 px-16 py-9'));
			$CI->make->eDivCol();
	    $CI->make->eDivRow();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function transTypeFormPage($trans_id=null,$branch=null){
	$CI =& get_instance();
	// $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
	// 		$CI->make->sDivCol(12,'right');
	// 			$CI->make->A(fa('fa-reply').' Go Back To list',base_url().'mods',array('class'=>'btn btn-primary'));
	// 		$CI->make->eDivCol();
	// 	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'settings/trans_type',array('return'=>true,'class'=>'btn btn-success-600 radius-8 px-16 py-9')),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'settings/transtype_details_load/','id'=>'details_link'),
						fa('fa-book')." Categories"=>array('href'=>'#categories','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'settings/trans_type_categories/','id'=>'categories_link'),
						fa('fa-book')." Menus"=>array('href'=>'#categories','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'settings/trans_type_menus/','id'=>'menus_link'),
					);
					$CI->make->hidden('trans_id',$trans_id);
					$CI->make->hidden('branch',$branch);
					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabBody();
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'categories','class'=>'tab-pane'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'menus','class'=>'tab-pane'));
						$CI->make->eTabPane();
					$CI->make->eTabBody();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function transCategoriesLoad($trans_id,$branch,$trans_name,$det = null){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-asterisk').' '.$trans_name.' Categories',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(8);
			$CI->make->sForm('settings/trans_type_categories_db',array('id'=>'trans-cat-form'));
				$CI->make->hidden('trans_id_hid',$trans_id);
				$CI->make->hidden('branch',$branch);
				$CI->make->sDivRow();
					// $CI->make->sDivCol(4);
					// 	$CI->make->transTypeDropId('Transaction Type','trans_type',null,'Select a Type');
					// $CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->menuCategoriesDrop('Categories','menu_cat_id',null,'Select Category');
						// $CI->make->input('Price','price',null,'Price',array());
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
		                $CI->make->A(fa('fa-lg fa-plus')." ADD",'#',array('class'=>'btn btn-primary','id'=>'add-trans-cat','style'=>'margin-top:23px;'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
	$CI->make->eDivRow();	

	$CI->make->sDivRow();	
		$CI->make->sDivCol(7);
			$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'trans-cat-details-tbl'));
    					$total = 0;

    					$CI->make->sRow();
        					// $CI->make->td("Transaction Type",array('style'=>'text-align:left'));
        					$CI->make->td("Categories Added",array('style'=>'text-align:left'));
        					$CI->make->td("",array('style'=>'text-align:right'));
						$CI->make->eRow();


    					foreach ($det as $val) {
    						$CI->make->sRow(array('id'=>'trans-cat-row-'.$val->id.'-'.$branch));
    							// $CI->make->td($val->trans_name);
    							$CI->make->td($val->menu_cat_name,array('style'=>'text-align:left'));
    							$a = $CI->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'trans-cat-del-'.$val->id,'ref'=>$val->id,'branch_code'=>$branch,'class'=>'del-trans-cat','return'=>true));
            					$CI->make->td($a,array('style'=>'text-align:right'));
    						$CI->make->eRow();
    					}
    			$CI->make->eTable();
    		$CI->make->eDiv();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function transMenusLoad($trans_id,$trans_name,$det = null,$branch){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-asterisk').' '.$trans_name.' Categories',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(8);
			$CI->make->sForm('settings/trans_type_menus_db',array('id'=>'trans-menu-form'));
				$CI->make->hidden('trans_id_hid',$trans_id);
				$CI->make->hidden('branch',$branch);
				$CI->make->sDivRow();
					// $CI->make->sDivCol(4);
					// 	$CI->make->transTypeDropId('Transaction Type','trans_type',null,'Select a Type');
					// $CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->itemsDrop('Menus','menu_id',null,'Select Menus');
						// $CI->make->input('Price','price',null,'Price',array());
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
		                $CI->make->A(fa('fa-lg fa-plus')." ADD",'#',array('class'=>'btn btn-primary','id'=>'add-trans-menu','style'=>'margin-top:23px;'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
	$CI->make->eDivRow();	

	$CI->make->sDivRow();	
		$CI->make->sDivCol(7);
			$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'trans-menu-details-tbl'));
    					$total = 0;

    					$CI->make->sRow();
        					// $CI->make->td("Transaction Type",array('style'=>'text-align:left'));
        					$CI->make->td("Menus Added",array('style'=>'text-align:left'));
        					$CI->make->td("",array('style'=>'text-align:right'));
						$CI->make->eRow();


    					foreach ($det as $val) {
    						$CI->make->sRow(array('id'=>'trans-menu-row-'.$val->id));
    							// $CI->make->td($val->trans_name);
    							$CI->make->td($val->menu_name,array('style'=>'text-align:left'));
    							$a = $CI->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'trans-menu-del-'.$val->id,'ref'=>$val->id,'branch_code'=>$branch,'class'=>'del-trans-menu','return'=>true));
            					$CI->make->td($a,array('style'=>'text-align:right'));
    						$CI->make->eRow();
    					}
    			$CI->make->eTable();
    		$CI->make->eDiv();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
?>