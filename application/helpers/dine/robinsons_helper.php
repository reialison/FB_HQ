<?php
function robFiles(){
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
					$CI->make->button(fa('fa-refresh').'Regen & Resend',array('id'=>'resend-file-btn','style'=>'margin-top:28px;','class'=>'btn-block bg-green'));
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->sDivRow();
				$CI->make->sDivCol(6,'left');
					$CI->make->H(3,'DAILY FILE',array('class'=>'headline text-center','style'=>'margin-bottom:10px;font-size:20px;'));
					$CI->make->sDiv(array('id'=>'daily-div','style'=>'height:350px;width:100%;overflow:auto;background-color:#fff'));
					$CI->make->eDiv();
				$CI->make->eDivCol();
			$CI->make->eDivRow();
		$CI->make->eDiv();
	return $CI->make->code();
}
function settingsPage($det=array()){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol(8,'left',2);
		$CI->make->sBox('solid',array('style'=>'margin-top:20px;'));
			$CI->make->sBoxBody();
			$CI->make->sForm("robinsons/details_db",array('id'=>'details_form'));
				$CI->make->input('RLC TENANT CODE','rob_tenant_code',iSetObj($det,'rob_tenant_code'),'RLC PATH',array());
				$CI->make->input('RLC Path','rob_path',iSetObj($det,'rob_path'),'RLC PATH',array());
				$CI->make->input('RLC Username','rob_username',iSetObj($det,'rob_username'),'RLC Username',array());
				$CI->make->input('RLC Password','rob_password',iSetObj($det,'rob_password'),'RLC Password',array());
				$CI->make->button(fa('fa-save fa-fw').' Save Details',array('id'=>'save-btn','class'=>'btn-block manager-btn-green'));
			$CI->make->eForm();
			$CI->make->eBoxBody();
		$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function robFiles3($zreads=array(),$det){
	$CI =& get_instance();
	$CI->make->sBox('default',array('class'=>'box-solid'));
		$CI->make->sBoxBody(array('style'=>'background-color:#015D56;min-height:535px;padding:10px;;'));
			$CI->make->sTab();
				$tabs = array(
					fa('fa-table')." LIST"=>array('href'=>'#list'),
					fa('fa-cogs')." SETTINGS"=>array('href'=>'#setup')
				);
				$CI->make->tabHead($tabs,null,array());
				$CI->make->sTabBody();
					$CI->make->sTabPane(array('id'=>'list','class'=>'tab-pane active'));

						$CI->make->sDivRow();
							$CI->make->sDivCol();
								$CI->make->sBox('success');
									$CI->make->sBoxBody();
										$CI->make->sDivRow();
											$CI->make->sDivCol();
												$th = array(
													'READ DATE'=>'',
													'Scope From'=>'',
													'Scope To'=>'',
													''=>array('width'=>'10%','align'=>'right')
												);
												$rows = array();
												foreach ($zreads as $val) {
													$link = "";
													$link .= $CI->make->A(fa('fa-refresh fa-fw')." RESEND",base_url().'reads/resend_to_rob/'.$val->id,array('class'=>'btn btn-primary','return'=>'true'));
													$rows[] = array(
														sql2Date($val->read_date),
														($val->scope_from),
														($val->scope_to),
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
					
					$CI->make->eTabPane();
					$CI->make->sTabPane(array('id'=>'setup','class'=>'tab-pane'));
						
						$CI->make->sDivRow();
							$CI->make->sDivCol(6,'left',3);
								$CI->make->sForm("robinsons/details_db",array('id'=>'details_form'));
									$CI->make->input('RLC TENANT CODE','rob_tenant_code',iSetObj($det,'rob_tenant_code'),'RLC PATH',array());
									$CI->make->input('RLC Path','rob_path',iSetObj($det,'rob_path'),'RLC PATH',array());
									$CI->make->input('RLC Username','rob_username',iSetObj($det,'rob_username'),'RLC Username',array());
									$CI->make->input('RLC Password','rob_password',iSetObj($det,'rob_password'),'RLC Password',array());
									$CI->make->button(fa('fa-save fa-fw').' Save Details',array('id'=>'save-btn','class'=>'btn-block manager-btn-green'));
								$CI->make->eForm();
							$CI->make->eDivCol();
						$CI->make->eDivRow();

					$CI->make->eTabPane();

				$CI->make->eTabBody();
			$CI->make->eTab();

			
		$CI->make->eBoxBody();
	$CI->make->eBox();

	return $CI->make->code();
}
function robFiles2($list = array(),$det=array()){
	$CI =& get_instance();
	$CI->make->sBox('default',array('class'=>'box-solid'));
		$CI->make->sBoxBody(array('style'=>'background-color:#015D56;min-height:535px;padding:10px;;'));
			$CI->make->sTab();
				$tabs = array(
					fa('fa-table')." LIST"=>array('href'=>'#list'),
					fa('fa-cogs')." SETTINGS"=>array('href'=>'#setup')
				);
				$CI->make->tabHead($tabs,null,array());
				$CI->make->sTabBody();
					$CI->make->sTabPane(array('id'=>'list','class'=>'tab-pane active'));

						$CI->make->sDivRow();
							$CI->make->sDivCol();
								$CI->make->sBox('success');
									$CI->make->sBoxBody();
										$CI->make->sDivRow();
											$CI->make->sDivCol();
												$th = array(
													'File'=>'',
													'Date Created'=>'',
													'Date Last Sent'=>'',
													'Sent'=>'',
													''=>array('width'=>'10%','align'=>'right')
												);
												$rows = array();
												foreach ($list as $val) {
													$link = "";
													$link .= $CI->make->A(fa('fa-refresh fa-fw')." RESEND",base_url().'reads/send_to_rob_man/'.$val->id,array('class'=>'btn btn-primary','return'=>'true'));
													$rows[] = array(
														$val->code,
														sql2Date($val->date_created),
														sql2Date($val->last_update),
														($val->inactive == 0 ? 'Sent' : 'Not Sent'),
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
					
					$CI->make->eTabPane();
					$CI->make->sTabPane(array('id'=>'setup','class'=>'tab-pane'));
						
						$CI->make->sDivRow();
							$CI->make->sDivCol(6,'left',3);
								$CI->make->sForm("robinsons/details_db",array('id'=>'details_form'));
									$CI->make->input('RLC TENANT CODE','rob_tenant_code',iSetObj($det,'rob_tenant_code'),'RLC PATH',array());
									$CI->make->input('RLC Path','rob_path',iSetObj($det,'rob_path'),'RLC PATH',array());
									$CI->make->input('RLC Username','rob_username',iSetObj($det,'rob_username'),'RLC Username',array());
									$CI->make->input('RLC Password','rob_password',iSetObj($det,'rob_password'),'RLC Password',array());
									$CI->make->button(fa('fa-save fa-fw').' Save Details',array('id'=>'save-btn','class'=>'btn-block manager-btn-green'));
								$CI->make->eForm();
							$CI->make->eDivCol();
						$CI->make->eDivRow();

					$CI->make->eTabPane();

				$CI->make->eTabBody();
			$CI->make->eTab();

			
		$CI->make->eBoxBody();
	$CI->make->eBox();

	return $CI->make->code();
}
?>