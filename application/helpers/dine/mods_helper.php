<?php
function modGroupSearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input('Modifier Name','name',null,null);
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
function modSearchForm($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4);
				$CI->make->input('Modifier Name','name',null,null);
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
function modListPage($list=array()){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12,'right');
							 $CI->make->A(fa('fa-plus').' Add New Modifier',base_url().'mods/form',array('class'=>'btn btn-primary'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                	$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array(
									'Name'=>'',
									'Cost'=>'',
									' '=>array('width'=>'12%','align'=>'right'));
							$rows = array();
							foreach($list as $v){
								$links = "";
								$links .= $CI->make->A(fa('fa-edit fa-2x fa-fw'),base_url().'mods/form/'.$v->mod_id,array("return"=>true));
								$rows[] = array(
											  $v->name,
											  num($v->cost),
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
function modFormPage($mod_id=null,$bcode=null){
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
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'mods',array('class'=>'btn btn-success-600 radius-8 px-16 py-9','return'=>true)),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link ','load'=>'mods/details_load/','id'=>'details_link'),
						fa('fa-book')." Recipe"=>array('href'=>'#recipe','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'mods/recipe_load/','id'=>'recipe_link'),
						fa('fa-book')." Prices"=>array('href'=>'#price','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'mods/price_load/','id'=>'price_link'),
						fa('fa-book')." Sub Modifier"=>array('href'=>'#mod_sub','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'mods/mod_sub_load/','id'=>'mod_sub_link'),
					);
					$CI->make->hidden('mod_id',$mod_id);
					$CI->make->hidden('bcode',$bcode);
					$CI->make->tabHead($tabs,null,array('id'=>'mainTabs'));
					$CI->make->sTabBody();
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'recipe','class'=>'tab-pane'));
						$CI->make->eTabPane();$CI->make->sTabPane(array('id'=>'price','class'=>'tab-pane'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'mod_sub','class'=>'tab-pane'));
						$CI->make->eTabPane();
					$CI->make->eTabBody();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function modDetailsLoad($mod=null,$mod_id=null){
	$CI =& get_instance();
		$CI->make->sForm("mods/details_db",array('id'=>'details_form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(6);

					$readonly = $mod_id > 0 ? 'readonly' : '';

					$CI->make->hidden('form_mod_id',$mod_id);
					$CI->make->input('Code','mod_code',iSetObj($mod,'mod_code'),'Type Code',array('class'=>'rOkay',$readonly=>''));
					$CI->make->input('Name','name',iSetObj($mod,'name'),'Type Name',array('class'=>'rOkay',$readonly=>''));
					$CI->make->input('Price','cost',iSetObj($mod,'cost'),'Price',array('class'=>'rOkay'));
					if(HAS_MOD_SUB_CAT_ID){

						$CI->make->menuSubCategoriesDrop('Subcategory','mod_sub_cat_id',iSetObj($mod,'mod_sub_cat_id'),'Select Menu Type',array('class'=>'rOkay'));
					}
					$CI->make->ModifiersColorDrop('Color Group','color',iSetObj($mod,'color'),'Select Color',array('class'=>''));
					$CI->make->inactiveDrop('Has Recipe','has_recipe',iSetObj($mod,'has_recipe'));
					
					if(MENU_OTHER_DESC){
						$CI->make->input('Other Description','other_desc',iSetObj($mod,'other_desc'),'Other Desc',array('class'=>''));
					}
					if(iSetObj($mod,'mod_id') != null){
						$CI->make->inactiveDrop('Inactive','inactive',iSetObj($mod,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedModbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($mod,'mod_id'));
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
				$CI->make->eDivCol();
				// $CI->make->sDivCol(4);
				// $CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',4);
				$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-mod','class'=>'btn btn-success-600 radius-8 px-16 py-9'));
			$CI->make->eDivCol();
	    $CI->make->eDivRow();
	return $CI->make->code();
}
function modRecipeLoad($mod_id=null,$det=null,$mod=null,$branch_code=null){
	$CI =& get_instance();
		$CI->make->sForm("mods/recipe_db",array('id'=>'recipe_form'));
			$CI->make->hidden('mod_id',$mod_id);
			$CI->make->sDivRow();
				$CI->make->sDivCol(3);
	    			$CI->make->hidden('branch_code',$branch_code);
					$CI->make->input(null,'item-search',null,'Search Item',array('search-url'=>'mods/search_items'),'',fa('fa-search'));
					$CI->make->input('Item Price','item-cost',null,null,array('readonly'=>'readonly'));
					$uomTxt = $CI->make->span('&nbsp;&nbsp;',array('return'=>true,'id'=>'uom-txt'));
					$CI->make->input('Quantity','qty',null,null,array(),'',$uomTxt);
					$CI->make->hidden('item-id-hid',null,array('class'=>'rOkay','ro-msg'=>'Please Select an Item'));
					$CI->make->hidden('item-uom-hid',null);
					$CI->make->button(fa('fa-plus').' Add Item',array('class'=>'btn-block','id'=>'add-item-btn'),'primary');
				$CI->make->eDivCol();
				$CI->make->sDivCol(9);
					$CI->make->sDivRow();
						$CI->make->sDivCol();
							$CI->make->sDiv(array('class'=>'table-responsive'));
								$CI->make->sTable(array('class'=>'table table-striped','id'=>'details-tbl'));
									$CI->make->sRow();
										$CI->make->th('ITEM');
										$CI->make->th('QTY',array('style'=>'width:60px;'));
										$CI->make->th('PRICE',array('style'=>'width:60px;'));
										$CI->make->th('SUBTOTAL',array('style'=>'width:60px;'));
										$CI->make->th('&nbsp;',array('style'=>'width:40px;'));
									$CI->make->eRow();
									$total = 0;
									// echo "<pre>",print_r($det),"</pre>";die();
									if(count($det) > 0){
										foreach ($det as $res) {
											$CI->make->sRow(array('id'=>'row-'.$res->mod_recipe_id));
									            $CI->make->td($res->code." ".$res->name);
									            $CI->make->td(num($res->qty));
										        $CI->make->td(num($res->cost));
									            $CI->make->td(num($res->cost * $res->qty));
									            $a = $CI->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$res->mod_recipe_id,'class'=>'dels','ref'=>$res->mod_recipe_id,'return'=>true));
									            $CI->make->td($a);
									        $CI->make->eRow();
											$total += $res->cost * $res->qty;
										}
									}
								$CI->make->eTable();
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDivRow();
						$CI->make->sDivCol(4,'left','8');
								$pop = $CI->make->A(fa('fa-save fa-fw '),'#',array('id'=>'override-price','return'=>true));
								$sell_price = iSetObj($mod,'cost');
								$sell_price += $total;
								$CI->make->input('Selling Price','total',num($sell_price),null,array(),null,$pop);
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eDivCol();				
			$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}
function modGroupListPage($list=array()){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12,'right');
							 $CI->make->A(fa('fa-plus').' Add New Modifier Group',base_url().'mods/group_form',array('class'=>'btn btn-primary'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                	$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array(
									'Name'=>'',
									'Cardinality'=>'',
									'Selection'=>'',
									' '=>array('width'=>'12%','align'=>'right'));
							$rows = array();
							foreach($list as $v){
								$links = "";
								$links .= $CI->make->A(fa('fa-edit fa-2x fa-fw'),base_url().'mods/group_form/'.$v->mod_group_id,array("return"=>true));
								$type = "Mandatory";
								if($v->mandatory == 0)
									$type = "Optional";
								$sel = "Single";
								if($v->multiple == 0)
									$sel = "Multiple";
								$rows[] = array(
											  $v->name,
											  $type,
											  $sel,
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
function modGroupFormPage($mod_group_id=null,$bcode=null){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sTab();
					$tabs = array(
						"tab-title"=>$CI->make->a(fa('fa-reply')." Back To List",base_url().'mods/groups',array('return'=>true,'class'=>'btn btn-success-600 radius-8 px-16 py-9')),
						fa('fa-info-circle')." General Details"=>array('href'=>'#details','class'=>'tab_link','load'=>'mods/group_details_load/','id'=>'details_link'),
						fa('fa-book')." Modifiers"=>array('href'=>'#modifiers','disabled'=>'disabled','class'=>'tab_link load-tab','load'=>'mods/group_modifiers_load/','id'=>'recipe_link'),
					);
					$CI->make->hidden('mod_group_id',$mod_group_id);
					$CI->make->hidden('bcode',$bcode);
					$CI->make->tabHead($tabs,null,array());
					$CI->make->sTabBody();
						$CI->make->sTabPane(array('id'=>'details','class'=>'tab-pane active'));
						$CI->make->eTabPane();
						$CI->make->sTabPane(array('id'=>'modifiers','class'=>'tab-pane'));
						$CI->make->eTabPane();
					$CI->make->eTabBody();
				$CI->make->eTab();
		$CI->make->eDivCol();
	$CI->make->eDivRow();
	return $CI->make->code();
}
function modGroupDetailsLoad($grp=null,$mod_group_id=null){
	$CI =& get_instance();
		$CI->make->sForm("mods/group_details_db",array('id'=>'details_form'));
			$CI->make->sDivRow();
				$CI->make->sDivCol(6);
					$CI->make->hidden('form_mod_group_id',$mod_group_id);
					$CI->make->input('Code','grp_code',iSetObj($grp,'grp_code'),'Type Code',array('class'=>'rOkay'));
					$CI->make->input('Name','name',iSetObj($grp,'name'),'Type Name',array('class'=>'rOkay'));
					$CI->make->inactiveDrop('Mandatory','mandatory',iSetObj($grp,'mandatory'));
					$CI->make->number('Select Limit','multiple',iSetObj($grp,'multiple'));
					$CI->make->number('Select Minimum','min_no',iSetObj($grp,'min_no'));
					if(iSetObj($grp,'mod_group_id') != null){
						$CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($grp,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedModGrpbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($grp,'mod_group_id'));
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
				$CI->make->eDivCol();
	    	$CI->make->eDivRow();
		$CI->make->eForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(4,'left',4);
				$CI->make->button(fa('fa-save').' Save Details',array('id'=>'save-grp','class'=>'btn btn-success-600 radius-8 px-16 py-9'));
			$CI->make->eDivCol();
	    $CI->make->eDivRow();
	return $CI->make->code();
}
function groupModifiersLoad($mod_group_id=null,$det=null){
	$CI =& get_instance();
		$CI->make->sForm("mods/recipe_db",array('id'=>'recipe_form'));
			$CI->make->hidden('mod_group_id',$mod_group_id);
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					// $CI->make->input(null,'item-search',null,'Search Modifiers',array('search-url'=>'mods/search_modifiers'),'',fa('fa-search'));
					$CI->make->modifiersAjaxDrop('Search Modifier','item-search');
				$CI->make->eDivCol();				
				$CI->make->sDivCol(6);
					$CI->make->allbranchesDrop('Branch Code','b_code[]',null,null,array("multiple"=>""));
				$CI->make->eDivCol();				
				$CI->make->sDivCol(2);
	                $CI->make->A(fa('fa-lg fa-plus')." Add",'#',array('class'=>'btn btn-primary blockui_sample_2_1','id'=>'add-modifier','style'=>'margin-top:23px;'));
				$CI->make->eDivCol();				
			$CI->make->eDivRow();
			$CI->make->H(5,'Note: Check the modifier to auto add it.');
			$CI->make->sDivRow();
				$CI->make->sDivCol(4);
					$CI->make->sUl(array('class'=>'vertical-list','id'=>'modifier-list'));
							// echo "<pre>",print_r($det),"</pre>";die();
						foreach ($det as $res) {
							$chck = false;
							if($res->default == 1){
								$chck = true;
							}
							$li = $CI->make->li(
				                $CI->make->checkbox(null,'dflt_'.$res->id,0,array('class'=>'dflt','ref'=>$res->id,'return'=>true),$chck)." ".
				                $CI->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
				                $CI->make->span($res->mod_name,array('class'=>'text','return'=>true))." ".
				                $CI->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$res->id,'ref'=>$res->id,'ref2'=>$res->branch_code,'ref3'=>$res->mod_id,'hidden'=>'hidden')),
				                array('id'=>'li-'.$res->id)
				             );
						}
					$CI->make->eUl();
				$CI->make->eDivCol();				
			$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}
function modSubLoad($mod_id=null,$det=null){ 
	$CI =& get_instance();
		$CI->make->sForm("mods/mod_sub_db",array('id'=>'mod_sub_form'));
			$CI->make->hidden('mod_id',$mod_id);
			$CI->make->sDivRow();
				$CI->make->sDivCol(5);
					$CI->make->input('Submodifer Code','submod_code',null,null,array('required'=>'required'));
					$CI->make->input('Name','name',null,null,array('required'=>'required'));
					$CI->make->input('Price','cost',null,null,array('required'=>'required'));
					$CI->make->input('Group','group',null,null,array('required'=>'required'));
					$CI->make->input('Qty','qty',null,null,array('required'=>'required'));
					$CI->make->inactiveDrop('Auto Add','is_auto',null,null,array('style'=>'width: 85px;'));
					if(iSetObj($mod_id,'mod_id') != null){
						// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($grp,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>''));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedModGrpbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($mod_id,'mod_id'));
					}else{
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>''));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->allbranchesDrop('','b_code[]',null,null,array("multiple"=>"",'class'=>'rOkay'));
					}
					$uomTxt = $CI->make->span('&nbsp;&nbsp;',array('return'=>true,'id'=>'uom-txt'));
					// $CI->make->input('Quantity','qty',null,null,array(),'',$uomTxt);
					$CI->make->button(fa('fa-plus').' Add Item',array('class'=>'btn-block','id'=>'add-modsub-btn'),'primary');
				$CI->make->eDivCol();
				$CI->make->sDivCol(7);
					$CI->make->sDivRow();
						$CI->make->sDivCol();
							$CI->make->sDiv(array('class'=>'table-responsive'));
								$CI->make->sTable(array('class'=>'table table-striped','id'=>'modsub-details-tbl'));
									$CI->make->sRow();
										$CI->make->th('SUBMODIFIER CODE');
										$CI->make->th('NAME');
										$CI->make->th('GROUP');
										$CI->make->th('QTY');
										$CI->make->th('AUTO');
										$CI->make->th('PRICE',array('style'=>'width:60px;'));
									$CI->make->eRow();
									$total = 0;
									if(count($det) > 0){
										foreach ($det as $res) {
											$auto = 'No';
											if($res->is_auto == 1){
												$auto = 'Yes';
											}
											
											$CI->make->sRow(array('id'=>'row-'.$res->mod_sub_id,'class'=>'modsub-row','ref'=>$res->mod_sub_id,'modsub-name'=>$res->name));
												$CI->make->td($res->submod_code);
									            $CI->make->td($res->name);
									            $CI->make->td($res->group);
									            $CI->make->td($res->qty);
									            $CI->make->td($auto);
									            // $CI->make->td(num($res->qty));
										        $CI->make->td(num($res->cost),array('style'=>'text-align:right'));
									            $a = $CI->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$res->mod_sub_id,'class'=>'dels','ref'=>$res->mod_sub_id,'return'=>true));
									            $CI->make->td($a);
									        $CI->make->eRow();
											// $total += $res->cost * $res->qty;
										}
									}
								$CI->make->eTable();
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					
				$CI->make->eDivCol();

				$CI->make->sDivCol(12);
					$CI->make->sDivCol(3);
					$CI->make->eDivCol();

					$CI->make->sDivCol(9);
						$CI->make->sDiv(array('id'=>'modsub-price'));
						$CI->make->eDiv();
					$CI->make->eDivCol();
				$CI->make->eDivCol();

			$CI->make->eDivRow();
		$CI->make->eForm();
	return $CI->make->code();
}

function modPricesLoad($mod_id,$det = null){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-asterisk').' Prices',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(8);
			$CI->make->sForm('mods/mod_prices_db',array('id'=>'mod-price-form'));
				$CI->make->hidden('mod-id-hid',$mod_id);
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
					$CI->make->sDivCol(4);
		                $CI->make->A(fa('fa-lg fa-plus')." ADD",'#',array('class'=>'btn btn-primary','id'=>'add-price','style'=>'margin-top:23px;'));
					$CI->make->eDivCol();
					// $CI->make->inactiveDrop('Has Recipe','has_recipe',iSetObj($mod,'has_recipe'));
					if(iSetObj($det,'mod_id') != null){
						// $CI->make->inactiveDrop('Inactive','inactive',iSetObj($mod,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-27px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedModbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($det,'mod_id'));
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
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
	$CI->make->eDivRow();	
	$CI->make->sDivRow();	
		$CI->make->sDivCol(7);
			$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'modprice-details-tbl'));
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

function modsubPricesLoad($mod_sub_id,$mod_sub_name,$det = null){
	$CI =& get_instance();
	$CI->make->H(3,fa('fa fa-asterisk').' '.$mod_sub_name.' Prices',array('class'=>'page-header'));
	$CI->make->sDivRow();
		$CI->make->sDivCol(8);
			$CI->make->sForm('mods/mod_sub_prices_db',array('id'=>'modsub-price-form'));
				$CI->make->hidden('modsub-id-hid',$mod_sub_id);
				$CI->make->sDivRow();
					$CI->make->sDivCol(4);
						$CI->make->transTypeDrop('Transaction Type','trans_type',null,'Select a Type');
					$CI->make->eDivCol();
					$CI->make->sDivCol(4);
						$CI->make->input('Price','price',null,'Price',array());
					$CI->make->eDivCol();
					if(iSetObj($mod_sub_id,'mod_sub_id') != null){
						// $CI->make->inactiveDrop('Is Inactive','inactive',iSetObj($grp,'inactive'));
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->selectedModGrpbranchDrop('','bcode[]',null,null,array('class'=>'rOkay',"multiple"=>"","selected"=>""),iSetObj($mod_sub_id,'mod_sub_id'));
					}else{
						$CI->make->sDivCol(12);
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-26px;'));
								$CI->make->span("Non-affected Branch");
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,"",0,array('style'=>'margin-left:-30px;'));
								$CI->make->span("Affected Branch");
							$CI->make->eDivCol();
						$CI->make->eDivCol();
						$CI->make->allbranchesDrop('','b_code[]',null,null,array("multiple"=>"",'class'=>'rOkay'));
					}
					$CI->make->sDivCol(4);
		                $CI->make->A(fa('fa-lg fa-plus')." ADD",'#',array('class'=>'btn btn-primary','id'=>'add-modsub-price','style'=>'margin-top:23px;'));
					$CI->make->eDivCol();
				$CI->make->eDivRow();
			$CI->make->eForm();
		$CI->make->eDivCol();
	$CI->make->eDivRow();	

	$CI->make->sDivRow();	
		$CI->make->sDivCol(7);
			$CI->make->sDiv(array('class'=>'table-responsive','style'=>'margin-top:23px'));
    			$CI->make->sTable(array('class'=>'table table-striped','id'=>'modsub-price-details-tbl'));
    					$total = 0;

    					$CI->make->sRow();
        					$CI->make->td("Transaction Type",array('style'=>'text-align:left'));
        					$CI->make->td("Price",array('style'=>'text-align:right'));
        					$CI->make->td("",array('style'=>'text-align:right'));
						$CI->make->eRow();


    					foreach ($det as $val) {
    						$CI->make->sRow(array('id'=>'modsub-price-row-'.$val->id));
    							$CI->make->td($val->trans_type);
    							$CI->make->td(num($val->price),array('style'=>'text-align:right'));
    							$a = $CI->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'modsub-price-del-'.$val->id,'ref'=>$val->id,'class'=>'del-modsub-price','return'=>true));
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