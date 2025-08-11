<?php
function customerSearchForm(){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Customer Name','cust_name',null,null);
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function customersFormPage($cust_id=null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'pos_customers',array('return'=>true)),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'pos_customers/details_load/','id'=>'details_link'),
					);
					$CI->make->hidden('cust_id',$cust_id);
					$CI->make->hidden('to_terminal','no');

					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabBody(array('style'=>'min-height:202px;'));
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
					$CI->make->eTabBody();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function customersFormTerminalPage($cust_id=null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol(1);
		$CI->make->eDivCol();
		$CI->make->sDivCol(10);
			$CI->make->sDiv(array('style'=>'margin-top:10px;'));
				// $CI->make->sBoxBody();
					$CI->make->sTab();
						$tabs = array(
							"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'pos_customers/customer_terminal',array('return'=>true)),
							fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'pos_customers/details_load/','id'=>'details_link'),
						);
						$CI->make->hidden('cust_id',$cust_id);
						$CI->make->hidden('to_terminal','yes');

						$CI->make->tabHead($tabs,null,array());
						$CI->make->sTabBody(array('style'=>'min-height:202px;'));
							$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
							$CI->make->eTabPane();
						$CI->make->eTabBody();
					$CI->make->eTab();
				// $CI->make->eBoxBody();
			$CI->make->eDiv();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function customerDetailsLoad($cust=null,$cust_id=null){
	$CI =& get_instance();
			$CI->make->sForm("pos_customers/details_db",array('id'=>'customers_form'));
				/* GENERAL DETAILS */
				$CI->make->sDivRow();
					$CI->make->sDivCol(2);
						$url = base_url().'img/avatar.jpg';
						$CI->make->img($url,array('style'=>'width:100%;','class'=>'media-object thumbnail','id'=>'target'));
						// $CI->make->file('fileUpload',array('style'=>'display:none;'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(3);
						$CI->make->hidden('form_cust_id',$cust_id);
						$CI->make->input('First Name','fname',iSetObj($cust,'fname'),'First Name',array('class'=>'rOkay'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(3);
						$CI->make->input('Middle Name','mname',iSetObj($cust,'mname'),'Middle Name',array());
					$CI->make->eDivCol();
					$CI->make->sDivCol(3);
						$CI->make->input('Last Name','lname',iSetObj($cust,'lname'),'Last Name',array('class'=>'rOkay'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(1);
						$CI->make->input('Suffix','suffix',iSetObj($cust,'suffix'),'Suffix',array());
					$CI->make->eDivCol();
		    	$CI->make->eDivRow();
		   	 	/* GENERAL DETAILS END */
			$CI->make->H(4,"Contact Details",array('class'=>'page-header'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(3);
					$CI->make->input('Email Address','email',iSetObj($cust,'email'),'Email Address',array('class'=>'rOkay'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->input('Contact No.','phone',iSetObj($cust,'phone'),'Contact Number',array('class'=>'rOkay'));
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->H(4,"Address",array('class'=>'page-header'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(3);
					$CI->make->input('Street Address','street_no',iSetObj($cust,'street_no'),'Street Address',array());
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->input('Barangay','street_address',iSetObj($cust,'street_address'),'Barangay',array());
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->input('City','city',iSetObj($cust,'city'),'City',array('class'=>'rOkay'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->input('Zip','zip',iSetObj($cust,'zip'),'Zip',array('class'=>'rOkay'));
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->H(4,"",array('class'=>'page-header'));
			$CI->make->sDivRow();
			    $CI->make->sDivCol(4,'left');
			        $CI->make->button(fa('fa-save')." Save Details",array('id'=>'save-btn','class'=>'','style'=>'margin-right:6px;'),'success');
			        $CI->make->button(fa('fa-save')." Save As New",array('id'=>'save-new-btn','class'=>''),'info');
			    $CI->make->eDivCol();
			    $CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->eForm();

	return $CI->make->code();
}