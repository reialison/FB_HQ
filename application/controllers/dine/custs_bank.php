<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Custs_bank extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/customers_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/custs_bank_helper');
		$this->load->model('dine/main_model');
		$this->load->model('dine/setup_model');
		$this->load->helper('dine/print_helper');
		$this->load->helper('core/string_helper');
	}
    public function index(){
    	$data = $this->syter->spawn('customers');
    	$data['code'] = custsBankPage();
    	$data['load_js'] = 'dine/custs_bank.php';
    	$data['use_js'] = 'indexJs';

    	$data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css');
    	$data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');

    	$data['noNavbar'] = true;
    	$this->load->view('cashier',$data);
    }
    public function customers_money(){
    	$this->load->helper('site/site_forms_helper');
    	$data = $this->syter->spawn('customers');
    	$th = array('ID','Customer','Total Amount','Last Deposit Date','');
    	$code = create_rtable('customers','cust_id','customers-tbl',$th,'custs_bank/search_custs_deposit_form');
    	$data['code'] = $code;
    	$data['load_js'] = 'dine/custs_bank.php';
    	$data['use_js'] = 'customersMoneyList';
    	// $data['add_css'] = 'css/cashier.css';
    	// $data['noNavbar'] = true; /*Hides the navbar. Comment-out this line to display the navbar.*/
    	$this->load->view('load',$data);
    }	
    public function get_custs_deposits(){
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
	    $args['customers.inactive'] = 0;
	    $select = " customers.cust_id,customers.fname,customers.mname,customers.lname,customers.suffix,
					SUM(customers_bank.amount) AS cust_money,
					MAX(customers_bank.datetime) AS last_deposit";
	    $join["customers_bank"] = array('content'=>"customers.cust_id = customers_bank.cust_id");
	    $order['last_deposit'] = 'desc';
	    $group = 'customers_bank.cust_id';

	    $count = $this->site_model->get_tbl('customers',$args,null,$join,true,$select,$group,null,true);
	    $page = paginate('pos_customers/get_customers',$count,$total_rows,$pagi);
	    $items = $this->site_model->get_tbl('customers',$args,$order,$join,true,$select,$group,$page['limit']);
	    // echo $this->site_model->db->last_query();
	    $json = array();
	    if(count($items) > 0){
	        $ids = array();
	        foreach ($items as $res) {
	        	$link = "";
	        	// if($termninal == 1){
		        // 	$link = $this->make->A(fa('fa-edit fa-lg'),base_url().'pos_customers/customer_terminal_form/'.$res->cust_id,array('return'=>'true'));	        		
	        	// }
	        	// else{
		        // 	$link = $this->make->A(fa('fa-edit fa-lg'),base_url().'pos_customers/form/'.$res->cust_id,array('return'=>'true'));	        		
	        	// }
	            $json[$res->cust_id] = array(
	                "id"=>$res->cust_id,   
	                "title"=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix,   
	                "desc"=>num($res->cust_money),   
	                "date_reg"=>sql2Date($res->last_deposit),
	                "link"=>$link
	            );
	        }
	    }
	    echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
    public function search_custs_deposit_form(){
        $data['code'] = customerDepositSearchForm();
        $this->load->view('load',$data);
    }	
    public function deposit(){
    	$data['code'] = depositPage();
    	$data['load_js'] = 'dine/custs_bank.php';
    	$data['use_js'] = 'depositJs';
    	$this->load->view('load',$data);
    }
    public function deposit_db(){
    	$this->load->model('core/trans_model');
        $this->load->model('core/sync_model');

    	$next_ref = $this->trans_model->get_next_ref(CUST_DEPOSIT_TRANS);
    	$user = sess('user');
    	$card_type = $this->input->post('card_type');
    	if(!$this->input->post('card_type')){
    		$card_type = "";
    	}
		$items = array(
			'trans_ref' 			=> $next_ref,
			'type_id' 				=> CUST_DEPOSIT_TRANS,
			"cust_id"      			=> $this->input->post('cust_id'),
			"amount"       			=> $this->input->post('amount'),
			"amount_type"       	=> $this->input->post('amount_type'),
			"card_no"      			=> $this->input->post('card_no'),
			"card_type"      		=> $card_type,
			"approval_code"      	=> $this->input->post('approval_code'),
			"user_id"    		  	=> $user['id'],
			"pos_id"	    		=> TERMINAL_ID,
			"remarks"	    		=> $this->input->post('remarks')
		);
		$this->trans_model->db->trans_start();
			$id = $this->site_model->add_tbl('customers_bank',$items,array('datetime'=>'NOW()'));

            if(LOCALSYNC){
                $this->sync_model->add_customers_bank($id);
            }
			$this->trans_model->save_ref(CUST_DEPOSIT_TRANS,$next_ref);
		$this->trans_model->db->trans_complete();
		site_alert("Deposit Success",'success');
		$this->print_deposit($id,true);
    }
    public function print_deposit($id=null,$asJson=false){
    	$header = $this->print_header();
    	$print_str = $header['print_str'];
    	$branch = $header['branch'];
    	$select = "customers_bank.*,users.username,customers.fname,customers.mname,customers.lname,customers.suffix,terminals.terminal_code";
    	$args['bank_id'] = $id;
    	$join["users"] = array('content'=>"customers_bank.user_id = users.id");
    	$join["customers"] = array('content'=>"customers_bank.cust_id = customers.cust_id");
    	$join["terminals"] = array('content'=>"customers_bank.pos_id = terminals.terminal_id");
    	$result = $this->site_model->get_tbl('customers_bank',$args,array(),$join,true,$select);
    	if(count($result) > 0 ){
    		$trans = $result[0];
    		$print_str .= append_chars(strtoupper($trans->username),'right',19," ").append_chars(date2SqlDateTime($trans->datetime),'left',19," ")."\r\n";
    		$print_str .= "Terminal ID : ".($trans->terminal_code)."\r\n";
    		$print_str .= "======================================"."\r\n";
    		$print_str .= align_center('Acknowledgement Receipt - Deposit',38," ")."\r\n";
    		$print_str .= align_center("Reference # ".$trans->trans_ref,38," ")."\r\n";
    		$print_str .= "======================================"."\r\n";
    		$print_str .= "\r\n";
    		$customer_name = ucwords(strtolower($trans->fname." ".$trans->mname." ".$trans->lname." ".$trans->suffix));
    		$print_str .= append_chars(substrwords("Customer: ",18,""),"right",12," ").$customer_name;
    		$print_str .= "\r\n";
    		$print_str .= "\r\n";
    		$print_str .= "Amount Details \r\n";
    		$print_str .= append_chars(strtoupper($trans->amount_type),"right",27," ").append_chars("P ".num($trans->amount,2),"right",10," ")."\r\n";
    		if($trans->card_type != "")
	    		$print_str .= append_chars("  Card Type    : ".$trans->card_type,"right",38," ")."\r\n";
    		if($trans->card_no != "")
	    		$print_str .= append_chars("  Card No.     : ".$trans->card_no,"right",38," ")."\r\n";
    		if($trans->approval_code != "")
	    		$print_str .= append_chars("  Approval Code: ".$trans->approval_code,"right",38," ")."\r\n";
    		$print_str .= "\r\n";
    		$print_str .= "======================================"."\r\n";
    		$print_str .= "\r\n";
    		if($branch['contact_no'] != ""){
    		    $print_str .= align_center("For feedback, please call us at",38," ")."\r\n"
    		                 .align_center($branch['contact_no'],38," ")."\r\n";
    		}
    		if($branch['email'] != ""){
    		    $print_str .= align_center("Or Email us at",38," ")."\r\n" 
    		                 .align_center($branch['email'],38," ")."\r\n";
    		}
    		if($branch['website'] != "")
    		    $print_str .= align_center("Please visit us at \r\n".$branch['website'],38," ")."\r\n";

    	}
    	$this->do_print($print_str,$asJson);
    }
    public function do_print($print_str=null,$asJson=false){
        if (!$asJson) {
            // echo "<pre style='background-color:#fff'>$print_str</pre>";
            echo json_encode(array("code"=>"<pre style='background-color:#fff'>$print_str</pre>"));
        }
        else{
            $filename = "report.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);

            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);

            fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
            fclose($fh1);
            session_write_close();
            // exec($filename);
            exec($batfile);
            session_start();
            unlink($filename);
            unlink($batfile);  
        }
    }
    public function print_header(){
        $branch_details = $this->setup_model->get_branch_details();
        $branch = array();
        foreach ($branch_details as $bv) {
            $branch = array(
                'id' => $bv->branch_id,
                'res_id' => $bv->res_id,
                'branch_code' => $bv->branch_code,
                'name' => $bv->branch_name,
                'branch_desc' => $bv->branch_desc,
                'contact_no' => $bv->contact_no,
                'delivery_no' => $bv->delivery_no,
                'address' => $bv->address,
                'base_location' => $bv->base_location,
                'currency' => $bv->currency,
                'inactive' => $bv->inactive,
                'tin' => $bv->tin,
                'machine_no' => $bv->machine_no,
                'bir' => $bv->bir,
                'permit_no' => $bv->permit_no,
                'serial' => $bv->serial,
                'accrdn' => $bv->accrdn,
                'email' => $bv->email,
                'website' => $bv->website,
                'store_open' => $bv->store_open,
                'store_close' => $bv->store_close,
                "pos_footer"=>$bv->pos_footer,
                "rec_footer"=>$bv->rec_footer,
            );
        }
        $userdata = $this->session->userdata('user');
        $print_str = "\r\n\r\n";
        $wrap = wordwrap($branch['name'],35,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $print_str .= align_center($v,38," ")."\r\n";
        }
        $wrap = wordwrap($branch['address'],35,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $print_str .= align_center($v,38," ")."\r\n";
        }
        $print_str .= 
         align_center('TIN: '.$branch['tin'],38," ")."\r\n"
        .align_center('ACCRDN: '.$branch['accrdn'],38," ")."\r\n"
        // .$this->align_center('BIR # '.$branch['bir'],42," ")."\r\n"
        .align_center('MIN: '.$branch['machine_no'],38," ")."\r\n"
        // .align_center('SN: '.$branch['serial'],38," ")."\r\n"
        .align_center('PERMIT: '.$branch['permit_no'],38," ")."\r\n";
        $print_str .= "======================================"."\r\n";
        return array("print_str"=>$print_str,"branch"=>$branch);
    }
}