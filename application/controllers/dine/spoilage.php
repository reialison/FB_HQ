<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Spoilage extends CI_Controller {
    public function index(){
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('trans');
        $data['page_title'] = fa(' icon-trash')." Spoilage";
        $th = array('Reference','Trans by','Trans Date','');
        $data['code'] = create_rtable('trans_spoilage','spoil_id','main-tbl',$th,'spoilage/search',false,'list');
        $data['load_js'] = 'dine/spoilage.php';
        $data['use_js'] = 'spoilListJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function get_spoilage($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 30;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();      

        $url    =  'spoilage/get_spoilage';
        $table  =  'trans_spoilage';
        $select =  'trans_spoilage.*,users.username as username';
        $join['users'] = 'trans_spoilage.user_id=users.id';
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
                    $void = $this->make->A(fa('fa fa-times fa-lg').' Delte','#',array('title'=>'Void Trans '.$res->trans_ref,
                                                                       'ref'=>$res->spoil_id,
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
        $this->load->helper('dine/receiving_helper');
        $data['code'] = receivingSearch();
        $this->load->view('load',$data);
    }
    public function form(){
        $this->load->model('core/trans_model');
        $this->load->helper('dine/spoilage_helper');
        sess_clear('spoil_cart');
        $data = $this->syter->spawn('trans');
        $data['page_title'] = fa('icon-trash')." Spoilage";
        
        $ref = $this->trans_model->get_next_ref(SPOIL_TRANS);
        $data['code'] = spoilageFormPage($ref);
        $data['add_css'] = 'js/plugins/typeaheadmap/typeaheadmap.css';
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/spoilage.php';
        $data['use_js'] = 'spoliageJs';
        $this->load->view('page',$data);
    }
    public function save(){
        $this->load->model('dine/items_model');
        $this->load->model('core/trans_model');
        $user = $this->session->userdata('user');
        $spoil_cart = $this->session->userdata('spoil_cart');
        $next_ref = $this->input->post('reference');
        $now = $this->site_model->get_db_now();
        $datetime = date2SqlDateTime($now);
        $items = array(
            "memo"=>$this->input->post('memo'),
            "trans_date"=>date2Sql($this->input->post('trans_date')),
            "trans_ref"=>$next_ref,
            "type_id"=>SPOIL_TRANS,
            "user_id"=>$user['id'],
            "reg_date"=>$datetime
        );
        $errors = "";
        if (empty($spoil_cart)) {
            $errors = 'no item';
            echo json_encode(array('msg'=>"Please select an item first before proceeding",'error'=>$errors));
            return false;
        }

        $this->trans_model->db->trans_start();
            $count = $this->site_model->get_tbl('trans_spoilage',array('trans_ref'=>$next_ref),array(),array(),true,'*',null,null,true);
            if($count){
                $errors = 'Reference used';
                echo json_encode(array('msg'=>"Reference ".$next_ref." is already used.",'error'=>$errors));
                return false;
            }

            $id = $this->site_model->add_tbl('trans_spoilage',$items);
            $prepared = array();
            foreach ($spoil_cart as $val) {
                $prepared[] = array(
                    'spoil_id' => $id,
                    'item_id'  => (int) $val['item-id'],
                    'qty'      => (double)$val['qty'],
                    'uom'      => $val['item-uom']
                );
            }
            $this->site_model->add_tbl_batch('trans_spoilage_details',$prepared);
            $moves = array();
            foreach ($spoil_cart as $val) {
                $stocks = $this->items_model->get_latest_item_move(array('loc_id'=>$val['loc_id'],'item_id'=>$val['item-id']));
                if (!empty($stocks->curr_item_qty))
                    $last_stock = $stocks->curr_item_qty;

                $moves[] = array(
                    'type_id'  => SPOIL_TRANS,
                    'trans_id' => (int)$id,
                    'trans_ref'=> $next_ref,
                    'loc_id'   => $val['loc_id'],
                    'item_id'  => (int) $val['item-id'],
                    'qty'      => ($val['qty'] * -1),
                    'uom'      => $val['item-uom'],
                    'curr_item_qty'=> $last_stock + ($val['qty'] * -1),
                    'reg_date' => $datetime,
                );
            }
            $this->site_model->add_tbl_batch('item_moves',$moves);
            
            $this->trans_model->save_ref(SPOIL_TRANS,$next_ref);
        $this->trans_model->db->trans_complete();
        $this->session->unset_userdata('spoil_cart');
        site_alert($next_ref." processed",'success');
        echo json_encode(array('msg'=>$next_ref." processed",'error'=>$errors));
    }
    public function void($trans_id){
        $this->load->model('core/trans_model');
        $user = $this->session->userdata('user');
        $trans_type = SPOIL_TRANS;
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
            $this->site_model->update_tbl('trans_spoilage','spoil_id',array('inactive'=>1,'update_date'=>$now),$trans_id);
            $this->site_model->update_tbl('item_moves',array('type_id'=>$trans_type,'trans_id'=>$trans_id),array('inactive'=>1));
        $this->trans_model->db->trans_complete();
        // echo json_encode(array('msg'=>"Transaction Voided"));
        site_alert("Transaction Voided",'success');
    }
}