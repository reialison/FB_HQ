<?php
//-----------Branch Details-----start-----allyn
function makeDetailsForm($det=array(),$set=array(),$splashes=array()){
	$CI =& get_instance();

	$CI->make->sDivRow();
		$CI->make->sDivCol();
			// $CI->make->sBox('primary');
				// $CI->make->sBoxBody();
					$CI->make->sTab();
						$tabs = array(
							fa('fa-info-circle')." Details"=>array('href'=>'#details'),
							fa('fa-cogs')." POS"=>array('href'=>'#setup'),
							fa('fa-image')." Images"=>array('href'=>'#image'),
							fa('fa-database')." Database"=>array('href'=>'#database'),
							// fa('fa-download')." Download Update"=>array('href'=>'#Dlupdate'),
						);
					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabBody();
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
							$CI->make->sForm("setup/details_db",array('id'=>'details_form'));
								$CI->make->sDivRow(array('style'=>'margin:10px;'));
									$CI->make->sDivCol(6);
										$CI->make->hidden('tax_id',iSetObj($det,'tax_id'));
										$CI->make->input('Code','branch_code',iSetObj($det,'branch_code'),'Type Code',array('class'=>'rOkay', 'readonly'=>'readonly'));
										$CI->make->input('Name','branch_name',iSetObj($det,'branch_name'),'Type Name',array('class'=>'rOkay'));
										$CI->make->textarea('Description','branch_desc',iSetObj($det,'branch_desc'),'Type Description',array('class'=>'rOkay'));
										$CI->make->sDivRow();
											$CI->make->sDivCol(6);
												$CI->make->sDiv(array('class'=>'bootstrap-timepicker'));
													$opening_time = date('h:i A',strtotime($det->store_open));
													$CI->make->input('Opening Time','store_open',$opening_time,'',array('class'=>'rOkay timepicker'),null,fa('fa-clock-o'));
												$CI->make->eDiv();
											$CI->make->eDivCol();
											$CI->make->sDivCol(6);
												$CI->make->sDiv(array('class'=>'bootstrap-timepicker'));
													// $CI->make->input('Closing Time','store_close',iSetObj($det,'store_close'),'',array('class'=>'rOkay timepicker'),null,fa('fa-clock-o'));
													$closing_time = date('h:i A',strtotime($det->store_close));
													$CI->make->input('Closing Time','store_close',$closing_time,'',array('class'=>'rOkay timepicker'),null,fa('fa-clock-o'));
												$CI->make->eDiv();
											$CI->make->eDivCol();
										$CI->make->eDivRow();
										// $CI->make->input('TIN','tin',iSetObj($det,'tin'),'TIN',array('class'=>'rOkay'));
										// $CI->make->input('BIR #','bir',iSetObj($det,'bir'),'BIR',array());
										// $CI->make->input('Serial #','serial',iSetObj($det,'serial'),'Serial Number',array());
										$CI->make->input('Accreditation #','accrdn',iSetObj($det,'accrdn'),'Accreditation Number',array('readonly'=>'readonly'));
										$CI->make->input('Machine No.','machine_no',iSetObj($det,'machine_no'),'Machine Number',array());
										$CI->make->input('Permit#','permit_no',iSetObj($det,'permit_no'),'Permit Number',array());
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->input('Contact No.','contact_no',iSetObj($det,'contact_no'),'Type Contact Number',array());
										$CI->make->input('Delivery No.','delivery_no',iSetObj($det,'delivery_no'),'Type Delivery Number',array());
										$CI->make->textarea('Address','address',iSetObj($det,'address'),'Type Branch Address',array('class'=>'rOkay'));
										$CI->make->sDivRow();
											$CI->make->sDivCol(6);
												// $CI->make->sDiv(array('class'=>'bootstrap-timepicker'));
												// 	$CI->make->input('Opening Time','store_open',iSetObj($det,'store_open'),'',array('class'=>'rOkay timepicker'),null,fa('fa-clock-o'));
												// $CI->make->eDiv();
											$CI->make->input('TIN','tin',iSetObj($det,'tin'),'TIN',array('class'=>'rOkay'));
											$CI->make->eDivCol();
											$CI->make->sDivCol(6);
												// $CI->make->sDiv(array('class'=>'bootstrap-timepicker'));
												// 	$CI->make->input('Opening Time','store_open',iSetObj($det,'store_open'),'',array('class'=>'rOkay timepicker'),null,fa('fa-clock-o'));
												// $CI->make->eDiv();
											// $CI->make->input('BIR #','bir',iSetObj($det,'bir'),'BIR',array());
											$CI->make->input('Serial #','serial',iSetObj($det,'serial'),'Serial Number',array());
											$CI->make->eDivCol();
										$CI->make->eDivRow();
										$CI->make->input('Website','website',iSetObj($det,'website'),'Website',array());
										$CI->make->input('Email','email',iSetObj($det,'email'),'Email Address',array());
										
										// $CI->make->input('RLC Path','rob_path',iSetObj($det,'rob_path'),'RLC PATH',array());
										// $CI->make->input('RLC Username','rob_username',iSetObj($det,'rob_username'),'RLC Username',array());
										// $CI->make->input('RLC Password','rob_password',iSetObj($det,'rob_password'),'RLC Password',array());
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDivRow(array('style'=>'margin:10px;'));
									$CI->make->sDivCol(12, 'right');
											$CI->make->button(fa('fa-save fa-fw').' Save Details',array('id'=>'save-btn','class'=>''),'primary');
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								// $CI->make->sDivRow(array('style'=>'margin:10px;'));
								// 	$CI->make->sDivCol(6);
								// 		$CI->make->currenciesDrop('Currency','currency',iSetObj($det,'currency'),'',array());
								// 	$CI->make->eDivCol();
							$CI->make->eForm();
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'setup','class'=>'tab-pane'));
							$CI->make->sForm("setup/pos_settings_db",array('id'=>'settings_form'));
								$CI->make->H(3,'Printing');
								$CI->make->append('<hr style="margin-top:0px;">');
								$CI->make->sDivRow();
									$CI->make->sDivCol(3);
										$CI->make->number('No. of Receipt Prints on Settled','no_of_receipt_print',numInt(iSetObj($set,'no_of_receipt_print')),'',array('class'=>'rOkay'));
										$CI->make->input('Kitchen Printer Name','kitchen_printer_name',iSetObj($set,'kitchen_printer_name'),'');
										$CI->make->input('Beverage Printer Name','kitchen_beverage_printer_name',iSetObj($set,'kitchen_beverage_printer_name'),'');
										$CI->make->input('Printer With Open Cashdrawer','open_drawer_printer',iSetObj($set,'open_drawer_printer'),'');
									$CI->make->eDivCol();
									$CI->make->sDivCol(3);
										$CI->make->number('No. of Prints of Order Slip on Settled','no_of_order_slip_print',numInt(iSetObj($set,'no_of_order_slip_print')),'',array('class'=>'rOkay'));
										$CI->make->number('No. Of Kitchen Prints','kitchen_printer_name_no',iSetObj($set,'kitchen_printer_name_no'),'');
										$CI->make->number('No. Of Beverage Prints','kitchen_beverage_printer_name_no',iSetObj($set,'kitchen_beverage_printer_name_no'),'');
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$foot_rec = iSetObj($det,'rec_footer');
										if($foot_rec != "")
											$foot_rec = str_replace ("<br>","\r\n", $foot_rec );
										$CI->make->textarea('Restaurant Footer','rec_footer',$foot_rec,null);
										// $foot_pos = iSetObj($det,'pos_footer');
										// if($foot_pos != "")
										// 	$foot_pos = str_replace ("<br>","\r\n", $foot_pos );
										// $CI->make->textarea('POS Provider Footer','pos_footer',$foot_pos,null);
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->H(3,'Add On Charges');
								$CI->make->append('<hr style="margin-top:0px;">');
								$CI->make->sDivRow();
									$CI->make->sDivCol(3);
										$CI->make->decimal('Local Tax Percent','local_tax',numInt(iSetObj($set,'local_tax')),'',2,array('class'=>'rOkay'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->H(3,'Loyalty Card Settings');
								$CI->make->append('<hr style="margin-top:0px;">');
								$CI->make->sDivRow();
									$CI->make->sDivCol(3);
										$CI->make->decimal('For Every Amount','loyalty_for_amount',numInt(iSetObj($set,'loyalty_for_amount')),'',2,array('class'=>'rOkay'));
									$CI->make->eDivCol();
									$CI->make->sDivCol(3);
										$CI->make->decimal('To Points','loyalty_to_points',numInt(iSetObj($set,'loyalty_to_points')),'',2,array('class'=>'rOkay'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->H(3,'Controls');
								$CI->make->append('<hr style="margin-top:0px;">');
								$CI->make->sDivRow();
									$ids = explode(',',$set->controls);
									for($i=1;$i<=11;$i++){

										$falser = false;
										foreach ($ids as $value) {

											$text = explode('=>',$value);
											
												if($text[0] == $i){
													$CI->make->sDivCol(3);
														$CI->make->checkbox(strtoupper($text[1]),'chk['.$text[0].']',$text[0]."=>".$text[1],array(),true);
													$CI->make->eDivCol();
													$falser = true;
													break;
												}
											

										}
										if(!$falser){

											if($i == 1){
												$txt = "DINE IN";
											}elseif($i == 2){
												$txt = "DELIVERY";
											}elseif($i == 3){
												$txt = "COUNTER";
											}elseif($i == 4){
												$txt = "RETAIL";
											}elseif($i == 5){
												$txt = "PICKUP";
											}elseif($i == 6){
												$txt = "TAKEOUT";
											}elseif($i == 7){
												$txt = "DRIVE-THRU";
											}elseif($i == 8){
												$txt = "FOOD PANDA";
											}elseif($i == 9){
												$txt = "EATIGO";
											}elseif($i == 10){
												$txt = "BIGDISH";
											}elseif($i == 11){
												$txt = "HONESTBEE";
											}

											$CI->make->sDivCol(3);
												$CI->make->checkbox($txt,'chk['.$i.']',$i."=>".strtolower($txt),array());
											$CI->make->eDivCol();
										}
									}
								$CI->make->eDivRow();
								// $CI->make->sDivRow();
								// 	$CI->make->sDivCol(3);
								// 		$CI->make->checkbox('DINE IN','dinein',1,array());
								// 	$CI->make->eDivCol();
								// 	$CI->make->sDivCol(3);
								// 		$CI->make->checkbox('DELIVERY','dinein',2,array());
								// 	$CI->make->eDivCol();
								// $CI->make->eDivRow();
								$CI->make->sDivRow(array('style'=>'margin:10px;'));
									$CI->make->sDivCol(12, 'right');
											$CI->make->button(fa('fa-save fa-fw').' Save',array('id'=>'save-pos-btn','class'=>''),'primary');
									$CI->make->eDivCol();
								$CI->make->eDivRow();
							$CI->make->eForm();
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'image','class'=>'tab-pane'));
							$CI->make->H(3,'Splash Pages');
							$CI->make->append('<hr style="margin-top:0px;">');
							$CI->make->sDivRow();
								$CI->make->sDivCol(12,'right');
									$btnMsg = fa('fa-upload').' Upload Image';
									$CI->make->A($btnMsg,'setup/upload_splash_images/',array(
																				'id'=>'upload-splsh-img',
																				'rata-title'=>'Splash Image Upload',
																				'rata-pass'=>'setup/upload_splash_images_db',
																				'rata-form'=>'upload_image_form',
																				'class'=>'btn btn-primary'
																			));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
							$CI->make->sDivRow();
								foreach ($splashes as $res) {
									$CI->make->sDivCol(4);
										// $src ="data:image/jpeg;base64,".base64_encode($res->img_blob);
										$src = base_url().$res->img_path;
										$CI->make->img($src,array('style'=>'width:100%;margin-bottom:0px;margin-top:10px;','class'=>'thumbnail'));
										$CI->make->A(fa('fa-trash').'Delete','#',array('class'=>'del-spl-btn btn btn-danger ','ref'=>$res->img_id,'style'=>'margin:0px !important;'));
									$CI->make->eDivCol();
								}
							$CI->make->eDivRow();
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'database','class'=>'tab-pane'));
							$CI->make->H(3,'Database');
							$CI->make->append('<hr style="margin-top:0px;">');
							$CI->make->sForm("setup/pos_database_db",array('id'=>'database_form'));
								$CI->make->sDivRow();
									$CI->make->sDivCol(5,'left');
										$CI->make->input('Backup Path','backup_path',iSetObj($set,'backup_path'),'',array('class'=>'rOkay'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
								$CI->make->sDivRow();
									$CI->make->sDivCol(12,'left');
											$CI->make->button(fa('fa-save fa-fw').' Save Details',array('id'=>'save-db-btn','class'=>'','style'=>'margin-right:6px;'),'primary');
									// $CI->make->sDivCol(6,'right');
											$CI->make->button(fa('fa-download fa-fw').' Manual Back Up',array('id'=>'backup-db-btn','class'=>''),'success');
									// $CI->make->eDivCol();
									$CI->make->eDivCol();
								$CI->make->eDivRow();
							$CI->make->sDivRow(array('style'=>'margin: 20px 0 15px;'));
							$CI->make->eDivRow();
							$CI->make->eForm();
								$CI->make->sDiv(array('class'=>'note note-danger'));
									$CI->make->H(4,'Migrate Data to Master Server',array('class'=>'block','style'=>'color:red;'));
									$CI->make->P('This action will consolidate the data to the master server and will do price update on the menus based on master data. Please proceed with caution. ');
									$CI->make->button(fa('fa fa-warning').'  WARNING! ',array('id'=>'warning-db-button','class'=>'btn green-haze','style'=>'margin: 20px 0 15px;'));
								$CI->make->eDiv();
						$CI->make->eTabPane();
						// $CI->make->sTabPane(array('id'=>'Dlupdate','class'=>'tab-pane'));
						// 	$CI->make->sDiv(array('class'=>'note note-warning'));
						// 			$CI->make->H(4,'Migrate Data to Master Server',array('class'=>'block'));
						// 			$CI->make->P('This action will consolidate the data to the master server and will do price update on the menus based on master data. Please proceed with caution. ');
						// 			$CI->make->button(fa('fa fa-warning').'  WARNING! ',array('id'=>'warning-db-button','class'=>'btn green-haze','style'=>'margin: 20px 0 15px;'));
						// 		$CI->make->eDiv();
						// $CI->make->eTabPane();
						
					$CI->make->eTabBody();
				$CI->make->eTab();
				// $CI->make->eBoxBody();
			// $CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function makeImageUploadForm($det=null){
	$CI =& get_instance();
		$CI->make->sForm("setup/upload_splash_images_db",array('id'=>'upload_image_form','enctype'=>'multipart/form-data'));
			$CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
				$CI->make->sDivCol();
					$CI->make->A(fa('fa-picture-o').' Select an Image','#',array(
															'id'=>'select-img',
															'class'=>'btn btn-primary'
														));
					$CI->make->append('<br>');
				$CIa->make->eDivCol();
			$CI->make->eDivRow();
			$CI->make->sDivRow();
				$CI->mke->sDivCol();
					$thumb = base_url().'img/noimage.png';
					// if(iSetObj($det,'image')  != ""){
					// 	$thumb = base_url().'uploads/'.iSetObj($det,'image');
					// }
					$CI->make->img('',array('class'=>'media-object thumbnail','id'=>'target','style'=>'width:100%;'));
					$CI->make->file('fileUpload',array('style'=>'display:none;'));
				$CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}
//-----------Branch References-----end-----allyn
function makeBrandsForm($brands = null,$branch=null)
{
	$CI =& get_instance();
	$CI->make->sForm("setup/brands_db",array('id'=>'charges_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px'));
			$CI->make->sDivCol(6);
				$CI->make->hidden('id',iSetObj($brands,'id'));
				$CI->make->input('Brand Code','brand_code',iSetObj($brands,'brand_code'),'Brand Code',array('class'=>'rOkay'));
				$CI->make->input('Brand Name','brand_name',iSetObj($brands,'brand_name'),'Brand Name',array('class'=>'rOkay'));
				// $CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
				// 	$CI->make->input('Rate'
				// 		, 'charge_amount'
				// 		, iSetObj($charges,'charge_amount')
				// 		, 'Rate'
				// 		, array('class'=>'rOkay','style'=>'width:85px')
				// 	);
				// 	$CI->make->inactiveDrop('Absolute','absolute',iSetObj($charges,'absolute'),'',array('style'=>'width: 85px;'));
				// $CI->make->eDivCol();
				// $CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-15px;'));
				// 	// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($receipt_disc,'inactive'),'',array('style'=>'width: 85px;'));
				// 	$CI->make->inactiveDrop('Tax Exempt','no_tax',iSetObj($charges,'no_tax'),'',array('style'=>'width: 85px;'));
				// $CI->make->eDivCol();
				if($branch != null){
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($brands,'inactive'),'',array('style'=>'width: 85px;'));
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("brands","id",iSetObj($brands,'id'))); //table , where id, id needed
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
			$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-menu','class'=>'btn btn-success-600 radius-8 px-16 py-9'));
		$CI->make->eDivCol();
    $CI->make->eDivRow();
	return $CI->make->code();
}
?>