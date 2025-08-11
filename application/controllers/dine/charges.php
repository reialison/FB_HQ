<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Charges extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/settings_model');
		$this->load->helper('dine/settings_helper');
		$this->load->helper('site/site_forms_helper');
	}
	public function index(){
     	$data = $this->syter->spawn('charges');
     	$list = $this->settings_model->get_charges();
        $data['page_title'] = fa('icon-equalizer')." Charges";
     	$data['code'] = site_list_form("charges/form","charge_form","Charges",$list,array('charge_code','charge_name'),'charge_id');
     	$data['add_js'] = 'js/site_list_forms.js';
        $this->load->view('page',$data);
	}
	public function form($ref=null){
        $item = array();
        if($ref != null){
            $items = $this->settings_model->get_charges($ref);
            $item = $items[0];
        }
        $data['code'] = makeChargeForm($item);
        $this->load->view('load',$data);
    }
    public function db(){
        $this->load->model('dine/main_model');
        $items = array(
            "charge_code"=>$this->input->post('charge_code'),
            "charge_name"=>$this->input->post('charge_name'),
            "charge_amount"=>$this->input->post('charge_amount'),
            "no_tax"=>$this->input->post('no_tax'),
            "absolute"=>$this->input->post('absolute'),
            "inactive"=>$this->input->post('inactive'),
        );
        if($this->input->post('charge_id')){
            $this->settings_model->update_charges($items,$this->input->post('charge_id'));
            $id = $this->input->post('charge_id');
            $act = 'update';
            $msg = 'Updated Charge. '.$this->input->post('charge_name');
            $this->main_model->update_tbl('charges','charge_id',$items,$id);
        }else{
            $id = $this->settings_model->add_charges($items);
            $id = $this->input->post('charge_id');
            $act = 'add';
            $msg = 'Added  new Charge'.$this->input->post('charge_name');
            $this->main_model->add_trans_tbl('charges',$items);
        }
        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('charge_code')." ".$this->input->post('charge_name'),"act"=>$act,'msg'=>$msg));
    }

    //nicko 
    public function charges_new(){
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('general_settings');
        $data['page_title'] = fa('icon-doc')." Charges";
        // $data['page_subtitle'] = 'Item Category Management';
        $th = array('Code','Name','Branch','Rate','');
        $data['code'] = create_rtable('charges','charges_id','charges-tbl',$th);
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');
        $data['load_js'] = 'dine/charges.php';
        $data['use_js'] = 'chargesnewJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }

    public function get_charges($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 1000;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        // if($this->input->post('name')){
        //     $lk  =$this->input->post('name');
        //     $args["(categories.name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        // }
        // if($this->input->post('inactive')){
        //     $args['categories.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
        // }
        // if($this->input->post('branch_id')){
        //     // echo $this->input->post('branch_id');die();
        //     $args['categories.branch_code'] = array('use'=>'where','val'=>$this->input->post('branch_id'));
        // }
        // $args['receipt_discounts.is_item_disc'] = array('use'=>'where','val'=>"0");
        // $join = null;
        $join["branch_details"] = array('content'=>"branch_details.branch_code = charges.branch_code");
        $count = $this->site_model->get_tbl('charges',$args,array(),$join,true,'charges.*',null,null,true);
        $page = paginate('charges/get_charges',$count,$total_rows,$pagi);
        $char = $this->site_model->get_tbl('charges',$args,array(),$join,true,'charges.*,branch_details.branch_name',null,$page['limit']);
        // echo "<pre>",print_r($disc),"</pre>";die();
        $json = array();
        if(count($char) > 0){
            $ids = array();
            foreach ($char as $res) {
                
                $link = $this->make->A(fa('fa-edit fa-lg').' Edit','#',array('class'=>'btn btn-outline-success-600 radius-8 px-20 py-11 align-items-center gap-2','id'=>'edit-'.$res->charge_id.'-'.$res->branch_code,'ref'=>$res->charge_id,'ref2'=>$res->branch_code,'return'=>'true'));

                $charge_amount = $res->charge_amount;
                if($res->absolute == 0){
                    $charge_amount = $res->charge_amount."%";
                }

                $json[$res->charge_id.'-'.$res->branch_code] = array(
                    "id"=>$res->charge_code,   
                    "title"=>ucwords(strtolower($res->charge_name)),
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),   
                    "branch"=>$res->branch_name,
                    "charge_amount"=>$charge_amount,
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "link"=>$link
                );
                // $ids[] = $res->disc_id;
            }
        }
         // echo "<pre>",print_r($json),"</pre>";die();
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }

    public function charges_form($ref=null,$branch=null){
        $this->load->helper('dine/settings_helper');
        $this->load->model('dine/settings_model');
        $data = $this->syter->spawn('general_spettings');
        $data['page_title'] = fa('icon-doc')." Charges";
        $charges = array();
        if($ref != null){
            $charges = $this->settings_model->get_charges2($ref);
            $charges = $charges[0];
        }
        $data['code'] = makeChargesForm($charges,$branch);
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');
        $data['load_js'] = 'dine/charges.php';
        $data['use_js'] = 'chargeformJs';
        $this->load->view('page',$data);
    }

    public function charges_new_db()
    {
        $this->load->model('dine/settings_model');
        $this->load->model('dine/main_model');
        $this->load->model('dine/menu_model');
        $branch_code = $this->input->post('b_code');
        $charge_id = $this->input->post('charge_id');
        $branches = $this->menu_model->get_all_branch();
        foreach ($branches as $value) {
            $chr_id = $this->settings_model->get_last_charges($value->branch_code);
            $items[] = array(
                "charge_id"=>$chr_id+1,
                "charge_code"=>$this->input->post('charge_code'),
                "charge_name"=>$this->input->post('charge_name'),
                "charge_amount"=>$this->input->post('charge_amount'),
                "no_tax"=>(int)$this->input->post('no_tax'),
                "absolute"=>(int)$this->input->post('absolute'),
                "branch_code"=>$value->branch_code,
                // "is_item_disc"=>"0",
                "inactive"=>1
            );
        }
        if($this->input->post('charge_id')){
            unset($items[0]['charge_id'],$items[0]['branch_code']);
            $inact = 0;
            if($this->input->post('inactive')){
                $inact = $this->input->post('inactive');
            }
            $inactive = array(
                "inactive"=>$inact,
            );
            if(!empty($branch_code)){
                foreach ($branch_code as $value) {
                    $this->settings_model->update_charges2($items[0], $this->input->post('charge_id'),$value);
                    $this->settings_model->update_charges2($inactive, $this->input->post('charge_id'),$value);
                    // echo "<pre>",print_r($inactive),"</pre>";die();
                }
            }
            else{
                $msg = 'Error! Please select branch. ';
                site_alert($msg,'error');
            }
            $id = $this->input->post('charge_id');
            $act = 'update';
            $msg = 'Updated Charges: '.$items[0]['charge_name'];
            site_alert($msg,'success');
            // $this->main_model->update_tbl('receipt_discounts','disc_id',$items,$id);
        }else{
            $id = $this->settings_model->add_bulk_charges2($items);
            $inactive = array(
                "inactive"=>0,
            );
            if(!empty($branch_code)){
                foreach ($branch_code as $value) {
                // echo "<pre>",print_r($value),"</pre>";die();
                   $check = $this->settings_model->update_charges2($inactive,$items[0]['charge_id'],$value);
                }
            }
            else{
                foreach ($branches as $value) {
                   $check = $this->settings_model->update_charges2($inactive,$items[0]['charge_id'],$value->branch_code);
                }
            // echo "<pre>",print_r($check),"</pre>";die();
            }

            $act = 'add';
            $msg = 'Added New Charges: '.$items[0]['charge_name'];
            site_alert($msg,'success');
            // $this->main_model->add_trans_tbl('receipt_discounts',$items);
        }
        echo json_encode(array("id"=>$id,"desc"=>$items[0]['charge_name'],"act"=>$act,'msg'=>$msg));
    }
}