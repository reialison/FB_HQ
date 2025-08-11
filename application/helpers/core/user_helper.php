<?php
function makeUserForm($user=array(),$img=null,$det=null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
				// $CI->make->sBox('primary');  
					// $CI->make->sBoxBody();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'user',array('return'=>true)),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'user/users_form','id'=>'details_link'),
						// fa('fa-book')." Branches"=>array('href'=>'#branches','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'user/user_branches/','id'=>'branches'),
					);
					
					$CI->make->sTabBody();
					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'branches','class'=>'tab-pane'));
					$CI->make->eTabPane();
						$CI->make->sForm("core/user/users_db",array('id'=>'users_form'));
							/* GENERAL DETAILS */
							$CI->make->sDivRow(array('style'=>'margin:10px;'));
								$CI->make->sDivCol(2);
									$url = base_url().'img/avatar.jpg';
									if(iSetObj($img,'img_path') != ""){					
										$url = base_url().$img->img_path;
									}
									$CI->make->img($url,array('style'=>'width:100%;max-width: 200px; margin: 0 auto;','class'=>'media-object thumbnail','id'=>'target'));
									$CI->make->file('fileUpload',array('style'=>'display:none;'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(3);
									$CI->make->hidden('id',iSetObj($user,'id'));
									$CI->make->input('First Name','fname',iSetObj($user,'fname'),'First Name',array('class'=>'rOkay'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(3);
									$CI->make->input('Middle Name','mname',iSetObj($user,'mname'),'Middle Name',array());
								$CI->make->eDivCol();
								$CI->make->sDivCol(3);
									$CI->make->input('Last Name','lname',iSetObj($user,'lname'),'Last Name',array('class'=>'rOkay'));
								$CI->make->eDivCol();
								$CI->make->sDivCol(1);
									$CI->make->input('Suffix','suffix',iSetObj($user,'suffix'),'Suffix',array());
								$CI->make->eDivCol();
							$CI->make->eDivRow();

							$CI->make->sDivRow(array('style'=>'margin:10px;'));
								$CI->make->sDivCol(6);
										$CI->make->input('Username','uname',iSetObj($user,'username'),'Username',array('class'=>'rOkay',iSetObj($user,'id')?'disabled':''=>''));
										// if(!iSetObj($user,'id'))
										$CI->make->input('Password','password',iSetObj($user,'password'),'Password',array('type'=>'password','class'=>'rOkay'));
										$CI->make->input('Email','email',iSetObj($user,'email'),'Email',array('class'=>''));							
										// $CI->make->hidden('sys_user_id',$sys_user_id);
										$CI->make->hidden('sys_user_id',iSetObj($user,'sysid'));
										// $CI->make->hidden('branch_id',$branch_id);
										// print_r($user);exit;
										

										if($user){
											$CI->make->sDivRow();
												$CI->make->sDivCol(10);
													$CI->make->branchesAjaxDrop('Branches','branches-search');
												$CI->make->eDivCol();
												$CI->make->sDivCol(2);
													$CI->make->A(fa('fa-lg fa-plus')." Add",'#',array('class'=>'btn btn-primary blockui_sample_2_1','id'=>'add-branch','style'=>'margin-top:23px;'));
												$CI->make->eDivCol();

												$CI->make->sDivRow(array('style'=>'margin:10px;'));
													$CI->make->sDivCol(12);
														$CI->make->sUl(array('class'=>'vertical-list','id'=>'branch-list'));
															foreach ($det as $res) {
																// echo "<pre>",print_r($res->id),"</pre>";die();
																$chck = false;
																// if($res['default'] == 1){
																	// $chck = true;
																// }
																$li = $CI->make->li(
																	$CI->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
																	$CI->make->span($res->branch_code,array('class'=>'text','return'=>true))." ".
																	$CI->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$res->id,'ref'=>$res->id,'ref3'=>$res->sysid,'ref2'=>$res->branch_id)),
																	array('id'=>'li-'.$res->id)
																);
															}
														$CI->make->eUl();
													$CI->make->eDivCol();
												$CI->make->eDivRow();

											$CI->make->eDivRow();
										}																	

								
								
								$CI->make->sDivRow(array('style'=>'margin:10px;'));

								$CI->make->eDivRow();
						

								$CI->make->eDivCol();
								$CI->make->sDivCol(6);
										$CI->make->roleDrop('Role','role',iSetObj($user,'role'),'Role',array());
										$CI->make->genderDrop('Gender','gender',iSetObj($user,'gender'),array('class'=>'rOkay'));
										$CI->make->inactiveDrop('Inactive','inactive',iSetObj($user,'inactive'));
			
										// if(ENABLE_ADD_LOCAL_USER){
										// 	$CI->make->branchesDrop('Branch Code','branch_id',iSetObj($user,'branch_code'),'HQ');
										// }
										
										
										$CI->make->hidden('pin',iSetObj($user,'pin'),array('class'=>'' ,'style'=>'display:none;'));
								$CI->make->eDivCol();
							$CI->make->eDivRow();
							/* GENERAL DETAILS END */
						$CI->make->H(4,"",array('class'=>'page-header'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(4,'left',3);
								$CI->make->button(fa('fa-save')." Save Details",array('id'=>'save-btn','class'=>''),'success');
							$CI->make->eDivCol();
							$CI->make->sDivCol(2);
								$CI->make->A(fa('fa-reply')." Go Back",base_url().'user',array('class'=>'btn btn-primary'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						// $CI->make->eForm();
						// $CI->make->sForm("core/user/users_db",array('id'=>'user_branches'));
						// $CI->make->sDivRow();
						// $CI->make->sDivCol(4);
						// 	// $CI->make->input(null,'item-search',null,'Search Modifiers',array('search-url'=>'mods/search_modifiers'),'',fa('fa-search'));
						// 	$CI->make->branchesAjaxDrop('Search Branches','branches-search');
						// $CI->make->eDivCol();				
						// $CI->make->sDivCol(6);
						// 	$CI->make->allbranchesDrop('Branch Code','b_code[]',null,null,array("multiple"=>""));
						// $CI->make->eDivCol();				
						// $CI->make->sDivCol(2);
						// 	$CI->make->A(fa('fa-lg fa-plus')." Add",'#',array('class'=>'btn btn-primary blockui_sample_2_1','id'=>'add-modifier','style'=>'margin-top:23px;'));
						// $CI->make->eDivCol();				
						// $CI->make->eDivRow();
						// $CI->make->H(5,'Note: Check the branches to auto add it.');
						// $CI->make->sDivRow();
						// $CI->make->sDivCol(4);
						// 	$CI->make->sUl(array('class'=>'vertical-list','id'=>'modifier-list'));
						// 			// echo "<pre>",print_r($det),"</pre>";die();
						// 		// foreach ($det as $res) {
						// 		// 	$chck = false;
						// 		// 	if($res->default == 1){
						// 		// 		$chck = true;
						// 		// 	}
						// 		// 	$li = $CI->make->li(
						// 		// 		$CI->make->checkbox(null,'dflt_'.$res->id,0,array('class'=>'dflt','ref'=>$res->id,'return'=>true),$chck)." ".
						// 		// 		$CI->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
						// 		// 		$CI->make->span($res->mod_name,array('class'=>'text','return'=>true))." ".
						// 		// 		$CI->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$res->id,'ref'=>$res->id,'ref2'=>$res->branch_code,'ref3'=>$res->mod_id,'hidden'=>'hidden')),
						// 		// 		array('id'=>'li-'.$res->id)
						// 		// 	);
						// 		// }
						// 	$CI->make->eUl();
						// $CI->make->eDivCol();				
						$CI->make->eDivRow();
					$CI->make->eForm();
						
						$CI->make->eForm();
					$CI->make->eBoxBody();  
					$CI->make->eTabBody();  
				// $CI->make->eBox();
			$CI->make->eTab();
		$CI->make->eDivCol();
		
	$CI->make->eDivRow();
	
	return $CI->make->code();
}
function makeUserAccessForm($role=array()){
	$CI =& get_instance();

	$CI->make->sForm("user/user_access_db",array('id'=>'user_permissions_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(3);
				$CI->make->hidden('id',iSetObj($role,'id'));
				$CI->make->input('Role Name','role',iSetObj($role,'role'),'Role',array('class'=>'rOkay'));
			$CI->make->eDivCol();
			$CI->make->sDivCol(9);
				$CI->make->input('Description','description',iSetObj($role,'description'),'Description',array());
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			// $CI->make->sDivCol(12);
				$CI->make->sBox('success');
                    $CI->make->sBoxHead();
                        $CI->make->boxTitle('Attendance');
                    $CI->make->eBoxHead();
                    $CI->make->sBoxBody();
                        // $list = array();
                        // // $icon = $CI->make->icon('fa-plus');
                        // $list[fa('fa-plus').' Add New'] = array('id'=>'add-new','class'=>'grp-list');
                        // foreach($lists as $val){
                        //     $name = "";
                        //     if(!is_array($desc))
                        //       $name = $val->$desc;
                        //     else{
                        //         foreach ($desc as $dsc) {
                        //            $name .= $val->$dsc." ";
                        //         }
                        //     }
                        //     $list[$name] = array('class'=>'grp-btn grp-list','id'=>'grp-list-'.$val->$ref,'ref'=>$val->$ref);
                        // }
                        // $CI->make->listGroup($list,array('id'=>'add-grp-list-div'));
                    $CI->make->eBoxBody();
                $CI->make->eBox();
			// $CI->make->eDivCol();
    	$CI->make->eDivRow();
	return $CI->make->code();
}
// function userBranchesLoad($mod_group_id=null,$det=null){
// 	$CI =& get_instance();
// 	$CI->make->sTab();
// 				// $CI->make->sBox('primary');  
// 					// $CI->make->sBoxBody();
// 					$tabs = array(
// 						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'user',array('return'=>true)),
// 						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'user/users_form','id'=>'details_link'),
// 						fa('fa-book')." Branches"=>array('href'=>'#branches','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'user/user_branches/','id'=>'branches'),
// 					);
// 					$CI->make->sTabBody();
// 					$CI->make->tabHead($tabs,null,array());
// 					$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
// 						$CI->make->eTabPane();
// 						$CI->make->sTabPane(array('id'=>'branches','class'=>'tab-pane'));
// 					$CI->make->eTabPane();
// 					/*BRANCHES*/
// 					$CI->make->sForm("core/user/users_db",array('id'=>'user_branches'));
// 						$CI->make->sDivRow();
// 						$CI->make->sDivCol(4);
// 							// $CI->make->input(null,'item-search',null,'Search Modifiers',array('search-url'=>'mods/search_modifiers'),'',fa('fa-search'));
// 							$CI->make->modifiersAjaxDrop('Search Modifier','item-search');
// 						$CI->make->eDivCol();				
// 						$CI->make->sDivCol(6);
// 							$CI->make->allbranchesDrop('Branch Code','b_code[]',null,null,array("multiple"=>""));
// 						$CI->make->eDivCol();				
// 						$CI->make->sDivCol(2);
// 							$CI->make->A(fa('fa-lg fa-plus')." Add",'#',array('class'=>'btn btn-primary blockui_sample_2_1','id'=>'add-modifier','style'=>'margin-top:23px;'));
// 						$CI->make->eDivCol();				
// 						$CI->make->eDivRow();
// 						$CI->make->H(5,'Note: Check the modifier to auto add it.');
// 						$CI->make->sDivRow();
// 						$CI->make->sDivCol(4);
// 							$CI->make->sUl(array('class'=>'vertical-list','id'=>'modifier-list'));
// 									// echo "<pre>",print_r($det),"</pre>";die();
// 								foreach ($det as $res) {
// 									$chck = false;
// 									if($res->default == 1){
// 										$chck = true;
// 									}
// 									$li = $CI->make->li(
// 										$CI->make->checkbox(null,'dflt_'.$res->id,0,array('class'=>'dflt','ref'=>$res->id,'return'=>true),$chck)." ".
// 										$CI->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
// 										$CI->make->span($res->mod_name,array('class'=>'text','return'=>true))." ".
// 										$CI->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$res->id,'ref'=>$res->id,'ref2'=>$res->branch_code,'ref3'=>$res->mod_id,'hidden'=>'hidden')),
// 										array('id'=>'li-'.$res->id)
// 									);
// 								}
// 							$CI->make->eUl();
// 						$CI->make->eDivCol();				
// 						$CI->make->eDivRow();
// 					$CI->make->eForm();
// 				$CI->make->eTabBody();
// 			$CI->make->eTab();
// 		return $CI->make->code();
// }
?>