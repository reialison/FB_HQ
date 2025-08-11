<?php
function megaworldPage(){
	$CI =& get_instance();
	$CI->make->sDiv(array('class'=>'div-no-spaces'));
		$buttons = array(
						 "daily-files"	=> fa('fa-file-text fa-lg fa-fw')."<br> DAILY FILE READS",
						 "settings"		=> fa('fa-cogs fa-lg fa-fw')."<br> SETTINGS",
						 );
		$CI->make->sDivRow(array('style'=>'margin:0;'));
			foreach ($buttons as $id => $text) {
					$CI->make->sDivCol(6);
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
				$CI->make->sDivCol(4);
						$CI->make->H(3,'SALES FILE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->sDiv(array('id'=>'sales-div','style'=>'height:350px;width:100%;overflow:auto;background-color:#fff'));
						$CI->make->eDiv();
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->H(3,'HOURLY FILE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->sDiv(array('id'=>'hourly-div','style'=>'height:350px;width:100%;overflow:auto;background-color:#fff'));
						$CI->make->eDiv();
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->H(3,'DISCOUNT FILE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->sDiv(array('id'=>'discount-div','style'=>'height:350px;width:100%;overflow:auto;background-color:#fff'));
						$CI->make->eDiv();
					$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function settingsPage($obj=array()){
	$CI =& get_instance();
		$CI->make->sForm('megaworld/settings_db',array('id'=>'settings-form'));
			$CI->make->hidden('megaworld',iSetObj($obj,'id'));
			$CI->make->sDiv(array('style'=>'padding:10px;'));
				$CI->make->sDivRow();
					$CI->make->sDivCol(4,'left',4);
						$CI->make->H(3,'TENANT CODE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'tenant_code',iSetObj($obj,'tenant_code'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
						$CI->make->H(3,'SALES TYPE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'sales_type',iSetObj($obj,'sales_type'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'tenant code must not be empty'));
						$CI->make->H(3,'FILE PATH',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
						$CI->make->input(null,'file_path',iSetObj($obj,'file_path'),'',array('class'=>'count-inputs big-man-input rOkay','ro-msg'=>'FILE PATH must not be empty'));
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