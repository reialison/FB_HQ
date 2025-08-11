<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Receiving extends CI_Controller {
    public function index(){
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('trans');
        $data['page_title'] = fa('icon-arrow-down')." Receiving";
        $th = array('Reference','Supplier','Received By','Received Date','');
        $data['code'] = create_rtable('trans_receivings','receiving_id','main-tbl',$th,'receiving/search',false,'list');
        $data['load_js'] = 'dine/receive.php';
        $data['use_js'] = 'receiveListJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function get_receiving($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 30;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();      
        $url    =  'receiving/get_receiving';
        $table  =  'trans_receivings';
        $select =  'trans_receivings.*,suppliers.name as supplier_name,users.username as username';
        $join['suppliers'] = 'trans_receivings.supplier_id=suppliers.supplier_id';
        $join['users'] = 'trans_receivings.user_id=users.id';
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        if(isset($post['trans_ref'])){
            $lk = $post['trans_ref'];
            $args["(".$table.".trans_ref like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if(isset($post['supplier_id'])){
            $args["trans_receivings.supplier_id"] = $post['supplier_id'];
        }
        if(isset($post['void'])){
            $args["trans_receivings.inactive"] = $post['void'];
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

                    $void = $this->make->A(fa('fa fa-times fa-lg').' Delete','#',array('title'=>'Void Trans '.$res->trans_ref,
                                                                       'ref'=>$res->receiving_id,
                                                                       'class'=>'btn red btn-sm btn-outline void',
                                                                       'return'=>true));
                }
                else
                    $inactive = "Yes";
                $json[] = array(
                    "id"=>$res->trans_ref,   
                    "title"=>ucwords(strtolower($res->supplier_name)),   
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
        $this->load->helper('dine/receiving_helper');
        $data['code'] = receivingSearch();
        $this->load->view('load',$data);
    }
    public function form(){
        $this->load->model('core/trans_model');
        $this->load->model('dine/receiving_model');
        $this->load->helper('dine/receiving_helper');
        sess_clear('rec_cart');
        $data = $this->syter->spawn('trans');
        $data['page_title'] = fa('icon-arrow-down')." Receiving";
        $ref = $this->trans_model->get_next_ref(RECEIVE_TRANS);
        $data['code'] = receivingFormPage($ref);
        $data['add_css'] = 'js/plugins/typeaheadmap/typeaheadmap.css';
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/receive.php';
        $data['use_js'] = 'receiveJs';
        $this->load->view('page',$data);
    }
    public function get_item_details($item_id=null,$asJson=true){
        $this->load->model('dine/receiving_model');
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
    public function save(){
        $this->load->model('dine/receiving_model');
        $this->load->model('dine/items_model');
        $this->load->model('core/trans_model');
        $user = $this->session->userdata('user');
        $rec_cart = $this->session->userdata('rec_cart');
        // $next_ref = $this->trans_model->get_next_ref(RECEIVE_TRANS);
        $next_ref = $this->input->post('reference');
        $items = array(
            "reference"=>$next_ref,
            "memo"=>$this->input->post('memo'),
            "trans_date"=>date2Sql($this->input->post('trans_date')),
            "trans_ref"=>$next_ref,
            "type_id"=>RECEIVE_TRANS,
            "user_id"=>$user['id'],
            "supplier_id"=>$this->input->post('suppliers')
        );
        $errors = "";
        if (empty($rec_cart)) {
            $errors = 'no item';
            echo json_encode(array('msg'=>"Please select an item first before proceeding",'error'=>$errors));
            return false;
        }
        $this->trans_model->db->trans_start();
            $count = $this->site_model->get_tbl('trans_receivings',array('trans_ref'=>$next_ref),array(),array(),true,'*',null,null,true);
            if($count){
                $errors = 'Reference used';
                echo json_encode(array('msg'=>"Reference ".$next_ref." is already used.",'error'=>$errors));
                return false;
            }

            $id = $this->receiving_model->add_trans_receivings($items);
            $prepared = $prepared_moves = array();
            $total = 0;
            $now = $this->site_model->get_db_now();
            // $datetime = date('Y-m-d H:i:s');
            $datetime = date2SqlDateTime($now);
            foreach ($rec_cart as $val) {
                $prepare = array(
                    'receiving_id' => $id,
                    'item_id'      => (int) $val['item-id'],
                    'case'         => null,
                    'pack'         => null,
                    'uom'          => $val['item-uom'],
                    'price'        => $val['cost']
                );
                $prepare_moves = array(
                    'type_id'  => RECEIVE_TRANS,
                    'trans_id' => $id,
                    'trans_ref'=> $next_ref,
                    'item_id'  => $val['item-id'],
                    'uom'      => $val['item-uom'],
                    'pack_qty' => null,
                    'case_qty' => null,
                    'reg_date' => $datetime,
                );
                $loc_id = explode('-', $val['loc_id']);
                $prepare_moves['loc_id'] = $loc_id[0];
                $last_stock = 0;
                $stocks = $this->items_model->get_latest_item_move(array('loc_id'=>$val['loc_id'],'item_id'=>$val['item-id']));
                if (!empty($stocks->curr_item_qty))
                    $last_stock = $stocks->curr_item_qty;
                if (strpos($val['select-uom'],'pack') !== false) {
                    $converted_qty = $val['qty'] * $val['item-ppack'];
                    $prepare['qty'] = (double) $converted_qty;
                    $prepare['pack'] = (double) $val['qty'];
                    $prepare_moves['qty'] = $converted_qty;
                    $prepare_moves['pack_qty'] = (double) $val['qty'];
                    $prepare_moves['curr_item_qty'] = $last_stock + $converted_qty;
                } elseif (strpos($val['select-uom'],'case') !== false) {
                    $converted_qty = $val['qty'] * $val['item-ppack'] * $val['item-pcase'];
                    $prepare['qty'] = (double) $converted_qty;
                    $prepare['case'] = (double) $val['qty'];
                    $prepare_moves['qty'] = $converted_qty;
                    $prepare_moves['case_qty'] = (double) $val['qty'];
                    $prepare_moves['curr_item_qty'] = $last_stock + $converted_qty;
                } else {
                    $prepare['qty'] = (double)$val['qty'];
                    $prepare_moves['qty'] = (double)$val['qty'];
                    $prepare_moves['curr_item_qty'] = (double)$val['qty'] + $last_stock;
                }
                $prepared[] = $prepare;
                $prepared_moves[] = $prepare_moves;
                $total += $val['cost'];
            }
            $this->receiving_model->add_trans_receiving_batch($prepared);
            $this->receiving_model->update_trans_receivings(array('amount'=>$total),$id);
            $this->items_model->add_item_moves_batch($prepared_moves);
            $this->trans_model->save_ref(RECEIVE_TRANS,$next_ref);
        $this->trans_model->db->trans_complete();
        $this->session->unset_userdata('rec_cart');
        site_alert($next_ref." processed",'success');

        echo json_encode(array('msg'=>$next_ref." processed",'error'=>$errors));
    }
    public function void($trans_id){
        $this->load->model('core/trans_model');
        $user = $this->session->userdata('user');
        $trans_type = RECEIVE_TRANS;
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
            $this->site_model->update_tbl('trans_receivings','receiving_id',array('inactive'=>1,'update_date'=>$now),$trans_id);
            $this->site_model->update_tbl('item_moves',array('type_id'=>$trans_type,'trans_id'=>$trans_id),array('inactive'=>1));
        $this->trans_model->db->trans_complete();
        // echo json_encode(array('msg'=>"Transaction Voided"));
        site_alert("Transaction Voided",'success');
    }
}