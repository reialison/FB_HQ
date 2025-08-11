<?php
function adjustments_display($list = null){
	$CI =& get_instance();

	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12,'right');
							 $CI->make->A(fa('fa-plus').' Create new adjustment',base_url().'adjustment/form',array('class'=>'btn btn-primary'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                	$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array(
									'Date'=>'',
									'Reference'=>'',
									'Created by' => '',
									'Memo'=>'',
									'Last updated'=>'',
									' '=>array('width'=>'12%','align'=>'right'));
							$rows = array();
							foreach($list as $val){
								$links = "";
								// $links .= $CI->make->A(fa('fa-edit fa-2x fa-fw'),base_url().'menu/form/'.$v->menu_id,array("return"=>true));
								$rows[] = array(
											sql2Date($val->reg_date)
											, $val->trans_ref
											, $val->username
											, $val->memo
											, $val->update_date
											, ''
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
function adjustment_form($ref = null){
	$CI =& get_instance();
	$now = $CI->site_model->get_db_now();
	$CI->make->sDivRow();
		$CI->make->sDivCol(4);
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sForm("adjustment/add_item",array('id'=>'add_item_form'));
						// $CI->make->input('Item','item-search',null,'Search Item',array('search-url'=>'mods/search_items'),'',fa('fa-search'));
						$CI->make->itemAjaxDrop('Item','item-search',null,array());
						$CI->make->hidden('item-id',null);
						$CI->make->hidden('item-uom',null);
						$CI->make->hidden('item-ppack',null);
						$CI->make->hidden('item-ppack-uom',null);
						$CI->make->hidden('item-pcase',null);
						$CI->make->sDivRow();
							$CI->make->sDivCol(6);
								$CI->make->input('Quantity','qty',null,null,array());
							$CI->make->eDivCol();
							$CI->make->sDivCol(6);
								$CI->make->select('&nbsp;','select-uom',array(),null,array());
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->locationsDrop('Adjustment from','from_loc','',null,array('shownames'=>true));
						$CI->make->locationsDrop('Transfer to','to_loc','','- NONE -',array('shownames'=>true));
						$CI->make->button(fa('fa-plus').' Add Item',array('class'=>'','id'=>'add-item-btn'),'primary');
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
		#FORM
		$CI->make->sDivCol(8);
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sForm("adjustment/adjustment_db",array('id'=>'adjustment_form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->input('Reference','reference',$ref,'Adjustment Reference',array('class'=>'rOkay'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);
								$CI->make->date('Date','trans_date',sql2Date($now),null,array('class'=>'rOkay','ro-msg'=>'Date must not be empty'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(	);
								$CI->make->button(
									fa('fa-save').' Save'
									, array('id'=>'save-trans','disabled'=>'disabled','style'=>'margin:23px 0px 10px;margin-right:10px;')
									, 'success');
								$CI->make->button(fa('fa-reply').' Back',array('id'=>'back-btn','style'=>'margin-top:12px'),'primary');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eForm();
					$CI->make->sDivRow();
						$CI->make->sDivCol();
							$CI->make->sDiv(array('class'=>'table-responsive'));
							$CI->make->sTable(array('class'=>'table table-hover','id'=>'details-tbl','style'=>'border-bottom:2px solid #ddd;border-top:2px solid #ddd;margin-bottom:5px;'));
								$CI->make->sRow();
									$CI->make->th('Item');
									$CI->make->th('Quantity');
									// $CI->make->th('QTY',array('style'=>'width:60px;'));
									$CI->make->th('UOM',array('style'=>'width:60px;'));
									// $CI->make->th('Cost',array('style'=>'width:60px;'));
									$CI->make->th('Adjustment from');
									$CI->make->th('Transfer to');
									$CI->make->th('&nbsp;',array('style'=>'width:40px;'));
								$CI->make->eRow();
							$CI->make->eTable();
							$CI->make->eDiv();
						$CI->make->eDivCol();
					$CI->make->eDivRow();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12);
							$CI->make->textarea('','memo',null,'Add Remarks Here...',array());
						$CI->make->eDivCol();
					$CI->make->eDivRow();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();

	return $CI->make->code();
}
function adjustmentSearch($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Reference','trans_ref',null,null);
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}