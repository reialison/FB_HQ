<?php
function makeEmailForm($cat=array()){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/email_form_db",array('id'=>'email_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(5);
				// echo "<pre>",print_r($branch),"</pre>";die();
				$CI->make->hidden('email_id',iSetObj($cat,'id'));
				$CI->make->input('Name','name',iSetObj($cat,'name'),'Type Name',array('class'=>'rOkay'));
				$CI->make->input('Email','email',iSetObj($cat,'email_address'),'Type Email Address',array('class'=>'rOkay','type'=>'Email'));
				// if($branch != null){
				// $CI->make->selected_bcode('Branch Code','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("menu_categories","menu_cat_id",iSetObj($cat,'menu_sub_id'))); //table , where id, id needed
				$CI->make->emailSetDrop('Email Setting Types','email_types',iSetObj($cat,'types'),'',array());
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
				// }
				// else{
				// $CI->make->allbranchesDrop('Branch Code','b_code[]',null,null,array('multiple'=>'multiple'));
				// }
				// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
?>