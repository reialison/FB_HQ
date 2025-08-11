<?php
function receivingListPage($list=array()){
	$CI =& get_instance();
	$CI->make->sDivRow();
		$CI->make->sDivCol();
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sDivRow();
						$CI->make->sDivCol(12,'right');
							 $CI->make->A(fa('fa-plus').' Create Receive',base_url().'receiving/form',array('class'=>'btn btn-success'));
						$CI->make->eDivCol();
                	$CI->make->eDivRow();
                	$CI->make->sDivRow();
						$CI->make->sDivCol();
							$th = array(
									'Date'=>'',
									'Reference'=>'',
									'Supplier'=>'',
									'Supplier Reference'=>'',
									'Received By'=>'',
									' '=>array('width'=>'12%','align'=>'right'));
							$rows = array();
							foreach($list as $res){
								$links = "";
								// $links .= $CI->make->A(fa('fa-edit fa-2x fa-fw'),base_url().'menu/form/'.$v->menu_id,array("return"=>true));
								$rows[] = array(
											  sql2Date($res->reg_date),
											  $res->trans_ref,
											  $res->supplier_name,
											  $res->reference,
											  $res->username,
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
function receivingFormPage($ref=null){
	$CI =& get_instance();
	$now = $CI->site_model->get_db_now();
	$CI->make->sDivRow();
		$CI->make->sDivCol(3);
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sForm("receiving/add_item",array('id'=>'add_item_form'));
						// $CI->make->input('Item','item-search',null,'Search Item',array('search-url'=>'mods/search_items','class'=>'rOkay'),'',fa('fa-search'));
						$CI->make->itemAjaxDrop('Item','item-search',null,array());						
						$CI->make->hidden('item-id',null);
						$CI->make->hidden('item-uom',null);
						$CI->make->hidden('item-ppack',null);
						$CI->make->hidden('item-ppack-uom',null);
						$CI->make->hidden('item-pcase',null);
						$CI->make->sDivRow();
							$CI->make->sDivCol(6);
								$CI->make->decimal('Quantity','qty',null,null,2,array('class'=>'rOkay'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(6);
								$CI->make->select('&nbsp;','select-uom',array(),null,array('class'=>'rOkay'));
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->decimal('Total Cost','cost',null,null,2,array());
						$CI->make->locationsDrop('Receiving Location','loc_id',null,null,array('shownames'=>true));
						$CI->make->button(fa('fa-plus').' Add Item',array('class'=>'','id'=>'add-item-btn'),'primary');
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();
		#FORM
		$CI->make->sDivCol(9);
			$CI->make->sBox('primary');
				$CI->make->sBoxBody();
					$CI->make->sForm("receiving/save",array('id'=>'receive_form'));
						$CI->make->sDivRow();
							$CI->make->sDivCol(3);
								$CI->make->input('Reference','reference',$ref,'Supplier reference',array('class'=>'rOkay'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);
								$CI->make->suppliersDrop('Supplier','suppliers',null,null,array());
							$CI->make->eDivCol();
							$CI->make->sDivCol(3);
								$CI->make->date('Date','trans_date',sql2Date($now),null,array('class'=>'rOkay','ro-msg'=>'Date must not be empty'));
							$CI->make->eDivCol();
							$CI->make->sDivCol(6,'left');
								$CI->make->button(fa('fa-download').' Receive',array('id'=>'save-btn','style'=>'margin-right:10px;'),'success');
								$CI->make->button(fa('fa-reply').' Back',array('id'=>'back-btn','style'=>''),'primary');
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol();
								$CI->make->sDiv(array('class'=>'table-responsive'));
								$CI->make->sTable(array('class'=>'table table-hover','id'=>'details-tbl','style'=>'border-bottom:2px solid #ddd;border-top:2px solid #ddd;margin-bottom:5px;'));
									$CI->make->sRow();
										$CI->make->th('ITEM');
										$CI->make->th('QTY',array('style'=>'width:60px;'));
										$CI->make->th('UOM',array('style'=>'width:180px;'));
										$CI->make->th('Cost',array('style'=>'width:60px;'));
										$CI->make->th('&nbsp;',array('style'=>'width:40px;'));
									$CI->make->eRow();
									$CI->make->sRow(array('class'=>'no-items-row'));
										$CI->make->th('No Items',array('colspan'=>'5','style'=>'text-align:center'));
									$CI->make->eRow();
								$CI->make->eTable();
								$CI->make->eDiv();
							$CI->make->eDivCol();
						$CI->make->eDivRow();
						$CI->make->sDivRow();
							$CI->make->sDivCol(8);
								$CI->make->textarea('','memo',null,'Add Remarks Here...',array());
							$CI->make->eDivCol();
							$CI->make->sDivCol(4);
								$CI->make->sDivRow();
									$CI->make->sDivCol(6);
										$CI->make->H(3,'Total');
									$CI->make->eDivCol();
									$CI->make->sDivCol(6);
										$CI->make->H(3,'0.00',array('id'=>'grand-total','style'=>'text-align:right;margin-right:10px;'));
									$CI->make->eDivCol();
								$CI->make->eDivRow();
							$CI->make->eDivCol();
						$CI->make->eDivRow();
					$CI->make->eForm();
				$CI->make->eBoxBody();
			$CI->make->eBox();
		$CI->make->eDivCol();


	$CI->make->eDivRow();

	return $CI->make->code();
}
function receivingSearch($post=array()){
	$CI =& get_instance();
	$CI->make->sForm();
		$CI->make->sDivRow();
			$CI->make->sDivCol(6);
				$CI->make->input('Reference','trans_ref',null,null);
				$CI->make->suppliersDrop('Supplier','supplier_id',null,null,array());
			$CI->make->eDivCol();
			$CI->make->sDivCol(6);
				$CI->make->inactiveDrop('Voided','void',null,null,array());
			$CI->make->eDivCol();
    	$CI->make->eDivRow();
	$CI->make->eForm();
	return $CI->make->code();
}
?>