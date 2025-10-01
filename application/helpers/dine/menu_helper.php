<?php
function menuListPage($list=array()){
	$CI =& get_instance();

	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12,'right');
							 $CI->make->A(fa('fa-plus').' Add New Menu',base_url().'menu/form',array('class'=>'btn btn-primary'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                	$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array('Menu Code'=>'',
									'Barcode'=>'',
									'Short Description'=>'',
									'Name'=>'',
									'Category'=>'',
									'Schedule'=>'',
									'Price'=>'',
									' '=>array('width'=>'12%','align'=>'right'));
							$rows = array();
							foreach($list as $v){
								$links = "";
								$links .= $CI->make->A(fa('fa-edit fa-2x fa-fw'),base_url().'menu/form/'.$v->menu_id,array("return"=>true));
								$rows[] = array(
											  $v->menu_code,
											  $v->menu_barcode,
											  $v->menu_name,
											  $v->menu_short_desc,
											  $v->category_name,
											  $v->menu_schedule_name,
											  $v->cost,
											  $links
									);
							}
							$CI->make->listLayout($th,$rows);
						$CI->make->eDivCol();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function menuUploadForm(){
	$CI =& get_instance();
	$CI->make->H(3,'Warning! THIS WILL REPLACE ALL THE MENUS.',array('class'=>'label label-warning','style'=>'margin-bottom:50px;font-size:24px;'));
	// $CI->make->sForm('menu/upload_excel_db',array('id'=>"upload-form",'enctype'=>'multipart/form-data'));
	$CI->make->sForm('menu/upload_excel_db',array('id'=>"upload-form",'enctype'=>'multipart/form-data'));
		$CI->make->sDivRow(array('style'=>'margin-top:30px;'));
			$CI->make->sDivCol(6);
				$CI->make->file('menu_excel',array());
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function menuSearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Menu Name','menu_name',null,null);
				$CI->make->menuCategoriesDrop('Categories','menu_cat_id',null,'- select category -');
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
				$CI->make->branchesDrop('Branch','branch_id',null,'- select branch -');

			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function exportItemsForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm('menu/export_data',array('id'=>"export-form"));
		$CI->make->sDivRow();
			$CI->make->sDivCol(1);
			$CI->make->eDivCol();			
			$CI->make->sDivCol(8);
				// $CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
				$CI->make->branchesDrop('Branch','branch_id',null,'- select branch -');

			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function menuCatSearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input('Menu Category Name','menu_cat_name',null,null);
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
			$CI->make->branchesDrop('Branch','branch_id',null,'Select Branch');
			$CI->make->eDivCol();
			$CI->make->sDivCol(4);
				$CI->make->inactiveDrop('Is Inactive','inactive',null,null,array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function menuFormPage($menu_id=null,$img=null,$branch_code=null){
	$CI =& get_instance();
	$CI->make->hidden('menu_id',$menu_id);
		$CI->make->hidden('branch_code',$branch_code);

	$CI->make->sDivRow(array('style'=>'margin-bottom:5px;'));
			$CI->make->sDivCol(12,'right',0,array('style'=>'margin-top:0px;'));
				$CI->make->A(fa('fa-reply').' Go back to list',base_url().'menu',array('class'=>'btn btn-success-600 radius-8 px-16 py-9','style'=>'margin-top:0px;'));
			$CI->make->eDivCol();
		$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol(3);
			$CI->make->sBox('solid',array('style'=>'margin-bottom:5px;'));
				$CI->make->sBoxBody();
					$src = base_url().'img/noimage.png';
					if($menu_id != null && $img != ""){
						$src = base_url().$img;
					}
					$CI->make->sDiv(array('style'=>'position:relative;width:100%;background-color:#ddd;'));
						$CI->make->img($src,array('style'=>'width:100%;max-height:250px;','id'=>'item-pic'));
						if($menu_id != null){
							$CI->make->sDiv(array('style'=>'position:absolute;bottom:0;left:0;width:100%;height:30px;text-align:right;padding-right:5px;color:#fff'));
								$CI->make->A(fa('fa-camera fa-2x'),'#',array('style'=>'color:#fff;','id'=>'target','title'=>'Upload Picture'));
								$CI->make->sForm("menu/images_db",array('id'=>'pic-form'));
									$CI->make->file('fileUpload',array('style'=>'display:none;'));
									$CI->make->hidden('upload_menu_id',$menu_id);	
								$CI->make->eForm();
							$CI->make->eDiv();
						}
					$CI->make->eDiv();
				$CI->make->eBoxBody();
			$CI->make->eBox();
					$list[fa('icon-info').' General Details'] = array('id'=>'details_link','class'=>'tab_link',
																		   'load'=>'menu/details_load/','href'=>'#details',	
					                                        			   'style'=>'cursor:pointer;padding:10px;text-align:left;');
					if($menu_id != null){
						$list[fa('fa-book').' Recipe'] = array('id'=>'recipe_link','class'=>'tab_link',
																			   'load'=>'menu/recipe_load/','href'=>'#details',	
						                                        			   'style'=>'cursor:pointer;padding:10px;text-align:left;');
						$list[fa('fa-asterisk').' Modifiers'] = array('id'=>'modifier_link','class'=>'tab_link',
																			   'load'=>'menu/modifier_load/','href'=>'#details',	
						                                        			   'style'=>'cursor:pointer;padding:10px;text-align:left;');
						$list[fa('fa-money').' Cost price'] = array('id'=>'cost_link','class'=>'tab_link',
																			   'load'=>'menu/price_listing/','href'=>'#details',	
						                                        			   'style'=>'cursor:pointer;padding:10px;text-align:left;');
						$list[fa('fa-list-ul').' Price History'] = array('id'=>'history_link','class'=>'tab_link',
																			   'load'=>'menu/price_history/','href'=>'#details',	
						                                        			   'style'=>'cursor:pointer;padding:10px;text-align:left;');
						$list[fa('fa-money').' Prices'] = array('id'=>'price_link','class'=>'tab_link',
																				   'load'=>'menu/price_load/','href'=>'#details',	
							                                        			   'style'=>'cursor:pointer;padding:10px;text-align:left;');

					}
					$CI->make->listGroup($list);
		$CI->make->eDivCol();
		$CI->make->sDivCol(9,'left',0,array('style'=>'margin-bottom:50px;'));
			$CI->make->sBox('solid',array('style'=>'margin-bottom:5px;'));
			$CI->make->sBoxBody(array('id'=>'details'));
			$CI->make->eBoxBody();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}	
function menuFormPage2($menu_id=null){
	$CI =& get_instance();
	// $CI->make->sDivRow(array('style'=>'margin-bottom:10px;'));
	// 		$CI->make->sDivCol(12,'right');
	// 			$CI->make->A(fa('fa-reply').' Go back to list',base_url().'menu',array('class'=>'btn btn-primary'));
	// 		$CI->make->eDivCol();
	// 	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'menu',array('return'=>true)),
						fa('icon-info')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'menu/details_load/','id'=>'details_link'),
						fa('fa-book')." Recipe"=>array('href'=>'#recipe','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'menu/recipe_load/','id'=>'recipe_link'),
						fa('fa-asterisk')." Modifiers"=>array('href'=>'#modifiers','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'menu/modifier_load/','id'=>'modifier_link'),
						fa('fa-picture-o')." Image Upload"=>array('href'=>'#image','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'menu/upload_image_load/','id'=>'image_link'),
					);
					$CI->make->hidden('menu_id',$menu_id);
					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabBody(array('style'=>'min-height:202px;'));
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'recipe','class'=>'tab-pane'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'modifiers','class'=>'tab-pane'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'image','class'=>'tab-pane'));
						$CI->make->eTabPane();
					$CI->make->eTabBody();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function menuImagesLoad($menu_id=null,$res=null){
	$CI =& get_instance();
		$CI->make->sForm("menu/images_db",array('id'=>'images_form','enctype'=>'multipart/form-data'));
			$CI->make->hidden('form_menu_id',$menu_id);
			$CI->make->sDivRow();
				$img = base_url().'img/no_image.jpg';
				if(iSetObj($res,'img_path') != ""){					
					$img = base_url().$res->img_path;
				}
				$CI->make->sDivCol(12,'center');
					$CI->make->img($img,array('class'=>'media-object thumbnail','id'=>'target','style'=>'max-height:220px;margin:0 auto;'));
					$CI->make->file('fileUpload',array('style'=>'display:none;'));
				$CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
		$CI->make->sDiv(array('style'=>'margin-top:10px;'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(12,'center');
					$CI->make->button(fa('fa-save').' Save Image',array('id'=>'save-image'),'primary');
				$CI->make->eDivCol();
		    $CI->make->eDivRow();
	    $CI->make->eDiv();
	return $CI->make->code();
}
function menuDetailsLoad($menu=null,$menu_id=null,$branch_code=null){
	// echo "<pre>",print_r($menu),"</pre>";die();
	
	$date = iSetObj($menu,'date_effective');
	$date_effective = date('m/d/Y');// !in_array($date, array('','0000-00-00')) ? sql2Date($date) : date('m/d/Y');

	$CI =& get_instance();
		$CI->make->sForm("menu/details_db",array('id'=>'details_form'));
			$CI->make->hidden('form_menu_id',$menu_id);
			$readonly = $menu_id > 0 ? 'readonly' : '';
			// $CI->make->hidden('form_menu_id',$menu_id);

	    	$CI->make->H(3,fa('icon-info').'General Details',array('class'=>'page-header'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->hidden('f_branch_id',$branch_code);
					$CI->make->input('Code','menu_code',iSetObj($menu,'menu_code'),'Type Code',array('class'=>'rOkay','maxlength'=>'15',$readonly=>''));
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->input('Barcode','menu_barcode',iSetObj($menu,'menu_barcode'),'Type Barcode',array('class'=>'rOkay',$readonly=>''));
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->input('Short Description','menu_name',iSetObj($menu,'menu_name'),'Type Short Desc',array('class'=>'rOkay','maxlength'=>40,$readonly=>''));
				$CI->make->eDivCol();
	    	$CI->make->eDivRow();

	    	$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->textarea('Description','menu_short_desc',iSetObj($menu,'menu_short_desc'),'Type Name',array('class'=>'rOkay','maxlength'=>'50',$readonly=>''));
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->menuCategoriesDrop('Category','menu_cat_id',iSetObj($menu,'menu_cat_id'),'Select Category',array('class'=>'rOkay'));
					$CI->make->menuSubCategoriesDrop('Menu Type','menu_sub_cat_id',iSetObj($menu,'menu_sub_cat_id'),'Select Menu Type',array('class'=>'rOkay'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->menuSchedulesDrop('Schedule','menu_sched_id',iSetObj($menu,'menu_sched_id'),'Select Schedule',array());
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->menuSubDrop('Subcategory','menu_sub_id',iSetObj($menu,'menu_sub_id'),'Select Subcategory',array('class'=>''));
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
					$CI->make->brandDbDrop('Brand','brand',iSetObj($menu,'brand'),'Select Brand',array('class'=>'rOkay'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(4);
						$CI->make->foodTypeDrop('Food Type','food_type',iSetObj($menu,'food_type'),null,array('class'=>'rOkay'));
					$CI->make->eDivCol();
				if(MENU_OTHER_DESC){
					$CI->make->sDivCol(4);
						$CI->make->input('Other Description','menu_other_desc',iSetObj($menu,'menu_other_desc'),'Type Other Desc',array('class'=>''));
					$CI->make->eDivCol();
				}
			$CI->make->eDivRow();
			$CI->make->sDivRow();
				$CI->make->sDivCol(12);
					// echo "<pre>",print_r($menu),"</pre>";die();
					if($menu_id != null){
					// 	// $CI->make->h(5,'Branch Name:',array('style'=>'font-weight:bold;'));
					// 	// $CI->make->h(5,iSetObj($menu,'branch_name'));
					$CI->make->sDivRow();
						$CI->make->sDivCol(3);
						$CI->make->inactiveDrop('Inactive','inactive',iSetObj($menu,'inactive'),null,array('style'=>'width:85px;'));
						$CI->make->eDivCol();						
					$CI->make->eDivRow();
						// $CI->make->input('Branch Code:','b_code',iSetObj($menu,'branch_code'),'Branch Code',array('class'=>'rOkay','style'=>'width:200px;','readonly'=>'readonly'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("menus","menu_id",$menu_id)); //table , where id, id needed
					}
					else{
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'','class'=>'bootstrap-select'));		
						
					}
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			if(KITCHEN_SEQUENCE){
				$CI->make->sDivRow();
					$CI->make->sDivCol(3);
						$CI->make->input('Kitchen Order Sequence','kitchen_number',iSetObj($menu,'kitchen_number'),'',array('class'=>'rOkay'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->input('Time Preparation (minutes)','time_preparation',iSetObj($menu,'time_preparation'),'',array('class'=>'rOkay'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			}
	    	$CI->make->H(3,fa('icon-calculator').' Pricing Details',array('class'=>'page-header'));
	    	$CI->make->sDivRow();
				$CI->make->sDivCol(3);
					$CI->make->input('Cost','costing',iSetObj($menu,'costing'),'Price',array('class'=>'rOkay','style'=>'width:120px;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->input('Selling Price','cost',iSetObj($menu,'cost'),'Price',array('class'=>'rOkay','style'=>'width:120px;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->inactiveDrop('Is Tax Exempt','no_tax',null,null,array('style'=>'width:85px;'));
				$CI->make->eDivCol();
				$CI->make->sDivCol(3);
					$CI->make->inactiveDrop('Free','free',null,null,array('style'=>'width:85px;'));
				$CI->make->eDivCol();
			$CI->make->eDivRow();


			$CI->make->sDivRow();
				$CI->make->sDivCol(5);
					if($date){
						$d = date('m/d/Y',strtotime(iSetObj($menu,'date_effective')));

					}else{
						$d = $date_effective;
					}
					$CI->make->date('Date Effective','date_effective',$d);
				$CI->make->eDivCol();
			$CI->make->eDivRow();
			if(MALL_ENABLED){
            	if(MALL == 'miaa'){
					$CI->make->H(3,fa('fa fa-info-circle').' MIAA Details',array('class'=>'page-header'));
			    	$CI->make->sDivRow();
						$CI->make->sDivCol(6);
							$CI->make->miaaCategoriesDrop('MIAA Category','miaa_cat',(iSetObj($menu,'miaa_cat')),'Select Category',array('style'=>''));
						$CI->make->eDivCol();
					$CI->make->eDivRow();
                }
            }

		$CI->make->eForm();

    	$CI->make->H(3,"",array('class'=>'page-header'));

		$CI->make->sDivRow();
		
			$CI->make->sDivCol(3,'left',3);
				$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-menu','class'=>'btn btn-success-600 radius-8 px-16 py-9'),'success');
			$CI->make->eDivCol();
			// $CI->make->sDivCol(3,'left');
			// 	$CI->make->button(fa('fa-save').' Save As New',array('id'=>'save-new-menu','class'=>'btn-block'),'info');
			// $CI->make->eDivCol();
	    $CI->make->eDivRow();
	return $CI->make->code();
}
function menuRecipeLoad($menu_id,$recipe=null,$det=array(),$branch){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-book').' Recipe',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(4);
			$CI->make->sForm('menu/recipe_details_db',array('id'=>'recipe-details-form'));
				$CI->make->hidden('menu-id-hid',$menu_id);
				$CI->make->hidden('recipe-branch-hid',$branch);
				$CI->make->sDivRow();
					$CI->make->sDivCol();
						// $CI->make->input('Search Item','item-search',null,'Search for item',array('search-url'=>'menu/recipe_search_item'),'',fa('fa-search'));
						// $CI->make->input('Unit of Measurement','item-uom',null,'',array('readOnly'=>'readOnly'));
						$CI->make->itemAjaxDrop('Item','item-search',null,array());
						$uomTxt = $CI->make->span('&nbsp;&nbsp;',array('return'=>true,'id'=>'uom-txt'));
						$CI->make->input('Cost','item-cost',null,null,array('readOnly'=>'readOnly'));
						$CI->make->input('Quantity','qty',null,null,array(),'',$uomTxt);
						// $CI->make->allbranchesDrop('Branch Code','b_code');
						$CI->make->hidden('item-id-hid',null,array('class'=>'rOkay','ro-msg'=>'Please select an item'));
						$CI->make->hidden('item-uom-hid',null,array('class'=>'rOkay','ro-msg'=>'Please select an item'));
						$CI->make->hidden('item-branch-hid',null,array('class'=>'rOkay','ro-msg'=>'Please select an item'));
						$CI->make->hidden('d',0);
						$CI->make->button(fa('fa-plus').' Add item to recipe',array('id'=>'add-btn'),'primary btn-block');
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
    		$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-right:16px;float:right;'));
				$CI->make->button(fa('fa-print').' Print',array('id'=>'all-branch-print','class'=>'pull-left','style'=>'margin-top:24px;'),'success');
    		$CI->make->eDiv();
    	$CI->make->sDivCol(8);
    		$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
    					$CI->make->sRow();
	    					$CI->make->th('Item');
	    					$CI->make->th('UOM');
	    					$CI->make->th('Unit Price');
	    					$CI->make->th('Quantity');
	    					$CI->make->th('Line Total');
	    					$CI->make->th();
	    				$CI->make->eRow();
    					$total = 0;
    					foreach ($det as $val) {
    						$CI->make->sRow(array('id'=>'row-'.$val->recipe_id));
    							$CI->make->td($val->item_name);
    							$CI->make->td($val->uom);
    							$CI->make->td($val->item_cost);
    							$CI->make->td($val->qty);
    							$CI->make->td(num($val->item_cost * $val->qty));
    							$a = $CI->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$val->recipe_id,'ref'=>$val->recipe_id,'class'=>'del-item','return'=>true));
            					$CI->make->td($a);
    						$CI->make->eRow();
    						$total += $val->item_cost * $val->qty;
    					}
    					$CI->make->sRow();
	    					$CI->make->td('');
	    					$CI->make->td('');
	    					$CI->make->td('');
	    					$CI->make->td('');
	    					$CI->make->td('Total');
	    					$CI->make->td(num($total),array('id'=>'total'));
	    				$CI->make->eRow();
    			$CI->make->eTable();
    		$CI->make->eDiv();
    	$CI->make->eDivCol();
	$CI->make->eDivRow();
	// $CI->make->sDivRow(array("style"=>"margin-top:20px;"));
	// 	$CI->make->sDivCol(4,'left','8');
	// 		$pop = $CI->make->A(fa('fa-save fa-fw '),'#',array('id'=>'override-price','return'=>true));
	// 		$sell_price = iSetObj((!empty($det[0]) ? $det[0] : null),'menu_cost');
	// 		$CI->make->input('Selling Price','total',num($sell_price),null,array(),null,$pop);
	// 	$CI->make->eDivCol();
	// $CI->make->eDivRow();

	return $CI->make->code();
}
function menuModifierLoad($menu_id,$det = null){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-asterisk').' Modifiers',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(8);
			$CI->make->sForm('menu/menu_modifier_db',array('id'=>'menu-modifier-form'));
				$CI->make->hidden('menu-id-hid',$menu_id);
				$CI->make->sDivRow();
					$CI->make->sDivCol(8);
						// $CI->make->modifiersAjaxDrop('Search Item','item-search',null,array());
						// $CI->make->input('Search Item','item-search',null,'Search for item',array('search-url'=>'menu/modifier_search_item'),'',fa('fa-plus'));
						// $CI->make->button(fa('fa-plus').' Add Modifier Group',array('id'=>'add-btn'),'primary btn-block');
						$CI->make->modifiersGroupAjaxDrop('Search Group Modifier','item-search');
						$CI->make->hidden('mod-group-id-hid',null,array('class'=>'rOkay','ro-msg'=>'Please select a modifier'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
		                $CI->make->A(fa('fa-lg fa-plus')." ADD",'#',array('class'=>'btn btn-primary','id'=>'add-grp-modifier','style'=>'margin-top:23px;'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
	$CI->make->eDivRow();	
	$CI->make->sDivRow();	
		$CI->make->sDivCol(7);
			$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
    					$total = 0;
    					foreach ($det as $val) {
    						$CI->make->sRow(array('id'=>'row-'.$val->id));
    							$CI->make->td(fa('fa-asterisk')." ".$val->mod_group_name);
    							$a = $CI->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'del-'.$val->id,'ref'=>$val->id,'class'=>'del-item','return'=>true,'hidden'=>'hidden'));
            					$CI->make->td($a,array('style'=>'text-align:right'));
    						$CI->make->eRow();
    					}
    			$CI->make->eTable();
    		$CI->make->eDiv();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}

function menuRecipeForm($menu_id=null,$recipe=null,$det=array())
{
	$CI =& get_instance();

	$CI->make->sDivRow(array('style'=>'margin-bottom:10px'));
		$CI->make->sDivCol(12,'right');
			$CI->make->A(" ".fa('fa-reply')." Go back",base_url().'menu/form/'.$menu_id,array('class'=>'btn btn-default'));
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxHead();
					$CI->make->boxTitle(4,fa('fa-archive').' Recipe Details');
				$CI->make->eBoxHead();
				$CI->make->sBoxBody();
					$CI->make->sForm('menu/recipe_details_db',array('id'=>'recipe-details-form'));
						$CI->make->hidden('recipe-id-hid',iSetObj($recipe,'recipe_id'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(4);
								$CI->make->input(null,'item-search',null,'Search for item',array('search-url'=>'menu/search_items','add-data'=>'menu_id='+$menu_id));
								$CI->make->hidden('item-id-hid',null,array('class'=>'rOkay','ro-msg'=>'Please select an item'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(1);
								$CI->make->H(4,'',array('id'=>'item-uom'));
								$CI->make->hidden('item-uom-hid',0);
							$CI->make->eDivCol();
							$CI->make->sDivCol(1);
								$CI->make->H(4,'0.00',array('id'=>'item-price'));
								$CI->make->hidden('item-price-hid',0);
							$CI->make->eDivCol();
							$CI->make->sDivCol(1);
								$CI->make->input(null,'qty',null,'Add quantity',array('class'=>'rOkay','ro-msg'=>'Please add quantity'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(1);
								$CI->make->button(fa('fa-plus').' Add item to recipe',array('id'=>'add-btn'),'primary');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
    $CI->make->eDivRow();
    $CI->make->sDivRow();
    	$CI->make->sDivCol();
    		$CI->make->sDiv(array('class'=>'table-responsive'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
    				$CI->make->sTablehead();
    					$CI->make->sRow();
	    					$CI->make->th('Item');
	    					$CI->make->th('UOM');
	    					$CI->make->th('Unit Price');
	    					$CI->make->th('Quantity');
	    					$CI->make->th('Line Total');
	    				$CI->make->eRow();
    				$CI->make->eTableHead();
    				$CI->make->sTableBody();
    					$total = 0;
    					foreach ($det as $val) {
    						$CI->make->sRow();
    							$CI->make->td();
    						$CI->make->eRow();
    					}
    				$CI->make->eTableBody();
    			$CI->make->eTable();
    		$CI->make->eDiv();
    	$CI->make->eDivCol();
    $CI->make->eDivRow();


	return $CI->make->code();
}

function makeMenuCategoriesForm($cat=array(),$menu_id=null,$branch = null){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/categories_form_db",array('id'=>'categories_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(10);
				$CI->make->hidden('menu_cat_id',iSetObj($cat,'menu_cat_id'));
				$CI->make->hidden('f_branch_id',$branch);
				$CI->make->input('Name','menu_cat_name',iSetObj($cat,'menu_cat_name'),'Type Category Name',array('class'=>'rOkay'));
				$CI->make->menuSchedulesDrop('Default Schedule','menu_sched_id',iSetObj($cat,'menu_sched_id'),'Select Schedule',array('class'=>''));
				$CI->make->input('Arrangement','cat_arrangement',iSetObj($cat,'arrangement'),'Enter Number only',array('class'=>'rOkay','style'=>'width:150px;'));
				$CI->make->brandDbDrop('Brand','brand',iSetObj($cat,'brand'),'Select Brand',array('class'=>'rOkay'));
					// if($menu_id != null){
					// 	// $CI->make->h(5,'Branch Name:',array('style'=>'font-weight:bold;'));
					// 	// $CI->make->h(5,iSetObj($menu,'branch_name'));
					// 	$CI->make->input('Branch Code:','b_code',iSetObj($menu,'branch_code'),'Branch Code',array('class'=>'rOkay','style'=>'width:200px;','readonly'=>'readonly'));
					// }else{

					// }
					// 	// $CI->make->h(5,'Branch Name:',array('style'=>'font-weight:bold;'));
					// 	// $CI->make->h(5,iSetObj($menu,'branch_name'));
					// 	$CI->make->input('Branch Code:','b_code',iSetObj($cat,'branch_code'),'Branch Code',array('class'=>'rOkay','style'=>'width:200px;','readonly'=>'readonly'));
					// }else{
					if($branch != null){
						$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:6px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("menu_categories","menu_cat_id",iSetObj($cat,'menu_cat_id'))); //table , where id, id needed
					}
					else{
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:6px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'','required'=>''));
					}
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
function makeMenuSubCategoriesForm($cat=array(),$branch=null){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/subcategories_form_db",array('id'=>'subcategories_form'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(12);
				$CI->make->hidden('menu_sub_cat_id',iSetObj($cat,'menu_sub_cat_id'));
				$CI->make->hidden('f_branch_id',$branch);
				$CI->make->input('Name','menu_sub_cat_name',iSetObj($cat,'menu_sub_cat_name'),'Type Sub Category Name',array('class'=>'rOkay'));
				// if($branch != null){
				// 	$CI->make->input('Branch Code:','b_code',iSetObj($cat,'branch_code'),'Branch Code',array('class'=>'rOkay','style'=>'width:200px;','readonly'=>'readonly'));
				// }else{
					if($branch != null){
						$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("menu_categories","menu_cat_id",iSetObj($cat,'menu_sub_cat_id'))); //table , where id, id needed
					}
					else{
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'multiple'));
					}
				// }

				// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
function makeMenuSchedulesForm($cat=array(),$dets=array()){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/menu_sched_db",array('id'=>'schedules_form'));
		$CI->make->sDivRow(array('style'=>''));
			$CI->make->sDivCol(6);
				$CI->make->hidden('menu_sched_id',iSetObj($cat,'menu_sched_id'));
				$CI->make->input('Description','desc',iSetObj($cat,'desc'),'Type Description',array('class'=>'rOkay'));
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
    $CI->make->eForm();
	$CI->make->sForm("dine/menu/menu_sched_details_db",array('id'=>'schedules_details_form'));
    	$CI->make->sDivRow();
            $CI->make->sDivCol(3);
            	// $CI->make->hidden('menu_sched_id',iSetObj($cat,'menu_sched_id'));
            	$CI->make->hidden('sched_id',iSetObj($cat,'menu_sched_id'));
                $CI->make->time('Time On','time_on','07:00 AM','Time On');
            $CI->make->eDivCol();
            $CI->make->sDivCol(3);
                $CI->make->time('Time Off','time_off','10:00 PM','Time Off');
            $CI->make->eDivCol();
            $CI->make->sDivCol(3);
                $CI->make->dayDrop('Day','day',null,'',array('style'=>'width: inherit;'));
            $CI->make->eDivCol();
            $CI->make->sDivCol(3);
                $CI->make->button(fa('fa-plus').' Add Schedule',array('id'=>'add-schedule','style'=>'margin-top:23px;'),'primary');
            $CI->make->eDivCol();
        $CI->make->eDivRow();
        $CI->make->sDivRow();
            $CI->make->sDivCol();
                $CI->make->sDiv(array('class'=>'table-responsive'));
                    $CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
                        $CI->make->sRow();
                            // $CI->make->th('DAY');
                            $CI->make->th('DAY',array('style'=>'width:60px;'));
                            $CI->make->th('TIME ON',array('style'=>'width:60px;'));
                            $CI->make->th('TIME OFF',array('style'=>'width:60px;'));
                            $CI->make->th('&nbsp;',array('style'=>'width:40px;'));
                        $CI->make->eRow();
                        $total = 0;
                        // echo var_dump($dets);
                        if(count($dets) > 0){
                            foreach ($dets as $res) {
                                $CI->make->sRow(array('id'=>'row-'.$res->id));
                                    $CI->make->td(date('l',strtotime($res->day)));
                                    $CI->make->td(date('h:i A',strtotime($res->time_on)));
                                    $CI->make->td(date('h:i A',strtotime($res->time_off)));
                                    $a = $CI->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-sched-'.$res->id,'class'=>'del-sched','ref'=>$res->id,'return'=>true));
                                    $CI->make->td($a);
                                $CI->make->eRow();
                            //     $total += $price * $res->qty;
                            }
                        }
                    $CI->make->eTable();
                $CI->make->eDiv();
            $CI->make->eDivCol();
        $CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}

//for new subcategory
function makeMenuSubCategoriesNewForm($cat=array(),$branch=null){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/subcategories_form_new_db",array('id'=>'subcategories_form_new'));
		$CI->make->sDivRow(array('style'=>'margin:10px;'));
			$CI->make->sDivCol(12);
				$CI->make->hidden('menu_sub_id',iSetObj($cat,'menu_sub_id'));
				$CI->make->hidden('f_branch_id',$branch);
				$CI->make->input('Name','menu_sub_name',iSetObj($cat,'menu_sub_name'),'Type Sub Category Name',array('class'=>'rOkay'));
				$CI->make->menuCategoriesDrop('Under Category','category_id',iSetObj($cat,'category_id'),'',array());
				if($branch != null){
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->selected_bcode('','b_code[]',null,null,array("multiple"=>"","selected"=>""),array("menu_subcategory","menu_sub_id",iSetObj($cat,'menu_sub_id'))); //table , where id, id needed
					// echo "<pre>",print_r($cat),"</pre>";die();
					$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
				}
				else{
					$CI->make->sDivCol(12);
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
							$CI->make->span("Non-affected Branch");
						$CI->make->eDivCol();
						$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
							$CI->make->span("Affected Branch");
						$CI->make->eDivCol();
					$CI->make->eDivCol();
					$CI->make->allbranchesDrop('','b_code[]',null,null,array('multiple'=>'multiple'));
				}
				// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($cat,'inactive'),'',array('style'=>'width: 85px;'));
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}


function makePriceListing($menu=null){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/menu_sched_details_db",array('id'=>'schedules_details_form'));
        $CI->make->sDivRow();
            $CI->make->sDivCol();
                $CI->make->sDiv(array('class'=>'table-responsive'));
                    $CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
                        $CI->make->sRow();
                            // $CI->make->th('DAY');
                            $CI->make->th('Branch',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Cost',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Price',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Tax Exempt',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Free',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Date Effective',array('class'=>'text-center','style'=>'width:60px;'));
                            // $CI->make->th('&nbsp;',array('style'=>'width:40px;'));
                        $CI->make->eRow();
                        $total = 0;
                        // echo "</pre>",print_r($menu),"</pre>";
                        if(count($menu) > 0){
                            foreach ($menu as $res) {
                                $CI->make->sRow(array('id'=>'row-1'));
                                	// echo $res->branch_code;
                                    $CI->make->td($res->branch_code,array('align'=>'center'));
                                    $CI->make->td($res->costing,array('align'=>'center'));
                                    $CI->make->td($res->cost,array('align'=>'center'));
                                    if($res->no_tax == 1){
                                    	$tax = fa('fa-check font-green');
                                    }
                                    else{
                                    	$tax = fa('fa-close font-red');
                                    }
                                    if($res->free == 1){
                                    	$free = fa('fa-check font-green');
                                    }
                                    else{
                                    	$free = fa('fa-close font-red');
                                    }
                                    $CI->make->td($tax,array('align'=>'center'));
                                    $CI->make->td($free,array('align'=>'center'));
                                    if($res->date_effective == NULL){
                                    	$CI->make->td('Initial Setup',array('align'=>'center'));
                                    }else{
                                    	$CI->make->td((empty($res->reg_date)) ? '' : sql2Date($res->reg_date),array('align'=>'center'));
                                    }
                                $CI->make->eRow();
                            }
                        }
                    $CI->make->eTable();
                $CI->make->eDiv();
            $CI->make->eDivCol();
        $CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
function makeHistorypricing($history=null){
	$CI =& get_instance();
	$CI->make->sForm("dine/menu/menu_sched_details_db",array('id'=>'schedules_details_form'));
        $CI->make->sDivRow();
            $CI->make->sDivCol();
                $CI->make->sDiv(array('class'=>'table-responsive'));
                    $CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
                        $CI->make->sRow();
                            // $CI->make->th('DAY');
                            $CI->make->th('Branch',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Cost',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Price',array('class'=>'text-center','style'=>'width:60px;'));
                            $CI->make->th('Date and Time',array('class'=>'text-center','style'=>'width:60px;'));
                            // $CI->make->th('&nbsp;',array('style'=>'width:40px;'));
                        $CI->make->eRow();
                        $total = 0;
                        // echo "</pre>",print_r($menu),"</pre>";
                        if(count($history) > 0){
                            foreach ($history as $res) {
                                $CI->make->sRow(array('id'=>'row-1'));
                                	// echo $res->branch_code;
                                    $CI->make->td($res->branch_code,array('align'=>'center'));
                                    $CI->make->td($res->cost,array('align'=>'center'));
                                    $CI->make->td($res->selling,array('align'=>'center'));
                                    $CI->make->td($res->date_time,array('align'=>'center'));
                                $CI->make->eRow();
                            }
                        }
                    $CI->make->eTable();
                $CI->make->eDiv();
            $CI->make->eDivCol();
        $CI->make->eDivRow();
	$CI->make->eForm();

	return $CI->make->code();
}
//menu prices
function menuPricesLoad($menu_id,$det = null){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-asterisk').' Prices',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(8);
			$CI->make->sForm('menu/menu_prices_db',array('id'=>'menu-price-form'));
				$CI->make->hidden('menu-id-hid',$menu_id);
				$CI->make->sDivRow();
					$CI->make->sDivCol(4);
						// $CI->make->modifiersAjaxDrop('Search Item','item-search',null,array());
						// $CI->make->button(fa('fa-plus').' Add Modifier Group',array('id'=>'add-btn'),'primary btn-block');
						$CI->make->transTypeDrop('Transaction Type','trans_type',null,'Select a Type');
						// $CI->make->hidden('mod-group-id-hid',null,array('class'=>'rOkay','ro-msg'=>'Please select a modifier'));
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->input('Price','price',null,'Price',array());
					$CI->make->eDivCol();
					if(iSetObj($menu_id,'menu-id-hid') != null){
						// $CI->make->inactiveDrop('Inactive','inactive',iSetObj($mod,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedModbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($menu_id,'menu-id-hid'));
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
					$CI->make->sDivCol(4);
		                $CI->make->A(fa('fa-lg fa-plus')." ADD",'#',array('class'=>'btn btn-primary','id'=>'add-price','style'=>'margin-top:23px;'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
	$CI->make->eDivRow();	
	$CI->make->sDivRow();	
		$CI->make->sDivCol(7);
			$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
    					$total = 0;

    					$CI->make->sRow();
							// $CI->make->td(fa('fa-asterisk')." ".$val->mod_group_name);
							// $a = $CI->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'del-'.$val->id,'ref'=>$val->id,'class'=>'del-item','return'=>true));
       //  					$CI->make->td($a,array('style'=>'text-align:right'));
        					$CI->make->td("Transaction Type",array('style'=>'text-align:left'));
        					$CI->make->td("Price",array('style'=>'text-align:right'));
        					$CI->make->td("",array('style'=>'text-align:right'));
						$CI->make->eRow();


    					foreach ($det as $val) {
    						$CI->make->sRow(array('id'=>'row-'.$val->id));
    							$CI->make->td($val->trans_type);
    							$CI->make->td(num($val->price),array('style'=>'text-align:right'));
    							$a = $CI->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'del-'.$val->id,'ref'=>$val->id,'class'=>'del-item','return'=>true));
            					$CI->make->td($a,array('style'=>'text-align:right'));
    						$CI->make->eRow();
    					}
    			$CI->make->eTable();
    		$CI->make->eDiv();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
function menu_inventory_and_location_container($records, $loc_fields){
	$CI =& get_instance();
	// $CI->make->sDivRow();
		// $CI->make->sDivCol(9);
		// $CI->make->eDivCol();
		// $CI->make->sDivCol(3);
		// 	// $CI->make->button(fa('fa-print').' Download Excel',array('id'=>'print-btn','class'=>'btn-block'),'primary');
		// $CI->make->eDivCol();
    // $CI->make->eDivRow();
    // $CI->make->append('<br>');
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('info');
				$CI->make->sBoxBody();
						$th = array(
							'Branch Code'=>'',
							'Item Code'=>'',
							'Item Name'=>'',
							'Category'=>'',
							'Sub Category'=>'',
							'Qty Onhand'=>'',
						);
						$rows = array();

						// if (!empty($loc_fields)) {
						// 	foreach ($loc_fields as $unf => $frm) {
						// 		$th[$frm] = '';
						// 	}
						// 	// $th[''] = array('width'=>'10%','align'=>'right');
						// } else {
						// 	$rows[] = array('No records found','');
						// }


						foreach ($records as $val) {
							$item_array = array($val['branch_code'],$val['menu_code'],$val['menu_name'],$val['cat_name'],$val['sub_cat_name'],$val['qoh']);

							// foreach ($loc_fields as $unfx => $frmx) {
							// 	$item_array[] = (!empty($val[$unfx]) ? num($val[$unfx])." ".$val['uom'] : null);
							// }

							// $item_array[] = '';
							$rows[] = $item_array;
						}
						$CI->make->listLayout($th,$rows,array(),array('style'=>'font-weight:500'));
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
	$CI->make->eDivRow();

	return $CI->make->code();
}
?>