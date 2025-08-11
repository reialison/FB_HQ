<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos_customers extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/customers_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/pos_customers_helper');
		$this->load->model('dine/main_model');
	}
	public function index(){
		$this->load->helper('site/site_forms_helper');
		$data = $this->syter->spawn('customers');
		$th = array('ID','Name','Address','Register Date','Edit');
		$data['code'] = create_rtable('customers','cust_id','customers-tbl',$th,'pos_customers/search_customers_form');
		$data['load_js'] = 'dine/pos_customers.php';
		$data['use_js'] = 'listFormJs';
		$data['page_no_padding'] = true;
		// $data['sideBarHide'] = true;
		$this->load->view('page',$data);
	}
	public function get_customers($termninal=0){
	    $this->load->helper('site/pagination_helper');
	    $pagi = null;
	    $args = array();
	    $total_rows = 30;
	    if($this->input->post('pagi'))
	        $pagi = $this->input->post('pagi');
	    $post = array();
	    if(count($this->input->post()) > 0){
	        $post = $this->input->post();
	    }
	    
	    if($this->input->post('cust_name')){
	        $lk  =$this->input->post('cust_name');
	        $args["(customers.fname like '%".$lk."%' OR customers.mname like '%".$lk."%' OR customers.lname like '%".$lk."%' OR customers.suffix like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
	    }
	    if($this->input->post('inactive')){
	        $args['customers.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
	    }
	    $join = null;
	    $count = $this->site_model->get_tbl('customers',$args,array(),$join,true,'*',null,null,true);
	    $page = paginate('pos_customers/get_customers',$count,$total_rows,$pagi);
	    $items = $this->site_model->get_tbl('customers',$args,array(),$join,true,'*',null,$page['limit']);
	    $json = array();
	    if(count($items) > 0){
	        $ids = array();
	        foreach ($items as $res) {
	        	if($termninal == 1){
		        	$link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'pos_customers/customer_terminal_form/'.$res->cust_id,array('class'=>'btn blue btn-sm btn-outline','return'=>'true'));	        		
	        	}
	        	else{
		        	$link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'pos_customers/form/'.$res->cust_id,array('class'=>'btn blue btn-sm btn-outline','return'=>'true'));	        		
	        	}
	            $json[$res->cust_id] = array(
	                "id"=>$res->cust_id,   
	                "title"=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix,   
	                "desc"=>$res->street_no." ".$res->street_address." ".$res->city." ".$res->region,   
	                "date_reg"=>sql2Date($res->reg_date),
	                "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
	                "link"=>$link
	            );
	        }
	    }
	    echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
	}
	public function search_customers_form(){
	    $data['code'] = customerSearchForm();
	    $this->load->view('load',$data);
	}
	public function form($cust_id=null){
        $data = $this->syter->spawn('customers');
        $data['code'] = customersFormPage($cust_id);
        $data['add_css'] = 'js/plugins/typeaheadmap/typeaheadmap.css';
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/pos_customers.php';
        $data['use_js'] = 'customerFormJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function details_load($cust_id=null){
        $customer=array();
        if($cust_id != null){
            $customers = $this->site_model->get_tbl('customers',array('cust_id'=>$cust_id));
            $customer=$customers[0];
        }
        $data['code'] = customerDetailsLoad($customer,$cust_id);
        $data['load_js'] = 'dine/pos_customers.php';
        $data['use_js'] = 'detailsLoadJs';
        $this->load->view('load',$data);
    }
    public function details_db(){
        $error = "";
        $id = "";
        $act = "";
        $items = array(
            "fname"=>$this->input->post('fname'),
            "mname"=>$this->input->post('mname'),
            "lname"=>$this->input->post('lname'),
            "suffix"=>$this->input->post('suffix'),
            "email"=>$this->input->post('email'),
            "phone"=>$this->input->post('phone'),
            "street_no"=>$this->input->post('street_no'),
            "street_address"=>$this->input->post('street_address'),
            "city"=>$this->input->post('city'),
            "zip"=>$this->input->post('zip'),
            "inactive"=>(int)$this->input->post('inactive')
        );

        if($this->input->post('new')){
            $cargs['email'] = $this->input->post('email');
            $select = 'email';
            $cresult = $this->site_model->get_tbl('customers',$cargs,array(),'',true,$select);
            if(count($cresult) > 0){
                $error = "error";
                $msg = "Email is already taken.";
            }  
            else{
                $id = $this->site_model->add_tbl('customers',$items,array('reg_date'=>'NOW()'));
                $act = 'add';
                $msg = 'Added  new Customer '.$this->input->post('fname').' '.$this->input->post('mname').' '.$this->input->post('lname').' '.$this->input->post('suffix');
                $items['cust_id'] = $id;
                $this->main_model->add_trans_tbl('customers',$items);
                site_alert($msg,'success');
            }
        }
        else{
            if($this->input->post('form_cust_id')){
                $this->site_model->update_tbl('customers','cust_id',$items,$this->input->post('form_cust_id'));
                $id = $this->input->post('form_cust_id');
                $act = 'update';
                $msg = 'Updated Customer '.$this->input->post('fname').' '.$this->input->post('mname').' '.$this->input->post('lname').' '.$this->input->post('suffix');
                $this->main_model->update_tbl('customers','cust_id',$items,$id);
            }else{
                $cargs['email'] = $this->input->post('email');
                $select = 'email';
                $cresult = $this->site_model->get_tbl('customers',$cargs,array(),'',true,$select);
                if(count($cresult) > 0){
                    $error = "error";
                    $msg = "Email is already taken.";
                }  
                else{
                    $id = $this->site_model->add_tbl('customers',$items,array('reg_date'=>'NOW()'));
                    $act = 'add';
                    $msg = 'Added  new Customer '.$this->input->post('fname').' '.$this->input->post('mname').' '.$this->input->post('lname').' '.$this->input->post('suffix');
                    $items['cust_id'] = $id;
                    $this->main_model->add_trans_tbl('customers',$items);
                }
            }
        }
        echo json_encode(array("error"=>$error,"id"=>$id,"desc"=>$this->input->post('fname').' '.$this->input->post('mname').' '.$this->input->post('lname').' '.$this->input->post('suffix'),"act"=>$act,'msg'=>$msg));
    }
    public function customer_terminal(){
    	$this->load->helper('site/site_forms_helper');
    	$data = $this->syter->spawn('customers');
    	$th = array('ID','Name','Address','Register Date','Inactive','');
    	$table = create_rtable('customers','cust_id','customers-tbl',$th,'pos_customers/search_customers_form');
    	$this->make->sDiv(array('style'=>'background-color:#fff;margin:10px;'));
    	$this->make->sBox('solid');
    	$this->make->sBoxBody();
    	$this->make->sDivRow(array('style'=>'margin-bottom:10px;'));
    		$this->make->sDivCol(12,'right');
    			$this->make->A(fa('fa-reply').' Back to Terminal',base_url()."cashier",array('class'=>'btn btn-primary'));
    		$this->make->eDivCol();
    	$this->make->eDivRow();
		$this->make->append($table);
    	$this->make->eBoxBody();
    	$this->make->eBox();
    	$this->make->eDiv();
    	$code = $this->make->code();
    	$data['code'] = $code;
    	$data['load_js'] = 'dine/pos_customers.php';
    	$data['use_js'] = 'listTerminalFormJs';
    	$data['add_css'] = 'css/cashier.css';
    	// $data['noNavbar'] = true; /*Hides the navbar. Comment-out this line to display the navbar.*/
    	$this->load->view('cashier',$data);
    }
    public function customer_terminal_form($cust_id=null){
    	$data = $this->syter->spawn('customers');
    	$data['code'] = customersFormTerminalPage($cust_id);
    	$data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/cashier.css');
    	$data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
    	$data['load_js'] = 'dine/pos_customers.php';
    	$data['use_js'] = 'customerFormJs';
    	// $data['page_no_padding'] = true;
    	$this->load->view('cashier',$data);
    }
}