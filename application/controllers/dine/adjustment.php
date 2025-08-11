<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Adjustment extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/adjustment_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/adjustment_helper');
	}
	public function index(){
	    $this->load->helper('site/site_forms_helper');
	    $data = $this->syter->spawn('trans');
	    $data['page_title'] = fa('icon-shuffle')." Adjustment";
	    $th = array('Reference','Created By','Adjustment Date','');
	    $data['code'] = create_rtable('trans_adjustments','adjustment_id','main-tbl',$th,'adjustment/search',false,'list');
	    $data['load_js'] = 'dine/adjustment.php';
	    $data['use_js'] = 'adjustListJs';
	    $data['page_no_padding'] = true;
	    $this->load->view('page',$data);
	}
	public function get_adjustment($id=null,$asJson=true){
	    $this->load->helper('site/pagination_helper');
	    $pagi = null;
	    $args = array();
	    $total_rows = 30;
	    if($this->input->post('pagi'))
	        $pagi = $this->input->post('pagi');
	    $post = array();      

	    $url    =  'adjustment/get_adjustment';
	    $table  =  'trans_adjustments';
	    $select =  'trans_adjustments.*,users.username';
	    $join['users'] = 'trans_adjustments.user_id=users.id';
	    
	    if(count($this->input->post()) > 0){
	        $post = $this->input->post();
	    }
	    if(isset($post['trans_ref'])){
	        $lk = $post['trans_ref'];
	        $args["(".$table.".trans_ref like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
	    }
	    $count = $this->site_model->get_tbl($table,$args,array(),$join,true,$select,null,null,true);
	    $page = paginate($url,$count,$total_rows,$pagi);
	    $items = $this->site_model->get_tbl($table,$args,array(),$join,true,$select,null,$page['limit']);
	    $json = array();
	    if(count($items) > 0){
	        $ids = array();
	        foreach ($items as $res) {
	        	$void = "";
	        	if($res->inactive == 0){
	        	    $inactive = "No";
	        	    $void = $this->make->A(fa('fa fa-times fa-lg').'Delete','#',array('title'=>'Void Trans '.$res->trans_ref,
	        	                                                       'ref'=>$res->adjustment_id,
	        	                                                       'class'=>'btn red btn-sm btn-outline void',
	        	                                                       'return'=>true));
	        	}
	        	else
	        	    $inactive = "Yes";
	            $json[] = array(
	                "id"=>$res->trans_ref,   
	                "desc"=>ucwords(strtolower($res->username)),   
	                "date"=>sql2Date($res->reg_date),   
	                "void"=>$void, 
	                "inactive"=>$inactive
	            );
	        }
	    }
	    echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
	}
	public function search(){
	    $data['code'] = adjustmentSearch();
	    $this->load->view('load',$data);
	}
	public function form(){
		$this->load->model('core/trans_model');
        $data = $this->syter->spawn('trans');
        $data['page_title'] = fa('icon-shuffle')." Adjustment";

        $this->session->unset_userdata('adj_cart');
        $ref = $this->trans_model->get_next_ref(ADJUSTMENT_TRANS);
        $data['code'] = adjustment_form($ref);
        $data['add_css'] = 'js/plugins/typeaheadmap/typeaheadmap.css';
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/adjustment.php';
        $data['use_js'] = 'adjustmentJs';
        $this->load->view('page',$data);
	}
	public function get_item_details($item_id=null,$asJson=true){
		$this->load->model('dine/items_model');
		$json = array();
        $items = $this->items_model->get_item($item_id);
        $item = $items[0];

        $json['item_id'] = $item->item_id;
        $json['uom'] = $item->uom;

        $opts = array();
        $opts[$item->uom] = $item->uom;
        if($item->no_per_pack > 0)
            $opts[$item->no_per_pack_uom.'(@'.$item->no_per_pack.' '.$item->uom.')'] = $item->uom."-".'pack-'.$item->no_per_pack;
        if($item->no_per_case > 0)
            $opts['Case(@'.$item->no_per_case.' Packs)'] = $item->uom."-".'case-'.$item->no_per_case;

        $json['opts'] =  $opts;
        $json['ppack'] = $item->no_per_pack;
        $json['ppack_uom'] = $item->no_per_pack_uom;
        $json['pcase'] = $item->no_per_case;
        echo json_encode($json);
	}
	public function adjustment_db(){
		$this->load->model('dine/items_model');
		$this->load->model('core/trans_model');

		$cart = $this->session->userdata('adj_cart');
		$user = $this->session->userdata('user');

		$ref = $this->input->post('reference');

		if (empty($cart)) {
            echo json_encode(array('msg'=>"Please select an item first before proceeding",'error'=>1));
            return false;
        }
        $now = $this->site_model->get_db_now();
        // $datetime = date('Y-m-d H:i:s');
        $datetime = date2SqlDateTime($now);
		$this->trans_model->db->trans_start();
			$count = $this->site_model->get_tbl('trans_adjustments',array('trans_ref'=>$ref),array(),array(),true,'*',null,null,true);
			if($count){
			    echo json_encode(array('msg'=>"Reference ".$ref." is already used.",'error'=>1));
			    return false;
			}
			$items = array(
				'type_id' => ADJUSTMENT_TRANS,
				'memo'=> $this->input->post('memo'),
				'trans_ref' => $ref,
				'user_id' => $user['id'],
			);
			$id = $this->adjustment_model->add_adjustment($items);

			$prepared = $prepared_moves = array();
			foreach ($cart as $val) {
				$val['item-id'] = abs($val['item-id']);

				$prepare = array(
					'adjustment_id' => $id,
					'item_id' => (int)$val['item-id'],
					'case' => 0,
					'pack' => 0
				);
				$prepare_moves = array(
                    'type_id' => ADJUSTMENT_TRANS,
                    'trans_id' => $id,
                    'trans_ref' => $ref,
                    'item_id' => $val['item-id'],
                    'uom' => $val['item-uom'],
                    'pack_qty' => null,
                    'case_qty' => null,
                    'reg_date' => $datetime
                );

				if (strpos($val['select-uom'],'pack') !== false) {
					$converted_qty = $val['qty'] * $val['item-ppack'];
					$prepare['qty'] = (double) $converted_qty;
					$prepare['pack'] = (double) $val['qty'];
					$prepare_moves['qty'] = $converted_qty;
                    $prepare_moves['pack_qty'] = (double) $val['qty'];
				} elseif (strpos($val['select-uom'],'case') !== false) {
					$converted_qty = $val['qty'] * $val['item-ppack'] * $val['item-pcase'];
					$prepare['qty'] = (double) $converted_qty;
					$prepare['case'] = (double) $val['qty'];
					$prepare_moves['qty'] = $converted_qty;
                    $prepare_moves['case_qty'] = (double) $val['qty'];
				} else {
					$prepare['qty'] = (double)$val['qty'];
					$prepare_moves['qty'] = (double)$val['qty'];
				}

				$fr = explode('-', $val['from_loc']);
				$prepare['from_loc'] = (int)$fr[0];
				$prepare['to_loc'] = "";
				if($val['to_loc'] != ""){
					$to = explode('-', $val['to_loc']);
					$prepare['to_loc'] = (int)$to[0];			
				}
				if ($prepare['from_loc'] != $prepare['to_loc']) {
					# From Location
	                $prepare_moves['loc_id'] = $prepare['from_loc'];

					$last_stock = 0;
	                $stocks = $this->items_model->get_latest_item_move(array('loc_id'=>$prepare['from_loc'],'item_id'=>$val['item-id']));
	                if (!empty($stocks->curr_item_qty))
	                    $last_stock = $stocks->curr_item_qty;
	                
	                if($prepare['to_loc'] != "")
	                	$prepare_moves['curr_item_qty'] = $last_stock - $prepare_moves['qty'];
	                else
	                	$prepare_moves['curr_item_qty'] = $last_stock + $prepare_moves['qty'];

	                $prepared_moves[] = $prepare_moves;

	                if($prepare['to_loc'] != ""){
		                # To Location
						$prepare_moves['loc_id'] = $prepare['to_loc'];

						$last_stock = 0;
		                $stocks = $this->items_model->get_latest_item_move(array('loc_id'=>$prepare['to_loc'],'item_id'=>$val['item-id']));
		                if (!empty($stocks->curr_item_qty))
		                    $last_stock = $stocks->curr_item_qty;
		                $prepare_moves['curr_item_qty'] = $last_stock + $prepare_moves['qty'];
		                $prepared_moves[] = $prepare_moves;
	                }
				}
				$prepared[] = $prepare;
			}
			$this->items_model->add_item_moves_batch($prepared_moves);
			$this->adjustment_model->add_adjustment_detail_batch($prepared);
			$this->trans_model->save_ref(ADJUSTMENT_TRANS,$ref);
		$this->trans_model->db->trans_complete();
		site_alert($ref." processed",'success');
		echo json_encode(array('msg'=>$ref." processed",'error'=>0));
	}
	public function void($trans_id){
	    $this->load->model('core/trans_model');
	    $user = $this->session->userdata('user');
	    $trans_type = ADJUSTMENT_TRANS;
	    $reason = $this->input->post('reason');
	    $now = $this->site_model->get_db_now('sql');
	    $this->trans_model->db->trans_start();
	        $void = array(
	            'trans_type'=>$trans_type,
	            'trans_id'  =>$trans_id,
	            'reason'    =>$reason,
	            'reg_user'  =>$user['id'],
	            'reg_date'  =>$now,
	        );
	        $this->site_model->add_tbl('trans_voids',$void);
	        $this->site_model->update_tbl('trans_adjustments','adjustment_id',array('inactive'=>1,'update_date'=>$now),$trans_id);
	        $this->site_model->update_tbl('item_moves',array('type_id'=>$trans_type,'trans_id'=>$trans_id),array('inactive'=>1));
	    $this->trans_model->db->trans_complete();
	    // echo json_encode(array('msg'=>"Transaction Voided"));
	    site_alert("Transaction Voided",'success');
	}
}