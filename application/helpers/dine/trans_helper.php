<?php
function voidForm($trans_id=null){
	$CI =& get_instance();
	$CI->make->sForm("",array('id'=>'main-form'));
		$CI->make->textarea('Reason','reason',null,null,array());
	$CI->make->eForm();
	return $CI->make->code();
}