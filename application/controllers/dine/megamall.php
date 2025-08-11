<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/cashier.php");
class Megamall extends Cashier {
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/megamall_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
	}
	public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = megamallPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/megamall.php';
        $data['use_js'] = 'megamallPageJs';
        $this->load->view('load',$data);
	}
	public function files(){
		$data = $this->syter->spawn(null);
		$data['code'] = filesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/megamall.php';
		$data['use_js'] = 'fileJs';
		$this->load->view('load',$data);
	}
	public function daily_files(){
		$date = $this->input->post('file_date');
		// $date = "09/29/2015";
		// $path = "C:/SM/";
		// $file_txt = date('mdY',strtotime($date)).".txt";
		$file_txt = "Z".date('Ymd',strtotime($date)).".flg";
        $year = date('Y',strtotime($date));
        $month = date('M',strtotime($date));
        $text = "C:/SM/".$file_txt;
		if(file_exists($text)){
			$fh = fopen($text, 'r');
			$theData = fread($fh, filesize($text));
			fclose($fh);
			// $mod = "<pre>".$theData."</pre>";
			$name = array(
				1 => 'BR_CODE',2 => 'N_CHECK',3 => 'CLAS_C', 4 => 'CLAS_TRD_C',5 => 'STOR_NO',6 => 'TSL_NEW_A',7 => 'TSL_OLD_A',8 => 'SALE_TYPE',9 => 'TSL_DAY_A', 10 => 'TLS_DIS_A', 
				11 => 'TSL_DIS_B',12 => 'TSL_DIS_C',13 => 'TSL_DIS_D',14 => 'TSL_DIS_E',15 => 'TSL_DIS_F',16 => 'TSL_DIS_G',17 => 'TSL_DIS_H',18 => 'TSL_DIS_I',19 => 'TSL_DIS_J',20 => 'TSL_DIS_K',
				21 => 'TSL_DIS_L',22 => 'TSL_TAX_A',23 => 'TSL_TAX_B',24 => 'TSL_ADJ_A',25 => 'TSL_ADJ_POS',26 => 'TSL_ADJ_NEG',27 => 'TSL_ADJ_NT_POS',28 => 'TSL_ADJ_NT_NEG',29 => 'TSL_NET_A',30 => 'TSL_VOID',
				31 => 'TSL_RFND',32 => 'TSL_TX_SAL',33 => 'TSL_NX_SAL',34 => 'TSL_CHG',35 => 'TSL_CSH',36 => 'TSL_GC',37 => 'TSL_EPS',38 => 'TSL_TND',39 => 'TSL_MCRD',40 => 'TSL_VISA',
				41 => 'TSL_AMEX',42 => 'TSL_VISA',43 => 'TSL_JCB',44 => 'TSL_OTCRD',45 => 'TSL_SV_CHG',46 => 'TSL_OT_CHG',47 => 'TSL_FT',48 => 'TSL_LT',49 => 'TSL_NT',50 => 'TSL_BEG_INV',
				51 => 'TSL_END_INV',52 => 'TSL_TC_CASH',53 => 'TSL_TC_GC',54 => 'TSL_TC_EPS',55 => 'TSL_TC_TND',56 => 'TSL_TC_MCD',57 => 'TSL_TC_VIS',58 => 'TSL_TC_AMX',59 => 'TSL_TC_DIN',60 => 'TSL_TC_JCB',
				61 => 'TSL_TC_OC',62 => 'TSL_TC_MCH',63 => 'TSL_TC_SRL',64 => 'TSL_ZCNT',65 => 'TST_TIME',66 => 'TLS_DTE' 
			);
			$data = explode(',',$theData);
			$this->make->sTable(array('class'=>'table'));
			foreach ($data as $key => $val) {
				$this->make->sRow();
					$this->make->td(($key+1));
					if(isset($name[$key+1]))
						$this->make->td("[".$name[$key+1]."] ");	
					$this->make->td($val);
				$this->make->eRow();					
				// $mod .= ($key+1)." ";
				// $mod .= " = ".$val."<br>";
			}
			$this->make->eTable();
			$mod = $this->make->code();
		}
		else{
			$mod = "<center> file not found. </center>";
		}
		echo "<pre>".$mod."</pre>";
		// echo json_encode(array('daily'=>$mod));
	}
	public function settings(){
		$data = $this->syter->spawn(null);
		$objs = $this->site_model->get_tbl('megamall');
		$obj = array();
		if(count($objs) > 0){
			$obj = $objs[0];			
		}
		$data['code'] = megamallSettingsPage($obj);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/megamall.php';
		$data['use_js'] = 'settingsJs';
		$this->load->view('load',$data);
	}
	public function settings_db(){
		$items = array(
			'br_code'=>$this->input->post('br_code'),
			'tenant_no'=>$this->input->post('tenant_no'),
			'class_code'=>$this->input->post('class_code'),
			'outlet_no'=>$this->input->post('outlet_no'),
			'trade_code'=>$this->input->post('trade_code'),
		);
		if ($this->input->post('megamall_id')) {
			$id = $this->input->post('megamall_id');
			$this->site_model->update_tbl('megamall','id',$items,$id);
			$msg = "Updated Settings.";
		} else {
			$id = $this->site_model->add_tbl('megamall',$items);
			$msg = "Updated Settings";
		}
		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function scenario(){
	    $trans = 0;
	    $payments = array(
    	    '8'=> array(
    	            array('cash'=>array('type'=>'cash','amount'=>250.00,'card_number'=>null,'approval'=>null)),
    	            array('credit'=>array('type'=>'credit','amount'=>400.00,'card_number'=>'12345678','approval'=>'12345678')),
    	          ),
    	    '16'=> array(
    	            array('credit'=>array('type'=>'credit','amount'=>500.00,'card_number'=>'12345678','approval'=>'12345678')),
    	            array('debit'=>array('type'=>'debit','amount'=>150.00,'card_number'=>'12345678','approval'=>'12345678'))
    	          ),
    	    '23'=> array(
    	            array('debit'=>array('type'=>'debit','amount'=>575.00,'card_number'=>'12345678','approval'=>'12345678')),
    	            array('smac'=>array('type'=>'smac','amount'=>75.00,'card_number'=>'12345678','approval'=>null)),
    	          ),
    	);
	    $ctr = 1;
	    $last = count($payments);
	    foreach ($payments as $no => $pay) {
	        $this->cashier_model->db = $this->load->database('default', TRUE);
	        $this->site_model->db = $this->load->database('default', TRUE);
	        // $trans_cart = sess('trans_cart');
	        // echo $no."==";
	        // echo var_dump($trans_cart)."<br>";
	        $order = $this->submit_trans(false,null,false,null,null,null,false,null,false);
	        // echo var_dump($order)."<br>";
	        $sales_id = $order['id'];
	        foreach ($pay as $key => $val) {
	            foreach ($val as $v) {
	                $send = false;
	                if($ctr == $last){
	                    $send = true;
	                }
	                $this->add_payment2($sales_id,$v['amount'],$v['type'],$v['approval'],$v['card_number'],$send);
	            }
	        }
	        $this->cashier_model->db = $this->load->database('default', TRUE);
	        $this->site_model->db = $this->load->database('default', TRUE);
	        $trans++;
	        $ctr++;
	    }
	    echo "created ".$trans." orders";
	}
	public function add_payment2($sales_id=null,$amount=null,$type=null,$approval_code=null,$card_number=null,$send=false){
	    $this->load->model('dine/cashier_model');
	    $this->load->model('site/site_model');
	    $order = $this->get_order_header(false,$sales_id);
	    $error = "";
	    $payments = $this->get_order_payments(false,$sales_id);
	    $total_to_pay = $order['amount'];
	    $paid = $order['paid'];
	    $total_paid = 0;
	    $balance = $order['balance'];
	    if(count($payments) > 0){
	        foreach ($payments as $pay_id => $pay) {
	            $total_paid += $pay['amount'];
	        }
	    }
	    if($total_to_pay >= $total_paid)
	        $total_to_pay -= $total_paid;
	    else
	        $total_to_pay = 0;
	    $change = 0;
	    

	    $log_user = $this->session->userdata('user');
	    if($total_to_pay > 0){
	        $payment = array(
	            'sales_id'      =>  $sales_id,
	            'payment_type'  =>  $type,
	            'amount'        =>  $amount,
	            'to_pay'        =>  $total_to_pay,
	            "user_id"       =>  $log_user['id'],
	            // 'reference'     =>  null,
	            // 'card_type'     =>  null
	        );


	        if ($type=="credit") {
	            $payment['card_type'] = 'master';
	            $ma = $card_number;
	            for ($i=0,$x=strlen($ma); $i < $x-4; $i++) { $ma[$i] = "*"; }
	            $payment['card_number'] = $ma;
	            $payment['approval_code'] = $approval_code;
	        } elseif ($type=="debit") {
	            $payment['card_number'] = $card_number;
	            $payment['approval_code'] = $approval_code;
	        } elseif ($type=="smac") {
	            $payment['card_number'] = $card_number;
	        } elseif ($type=="eplus") {
	            $payment['card_number'] = $card_number;
	        } elseif ($type=="online") {
	            $payment['card_number'] = $card_number;
	        } elseif ($type=="gc") {
	            $this->load->model('dine/gift_cards_model');
	            $payment['reference'] = $card_number;
	            $payment['amount'] = $amount;
	        } elseif ($type=="coupon") {
	            $payment['reference'] = $card_number;
	            $payment['amount'] = $amount;
	        } elseif ($type=="chit") {
	            $payment['user_id'] = $this->input->post('manager_id');
	        }
	        $curr_shift_id = $order['shift_id'];
	        $time = $this->site_model->get_db_now();
	        $get_curr_shift = $this->clock_model->get_shift_id(date2Sql($time),$log_user['id']);
	        if(count($get_curr_shift) > 0){
	            $curr_shift_id = $get_curr_shift[0]->shift_id;
	        }
	        $payment_id = $this->cashier_model->add_trans_sales_payments($payment);
	        $new_total_paid = 0;
	        if($amount > $total_to_pay){
	            $new_total_paid = $order['amount'];
	            $balance = 0;
	        }
	        else{
	            $new_total_paid = $total_paid+$amount;
	            $balance = $balance - $amount;
	            // $balance = $total_to_pay - $amount;
	        }

	        // var_dump($payment);
	        $this->cashier_model->update_trans_sales(array('total_paid'=>$new_total_paid,'user_id'=>$log_user['id'],'shift_id'=>$curr_shift_id),$sales_id);
	        $log_user = $this->session->userdata('user');
	        $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Added Payment ".$amount." on Sales Order #".$sales_id,$sales_id);

	        if (num($balance) == 0) {
	        //     // if ($paid == 0) {
	                $this->finish_trans($sales_id,true);
	                $set = $this->cashier_model->get_pos_settings();
	                $no_prints = $set->no_of_receipt_print;
	                $order_slip_prints = $set->no_of_order_slip_print;
	                $approved_by = null;
	                if($type == 'chit'){
	                    $approved_by = $payment['user_id'];
	                    $app = $this->site_model->get_user_details($approved_by);
	                    $this->logs_model->add_logs('Sales Order',$app->id,$app->fname." ".$app->mname." ".$app->lname." ".$app->suffix." Approved CHIT Payment ".$amount." on Sales Order #".$sales_id,$sales_id);
	                }
	                $print_echo = $this->print_sales_receipt($sales_id,false,false,true,null,true,$no_prints,$order_slip_prints,$approved_by,false,true);
	                
	                if($send){
	                    if(MALL_ENABLED){
	                        if(MALL == 'megamall'){
	                            $this->sm_file($order['datetime']);
	                        }
	                    }
	                }

	        //     // }
	        }
	        // if($paid == 0){
	        //     $move = true;
	        // }
	        // else
	        //     $move = false;
	        // if(in_array($type, array([0]=>'cash'))){
	        if ($type == 'cash') {
	            if($amount > $total_to_pay){
	                $change = $amount - $total_to_pay;
	            }
	        }
	    }
	    else{
	        $error = 'Amount Received.';
	    }
	    // echo json_encode(array('error'=>$error,'change'=>$change,'tendered'=>$amount,'balance'=>num($balance) ));
	}
}