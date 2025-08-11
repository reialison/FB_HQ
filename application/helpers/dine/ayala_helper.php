<?php
function ayalaPage(){
	$CI =& get_instance();
	$CI->make->sDiv(array('class'=>'div-no-spaces'));
		$buttons = array(
						 "daily-files"	=> fa('fa-file-text fa-lg fa-fw')."<br> DAILY FILE READS",
						 "backtrack"		=> fa('fa-file-text fa-lg fa-fw')."<br> BACKTRACK GENERATOR",
						 "settings"		=> fa('fa-cogs fa-lg fa-fw')."<br> SETTINGS",
						 );
		$CI->make->sDivRow(array('style'=>'margin:0;'));
			foreach ($buttons as $id => $text) {
					$CI->make->sDivCol(4);
						$CI->make->button($text,array('id'=>$id.'-btn','class'=>'btn-block manager-btn-red-gray double'));
					$CI->make->eDivCol();
			}
		$CI->make->eDivRow();
	$CI->make->eDiv();
	$CI->make->sBox('default',array('class'=>'box-solid'));
		$CI->make->sBoxBody(array('style'=>'background-color:#015D56;min-height:492px;padding:0 0 5px 0;'));
			$CI->make->sDiv(array('id'=>'content'));
			$CI->make->eDiv();
		$CI->make->eBoxBody();
	$CI->make->eBox();
	return $CI->make->code();
}
function dailyFilesPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('style'=>'padding:10px;'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(6);
					$CI->make->H(3,'Select Date',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
					$CI->make->date(null,'file_date','','',array('class'=>'count-inputs  big-man-input'));	
				$CI->make->eDivCol();
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-eye').' Show File',array('id'=>'show-file-btn','style'=>'margin-top:28px;','class'=>'btn-block bg-teal'));					
				$CI->make->eDivCol();
				$CI->make->sDivCol(2);
					$CI->make->button(fa('fa-refresh').'Re Generate File',array('id'=>'regen-btn','style'=>'margin-top:28px;','class'=>'btn-block bg-green'));					
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->sDivRow();
				$CI->make->sDivCol(6,'left');
					$CI->make->H(3,'DAILY FILE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
					$CI->make->sDiv(array('id'=>'daily-div','style'=>'height:350px;width:100%;overflow:auto;background-color:#fff'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
				$CI->make->sDivCol(6,'left');
					$CI->make->H(3,'HOURLY FILE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
					$CI->make->sDiv(array('id'=>'hourly-div','style'=>'height:350px;width:100%;overflow:auto;background-color:#fff'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function backTrackPage(){
	$CI =& get_instance();
		$CI->make->sDiv(array('style'=>'padding:10px;'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(6);
					$CI->make->H(3,'Start Date',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
					$CI->make->date(null,'file_start_date','','',array('class'=>'count-inputs  big-man-input'));	
				$CI->make->eDivCol();
				$CI->make->sDivCol(6);
					$CI->make->H(3,'End Date',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
					$CI->make->date(null,'file_end_date','','',array('class'=>'count-inputs  big-man-input'));	
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->button(fa('fa-refresh').'Generate Files',array('id'=>'regen-btn','style'=>'margin-top:28px;','class'=>'btn-block bg-green'));					
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
				$CI->make->eDivCol();
			$CI->make->eDivRow();
				// $CI->make->sDivCol(2);
				// 	$CI->make->button(fa('fa-eye').' Show File',array('id'=>'show-file-btn','style'=>'margin-top:28px;','class'=>'btn-block bg-teal'));					
				// $CI->make->eDivCol();
				
		$CI->make->eDiv();
	return $CI->make->code();
}
function settingsPage($obj=array()){
	$CI =& get_instance();
		$CI->make->sForm('ayala/settings_db',array('id'=>'settings-form'));
			$CI->make->hidden('mall_id',iSetObj($obj,'id'));
			$CI->make->sDiv(array('style'=>'padding:10px;'));
				$CI->make->sDivRow();
					$CI->make->sDivCol(4,'left',2);
						$CI->make->H(3,'STORE NAME',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'store_name',iSetObj($obj,'store_name'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
						$CI->make->H(3,'CONTRACT NUMBER',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'contract_no',iSetObj($obj,'contract_no'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
						$CI->make->H(3,'3 DIGIT CODE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'xxx_no',iSetObj($obj,'xxx_no'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(4,'left');
						$CI->make->H(3,'DBF TENANT NAME',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'dbf_tenant_name',iSetObj($obj,'dbf_tenant_name'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
						$CI->make->H(3,'DBF PATH',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'dbf_path',iSetObj($obj,'dbf_path'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
						$CI->make->H(3,'TEXT FILE PATH',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'text_file_path',iSetObj($obj,'text_file_path'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
				$CI->make->sDivRow();
					$CI->make->sDivCol(4,'left',4);
						$CI->make->button(fa('fa-save fa-lg fa-fw')." SAVE DETAILS",array('id'=>'save-btn','class'=>'btn-block manager-btn-green'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eDiv();
		$CI->make->eForm();
	return $CI->make->code();
}
?>